<?php

namespace App\Http\Controllers\v2\Elders;

use App\Exports\v2\Elders\EldersExport;
use App\Exports\v2\Elders\EldersExportInvalidData;
use App\Exports\v2\Elders\EldersFormatExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\v2\Elders\AutocompleteElderRequest;
use App\Http\Requests\v2\Elders\BulkCreateEldersRequest;
use App\Http\Requests\v2\Elders\ElderEventManyResourceSetRequest;
use App\Http\Requests\v2\Elders\ElderEventResourceSetRequest;
use App\Http\Requests\v2\Elders\ElderImportRequest;
use App\Http\Requests\v2\Elders\ElderIndexRequest;
use App\Http\Requests\v2\Elders\ElderInvalidDataRequest;
use App\Http\Requests\v2\Elders\ElderRequest;
use App\Http\Requests\v2\Elders\PhoneNumberAvailabilityRequest;
use App\Http\Requests\v2\Elders\UpdateElderRequest;
use App\Http\Resources\v2\Elders\ElderAutocompleteCollection;
use App\Http\Resources\v2\Elders\ElderCallResource;
use App\Http\Resources\v2\Elders\ElderCasesCallResource;
use App\Http\Resources\v2\Elders\ElderCasesResource;
use App\Http\Resources\v2\Elders\ElderDetailResource;
use App\Http\Resources\v2\Elders\ElderResource;
use App\Http\Resources\v2\Elders\ElderSingleResource;
use App\Imports\v2\Elders\ElderBulkImport;
use App\Imports\v2\Elders\ElderImportData;
use App\Models\v2\Elders\Cases;
use App\Models\v2\Elders\Elder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use stdClass;

class ElderController extends Controller
{
    public function index(ElderIndexRequest $request)
    {
        $perPage = $request->query('per_page', 25);
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        $allowedFields = ['id', 'name', 'created_at', 'updated_at'];
        $sortByField = in_array($sortBy, $allowedFields) ? $sortBy : 'created_at';
        $sortDirection = $sortDir == 'asc' ? $sortDir : 'desc';

        $elders = Elder::when($request->query('name'), function ($query, $name) {
            $query->where('name', 'like', "%{$name}%");
        })->when($request->query('uid'), function ($query, $uid) {
            $query->where('uid', 'like', "{$uid}%");
        })->when($request->query('ids'), function ($query, $ids) {
            $idList = explode(',', $ids);
            $query->whereIn('id', $idList);
        })
            ->orderBy($sortByField, $sortDir)
            ->paginate($perPage);

        return ElderResource::collection($elders);
    }

    public function elderDetail()
    {

        return ElderDetailResource::collection(Elder::get());
    }

    public function elderCases()
    {

        return ElderCasesResource::collection(Elder::latest()->paginate(15));
    }

    public function elderList()
    {

        return ElderCasesCallResource::collection(Elder::latest()->paginate(15));
    }

    public function elderCalls()
    {

        return ElderCallResource::collection(Elder::latest()->paginate(15));
    }

    public function elderValidation(ElderRequest $request)
    {
        $function = new QueryController;
        $newElder = $function->elderValidation($request);

        return $newElder;
    }

    public function store(ElderRequest $request)
    {
        $function = new QueryController;
        $newElder = $function->createElder($request);

        return $newElder;
    }

    public function show($elderId)
    {
        $elder = Elder::findOrFail($elderId);

        return new ElderSingleResource($elder);
    }

