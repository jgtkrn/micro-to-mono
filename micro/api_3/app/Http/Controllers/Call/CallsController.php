<?php

namespace App\Http\Controllers\Call;

use App\Models\ElderCalls;
use Illuminate\Http\Request;
use App\Exports\CallHistory\CallHistoryExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Calls\CallsRequest;
use App\Http\Resources\Calls\CallsResource;
use App\Http\Controllers\Query\QueryController;


class CallsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/elderly-api/v1/calls",
     *     tags={"calls"},
     *     summary="get calls",
     *     operationId="v1GetCallList",
     *     @OA\Parameter(
     *          in="query",
     *          name="page",
     *          example="1"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="per_page",
     *          example="25"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="sort_by",
     *          description="id|call_date|call_status|created_at|updated_at",
     *          example="created_at"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="sort_dir",
     *          description="ASC|DESC",
     *          example="desc"
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful"
     *     ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     ),
     * )
     */

    public function index(Request $request)
    {
        $allowedField = ['id', 'call_date', 'call_status', 'created_at', 'updated_at'];
        $by_name = $request->query('by_name') ? explode(',',$request->query('by_name')) : null;
        $sortField = $request->query('sort_by');
        $orderBy = in_array($sortField, $allowedField) ? $sortField : 'created_at';
        $orderDir = $request->query('sort_dir') == 'asc' ? 'ASC' : 'DESC';
        $perPage = $request->query('per_page', 25);
        $calls = ElderCalls::with(['case', 'case.elder'])
            ->when($by_name, function($query, $name) {
                $query->whereIn('updated_by_name', $name);
            })
            ->orderBy($orderBy, $orderDir)
            ->paginate($perPage);
        return CallsResource::collection($calls);
    }

    /**
     * @OA\Post(
     *     path="/elderly-api/v1/calls",
     *     tags={"calls"},
     *     summary="Create new call",
     *     operationId="v1CreateCall",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Call was created"),
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Calls")
     *         )
     *     ),
     *      @OA\Response(
     *         response=422,
     *         description="Bad Request"
     *     ),
     *     @OA\RequestBody(
     *         description="Input required call information (in string)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"cases_id,caller_name"},
     *                 @OA\Property(property="caller_id", type="integer", example="1", description="caller id"),
     *                 @OA\Property(property="cases_id", type="integer", example="1", description="Cases id"),
     *                 @OA\Property(property="call_date", type="date", example="2019-06-09", description="call start"),
     *                 @OA\Property(property="call_status", type="string", example="pending", description="caller name"),
     *             )
     *     )
     * )
     */


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

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/calls/{id}",
     *     operationId="v1GetCallsDetail",
     *     summary="get call detail use ID call",
     *     tags={"calls"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="id",
     *          description="The id of the call",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Call detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Calls")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find call with id {id}")
     *              )
     *          )
     *     )
     * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function show($callId)
    {
        $call = ElderCalls::findOrFail($callId);
        return new CallsResource($call);
    }

    /**
     * @OA\Put(
     *     path="/elderly-api/v1/calls/{id}",
     *     tags={"calls"},
     *     summary="Update call by Id",
     *     operationId="callsUpdate",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Call Id to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Calls")
     *         )
     *     ),
     *      @OA\Response(
     *         response=422,
     *         description="Bad Request"
     *     ),
     *     @OA\RequestBody(
     *         description="Input required call information (in string)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"cases_id,caller_name"},
     *                 @OA\Property(property="caller_id", type="integer", example="1", description="caller id"),
     *                 @OA\Property(property="call_start", type="datetime", example="2019-06-09 17:00", description="call start"),
     *                 @OA\Property(property="call_end", type="datetime", example="2019-8-09 17:20", description="call end"),
     *                 @OA\Property(property="call_status", type="string", example="pending", description="caller name"),
     *                 @OA\Property(property="remarks", type="string", example="admin", description="remarks"),
     *                 @OA\Property(property="cases_id", type="integer", example="1", description="Cases id"),
     *                 @OA\Property(property="created_by", type="string", example="user abc", description="created by"),
     *                 @OA\Property(property="updated_by", type="string", example="user abc", description="updated by"),
     *             )
     *     )
     * )
     */

    public function update(CallsRequest $request, $callId)
    {
        $request->merge([
            'updated_by' => $request->user_id,
            'updated_by_name' => $request->user_name,
        ]);

        $call = ElderCalls::findOrFail($callId);
        $function = new QueryController();
        $request['call_date'] = $function->dateConvertion($request->call_date);
        $call->update($request->toArray());
        return response()->json([
            'message' => 'Call was updated',
            'data' => new CallsResource($call),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/elderly-api/v1/calls/{id}",
     *     tags={"calls"},
     *     summary="Delete call by Id",
     *     operationId="v1DeleteCall",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="call Id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful"
     *     ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */

    public function destroy($callId)
    {
        $call = ElderCalls::findOrFail($callId);
        $call->delete();
        return response()->json(null, 204);
    }

    public function staff_calls(Request $request){
        $by_name = $request->query('by_name') ? explode(',',$request->query('by_name')) : null;

        $calls = ElderCalls::select(['id', 'updated_by_name'])
            ->when($by_name, function($query, $name) {
                $query->whereIn('updated_by_name', $name);
            })->get();
        $results = new \stdClass();
        for($i = 0; $i<count($calls); $i++){
            $staff_name = $calls[$i]['updated_by_name'];
            if($staff_name !== null){
                $swipespace = preg_replace('/[^A-Za-z0-9]+/', '_', $staff_name);
                $snakeCaseStaffName = strtolower($swipespace);
                $snakeCaseStaffName = trim($snakeCaseStaffName, '_');
                if(!property_exists($results, $snakeCaseStaffName)){
                    $results->$snakeCaseStaffName = 1;
                } else if(property_exists($results, $snakeCaseStaffName)){
                    $results->$snakeCaseStaffName += 1;
                }
            }
        }

        return response()->json(['data' => $results], 200);
    }

    public function exportCallHistory(Request $request){
        $result = $this->index($request);
        if(!$result) {
            return response()->json([
                'data' => [],
                'message' => 'No data loaded.'
            ], 404);
        }
        $result_collection_new = $result->collect('data');
        if(!$result_collection_new) {
            return response()->json([
                'data' => [],
                'message' => 'No data loaded.'
            ], 404);
        }
        $result_collection = clone $result_collection_new;
        $new_array = [];
        foreach ($result_collection as $items){
            $item = new \stdClass();
            $item->caller_id = $items->caller_id;
            $item->call_date = $items->call_date;
            $item->call_status = $items->call_status;
            $item->remark = $items->remark;;
            array_push($new_array, $item);
        }
        return Excel::download(new CallHistoryExport($new_array), 'call-history.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function elder_calls(){
        $calls = ElderCalls::with('case')->get();
        if(count($calls) == 0){
            return response()->json(['data' => null], 200);
        }
        $elder_name = $calls->pluck('case.elder.name');

        $results = new \stdClass();
        for ($i = 0; $i < count($elder_name); $i++){
            // $elder_name = $calls[$i]['case']['elder.name'];

            $swipespace = preg_replace('/[^A-Za-z0-9]+/', '_', $elder_name[$i]);
            $snakeCaseElderName = strtolower($swipespace);
            $snakeCaseElderName = trim($snakeCaseElderName, '_');
            if(!property_exists($results, $snakeCaseElderName)){
                $results->$snakeCaseElderName = 1;
            } else if(property_exists($results, $snakeCaseElderName)){
                $results->$snakeCaseElderName += 1;
            }
        }
        return response()->json(['data' => $results], 200);
    }
}
