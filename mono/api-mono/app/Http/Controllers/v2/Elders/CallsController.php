<?php

namespace App\Http\Controllers\v2\Elders;

use App\Exports\v2\Elders\CallHistoryExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\v2\Elders\CallsIndexRequest;
use App\Http\Requests\v2\Elders\CallsRequest;
use App\Http\Requests\v2\Elders\ExportCallHistoryRequest;
use App\Http\Requests\v2\Elders\StaffCallsRequest;
use App\Http\Resources\v2\Elders\CallsResource;
use App\Models\v2\Elders\Cases;
use App\Models\v2\Elders\ElderCalls;
use Carbon\Carbon;
use Maatwebsite\Excel\Excel as MaatExcel;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class CallsController extends Controller
{
    public function index(CallsIndexRequest $request)
    {
        $by_name = $request->query('by_name') ? explode(',', $request->query('by_name')) : null;
        $sortField = $request->query('sort_by');
        $orderBy = $sortField ?? 'created_at';
        $orderDir = $request->query('sort_dir') == 'asc' ? 'ASC' : 'DESC';
        $perPage = $request->query('per_page', 25);
        $calls = ElderCalls::with(['case', 'case.elder'])
            ->when($by_name, function ($query, $name) {
                $query->whereIn('updated_by_name', $name);
            })
            ->orderBy($orderBy, $orderDir)
            ->paginate($perPage);

        return CallsResource::collection($calls);
    }

    public function store(CallsRequest $request)
    {
        $request->merge([
            'created_by' => $request->user_id,
            'updated_by' => $request->user_id,
            'created_by_name' => $request->user_name,
            'updated_by_name' => $request->user_name,
        ]);
        $validated = $request->toArray();
        $call = ElderCalls::create($validated);

        return response()->json([
            'message' => 'Call was created',
            'data' => new CallsResource($call),
        ], 201);
    }

    public function show($callId)
    {
        $call = ElderCalls::findOrFail($callId);

        return new CallsResource($call);
    }

    public function update(CallsRequest $request, $callId)
    {
        $request->merge([
            'updated_by' => $request->user_id,
            'updated_by_name' => $request->user_name,
        ]);

        $call = ElderCalls::findOrFail($callId);
        $function = new QueryController;
        $request['call_date'] = $function->dateConvertion($request->call_date);
        $call->update($request->toArray());

        return response()->json([
            'message' => 'Call was updated',
            'data' => new CallsResource($call),
        ]);
    }

    public function destroy($callId)
    {
        $call = ElderCalls::findOrFail($callId);
        $call->delete();

        return response()->json(null, 204);
    }

    public function staffCalls(StaffCallsRequest $request)
    {
        $by_name = $request->query('by_name') ? explode(',', $request->query('by_name')) : null;

        $calls = ElderCalls::select(['id', 'updated_by_name'])
            ->when($by_name, function ($query, $name) {
                $query->whereIn('updated_by_name', $name);
            })->get();
        $results = new stdClass;
        for ($i = 0; $i < count($calls); $i++) {
            $staff_name = $calls[$i]['updated_by_name'];
            if ($staff_name !== null) {
                $swipespace = preg_replace('/[^A-Za-z0-9]+/', '_', $staff_name);
                $snakeCaseStaffName = strtolower($swipespace);
                $snakeCaseStaffName = trim($snakeCaseStaffName, '_');
                if (! property_exists($results, $snakeCaseStaffName)) {
                    $results->$snakeCaseStaffName = 1;
                } elseif (property_exists($results, $snakeCaseStaffName)) {
                    $results->$snakeCaseStaffName += 1;
                }
            }
        }

        return response()->json(['data' => $results], 200);
    }

    public function elderCalls($from = null, $to = null)
    {
        $calls = ElderCalls::select('*');
        if ($from && $to) {
            $calls = $calls->whereBetween('call_date', [$from, $to]);
        }
        $calls = $calls->with('case')->get();
        if (count($calls) == 0) {
            return null;
        }
        $elder_name = $calls->pluck('case.elder.name');

        $results = new stdClass;
        for ($i = 0; $i < count($elder_name); $i++) {
            $swipespace = preg_replace('/[^A-Za-z0-9]+/', '_', $elder_name[$i]);
            $snakeCaseElderName = strtolower($swipespace);
            $snakeCaseElderName = trim($snakeCaseElderName, '_');
            if (! property_exists($results, $snakeCaseElderName)) {
                $results->$snakeCaseElderName = 1;
            } elseif (property_exists($results, $snakeCaseElderName)) {
                $results->$snakeCaseElderName += 1;
            }
        }

        return $results;
    }

    public function exportCallHistory(ExportCallHistoryRequest $request)
    {
        $call_status_map = [
            'success | interested',
            'pending | no one answer',
            'pending | to schedule assessment',
            'fail | wrong number',
            'fail | nursery home / deceased / travel',
            'fail | refused to join',
            'to follow-up',
            'other',
        ];
        $call_status_map_length = count($call_status_map) - 1;
        $callsCount = ElderCalls::count() ?? 0;
        $by_name = $request->query('by_name') ? explode(',', $request->query('by_name')) : null;
        $sortField = $request->query('sort_by');
        $orderBy = $sortField ?? 'created_at';
        $orderDir = $request->query('sort_dir') == 'asc' ? 'ASC' : 'DESC';
        $page = $request->query('page') > 0 ? $request->query('page') - 1 : 0;
        $take = $request->query('size') ?? $callsCount;
        $skip = $page * $take;

        $calls = ElderCalls::select([
            'cases_id',
            'call_date',
            'call_status',
            'remark',
            'created_by_name',
        ])
            ->when($by_name, function ($query, $name) {
                $query->whereIn('created_by_name', $name);
            });

        if ($request->query('from') && $request->query('to')) {
            $from = Carbon::parse($request->query('from'))->startOfDay();
            $to = Carbon::parse($request->query('to'))->endOfDay();
            $calls = $calls->whereBetween('call_date', [$from, $to]);
        }
        $calls = $calls
            ->orderBy($orderBy, $orderDir)
            ->skip($skip)
            ->take($take)
            ->get()
            ->toArray();
        $casesUid = $this->getUidSetByCasesId();
        for ($i = 0; $i < count($calls); $i++) {
            $call_status_mapping = array_search(strtolower($calls[$i]['call_status']), $call_status_map);

            $callCases = $calls[$i]['cases_id'];
            $calls[$i]['uid'] = $casesUid ? $casesUid[$callCases]['uid'] : null;
            $calls[$i]['status_other'] = ! $call_status_mapping || $call_status_mapping == $call_status_map_length ? $calls[$i]['call_status'] : null;
            $calls[$i]['call_status'] = ! $call_status_mapping ? $call_status_map_length + 1 : $call_status_mapping + 1;
        }

        return Excel::download(new CallHistoryExport($calls), 'call-history.csv', MaatExcel::CSV);
    }

    public function getUidSetByCasesId()
    {
        $cases = Cases::select('id', 'elder_id')->with('elder')->get();
        if (count($cases) == 0) {
            return null;
        }
        $result = new stdClass;
        for ($i = 0; $i < count($cases); $i++) {
            $uid = $cases[$i]['elder'] ? $cases[$i]['elder']['uid'] : null;
            $casesId = $cases[$i]->id;
            if (! property_exists($result, $casesId && $uid !== null)) {
                $result->$casesId = ['uid' => $uid];
            }
        }

        return (array) $result;
    }
}