    public function update(UpdateElderRequest $request, $elderId)
    {
        if ($request->access_role !== 'admin') {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'Unauthorized',
                    'errors' => [],
                ],
            ], 401);
        }

        $elder = Elder::findOrFail($elderId);

        if ($elder) {
            $existElder = Elder::where([
                'name' => $request->name,
                'birth_day' => $request->birth_day,
                'birth_month' => $request->birth_month,
                'birth_year' => $request->birth_year,
                'contact_number' => $request->contact_number,
                'gender' => $request->gender,
            ])->first();
            if (($existElder && $elder->id == $existElder->id) || $existElder == null) {
                $function = new QueryController;
                $new_elder = $function->updateElder($request, $elder);

                if ($request->has('uid_connected_with') && $new_elder) {
                    $uidConnectedWith = $request->uid_connected_with;
                    $relatedElder = Elder::where('uid', $uidConnectedWith)->first();
                    if ($relatedElder) {
                        $relatedElder->uid_connected_with = $elder->uid;
                        $relatedElder->save();
                    }
                }

                return response()->json([
                    'message' => 'Elder was updated',
                    // 'data' => new ElderSingleResource($new_elder)
                    'data' => $new_elder,
                ]);
            } else {
                return response()->json([
                    'message' => 'Elder not updated, data duplicate',
                    'data' => null,
                ]);
            }
        } else {
            return response()->json([
                'message' => 'Elder not updated',
                'data' => null,
            ]);
        }
    }

    public function destroy($elderId)
    {
        $elder = Elder::findOrFail($elderId);
        $function = new QueryController;
        $function->deleteElder($elder);

        return response()->json(null, 204);
    }

    public function import(ElderImportRequest $import)
    {
        Excel::import(new ElderImportData, request()->file('file'));

        return response()->json([
            'message' => 'Elders Was Imported',
        ]);
    }

    public function bulkValidation(ElderImportRequest $import)
    {
        $function = new QueryController;

        //validation header / column name
        $document_columns = (new HeadingRowImport)->toArray(request()->file('file'))[0][0];
        $elderRequest = new ElderRequest;
        $rule_columns = array_keys($elderRequest->rules());
        $columns_difference = array_diff($rule_columns, $document_columns);
        if ($columns_difference) {
            if (! in_array('uid_connected_with', $columns_difference) || ! in_array('related_uid', $document_columns) || count($columns_difference) > 1) {
                return response()->json([
                    'status' => [
                        'code' => 422,
                        'message' => 'Document template are invalid',
                        'errors' => [],
                    ],
                ], 422);
            }
        }

        //validation data
        $elders = Excel::toArray(new ElderBulkImport, request()->file('file'))[0];
        if (! $elders) {
            return response()->json([
                'status' => [
                    'code' => 422,
                    'message' => 'No data imported',
                    'errors' => [],
                ],
            ], 422);
        }

        $result = $function->validateElders($elders);

        return response()->json([
            'data' => $result,
        ]);
    }

    public function bulkCreate(BulkCreateEldersRequest $request)
    {
        $function = new QueryController;
        $elders = $request->elders;
        $result = $function->validateElders($elders, true);

        return response()->json([
            'data' => $result,
        ]);
    }

    public function exportEnrollmentTemplate()
    {
        $template_path = 'enrollment_template.xlsx';
        if (Storage::disk('local')->exists($template_path)) {
            return Storage::disk('local')->download($template_path);
        } else {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => 'Template file not found',
                    'errors' => [],
                ],
            ], 404);
        }
    }

    public function export(Request $request)
    {
        $this->authorize('export', $request->access_role);

        return Excel::download(new EldersExport, 'elders.csv');
    }

    public function exportInvalidData(ElderEventResourceSetRequest $request)
    {
        $invalid_datas = $request->all();

        $failed = $invalid_datas['failed'];

        $elder_request = new ElderInvalidDataRequest;
        for ($i = 0; $i < count($failed); $i++) {
            $validator = Validator::make($failed[$i], $elder_request->rules());

            if ($validator->fails()) {
                return response()->json([
                    'status' => [
                        'code' => 422,
                        'message' => 'Request cannot be process!',
                        'errors' => $validator->errors(),
                    ],
                ], 422);
            }
        }

        $now = date('d-m-Y_H-i-s');

        return Excel::download(new EldersExportInvalidData($failed), "elders_invalid_datas_{$now}.xlsx");
    }

    public function exportFormat()
    {
        return Excel::download(new EldersFormatExport, 'elders_format.xlsx');
    }

    public function autocomplete(AutocompleteElderRequest $request)
    {
        $perPage = $request->query('per_page', 25);
        $elders = Elder::query()
            ->select('id', 'name', 'uid')
            ->when($request->query('name'), function ($query, $name) {
                $query->where('name', 'like', "%{$name}%");
            })
            ->when($request->query('uid'), function ($query, $uid) {
                $query->where('name', 'like', "%{$uid}%")
                    ->orWhere('uid', 'like', "%{$uid}%");
            })
            ->when($request->query('ids'), function ($query, $ids) {
                $elderIds = explode(',', $ids);
                $query->whereIn('id', $elderIds);
            })
            //search is for name OR uid
            ->when($request->query('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('uid', 'like', "%{$search}%");
            })
            ->when($request->query('case_type'), function ($query, $case_type) {
                $query->whereHas('cases', function ($q) use ($case_type) {
                    $q->where('case_name', $case_type);
                });
            })
            ->paginate($perPage);

        return new ElderAutocompleteCollection($elders);
    }

    public function isPhoneNumberAvailable(PhoneNumberAvailabilityRequest $request)
    {
        $phoneNumber = $request->query('contact_number');
        $phoneNumberExists = Elder::where('contact_number', $phoneNumber)->exists();

        return response()->json([
            'data' => [
                'status' => ! $phoneNumberExists,
            ],
        ]);
    }

    public function elderEventResourceSet(ElderEventResourceSetRequest $request)
    {
        $elderId = null;
        $validated = Validator::make($request->all(), [
            'elder_id' => ['nullable', 'integer'],
        ]);
        if (! $validated->fails()) {
            $elderId = $request->query('elder_id');
        }
        if (! $elderId) {
            return response()->json([
                'data' => null,
            ], 404);
        }
        $elder = Elder::select([
            'id',
            'name',
            'uid',
            'case_type',
            'contact_number',
            'second_contact_number',
            'third_contact_number',
            'address',
            'elder_remark',
        ])->where('id', $elderId)->with([
            'cases' => function ($query) {
                $query->select(['id', 'elder_id'])->oldest()->first();
            },
        ])->first();
        if (! $elder) {
            return response()->json([
                'data' => null,
            ], 404);
        }
        $elder->case_id = (count($elder->cases) == 0) ? null : $elder->cases[0]->id;
        unset($elder->cases);

        return response()->json([
            'data' => $elder,
        ], 200);
    }

    public function elderEventManyResourceSet(ElderEventManyResourceSetRequest $request)
    {
        $elderIds = $request->query('elderIds') ? explode(',', $request->query('elderIds')) : null;
        if (! $elderIds) {
            return response()->json([
                'data' => null,
            ], 404);
        }
        $elders = Elder::select([
            'id',
            'name',
            'uid',
            'case_type',
            'contact_number',
            'second_contact_number',
            'third_contact_number',
            'address',
            'elder_remark',
        ])
            ->whereIn('id', $elderIds)
            ->get();
        if (count($elders) == 0) {
            return response()->json([
                'data' => null,
            ], 404);
        }
        $result = new stdClass;
        for ($i = 0; $i < count($elders); $i++) {
            $elderId = $elders[$i]->id;
            if (! property_exists($result, $elderId)) {
                $result->$elderId = $elders[$i];
            }
        }

        return response()->json([
            'data' => $result,
        ], 200);
    }

    public function getCasesStatus()
    {
        // Check if there is any on-going case status
        $cases = Cases::select('id', 'case_status')->get();
        if (count($cases) == 0) {
            return response()->json([
                'data' => null,
            ], 404);
        }

        $on_going_keys = ['On going', 'Baseline Completed', 'Enrolled - BZN', 'Enrolled - CGA', 'Enrolled-BZN', 'Enrolled-CGA'];
        $pending_keys = ['Pending', 'Pending for waiting 1st visit'];
        $finished_keys = ['Reject', 'Dropout', 'Completed'];
        $result = new stdClass;
        for ($i = 0; $i < count($cases); $i++) {
            $case_id = $cases[$i]['id'];
            if (! property_exists($result, $cases[$i]['id'])) {
                $result->$case_id['on_going'] = 0;
                $result->$case_id['pending'] = 0;
                $result->$case_id['finished'] = 0;
                if (in_array($cases[$i]['case_status'], $on_going_keys)) {
                    $result->$case_id['on_going'] = 1;
                } elseif (in_array($cases[$i]['case_status'], $pending_keys)) {
                    $result->$case_id['pending'] = 1;
                } elseif (in_array($cases[$i]['case_status'], $finished_keys)) {
                    $result->$case_id['finished'] = 1;
                }
            } elseif (property_exists($result, $cases[$i]['id'])) {
                if (in_array($cases[$i]['case_status'], $on_going_keys)) {
                    $result->$case_id['on_going'] = +1;
                } elseif (in_array($cases[$i]['case_status'], $pending_keys)) {
                    $result->$case_id['pending'] = +1;
                } elseif (in_array($cases[$i]['case_status'], $finished_keys)) {
                    $result->$case_id['finished'] = +1;
                }
            }
        }

        return response()->json(['data' => $result], 200);
    }

    public function destroyByUID($uid)
    {
        try {
            $elder = Elder::where('uid', $uid)->first();
            if (! $elder) {
                return response()->json([
                    'data' => null,
                    'message' => "Elder with UID: {$uid} does not exists",
                ], 404);
            }
            $elder->delete();

            return response()->json([
                'data' => null,
                'message' => "Success to delete elder with UID: {$uid}",
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'data' => null,
                'message' => "Failed to delete elder with UID: {$uid}",
            ], 409);
        }
    }
}
