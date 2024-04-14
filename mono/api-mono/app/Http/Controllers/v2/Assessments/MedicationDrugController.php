<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Http\Requests\v2\Assessments\MedicalHistorySearchRequest;
use App\Http\Requests\v2\Assessments\MedicationDrugImportRequest;
use App\Http\Requests\v2\Assessments\MedicationDrugStoreRequest;
use App\Http\Requests\v2\Assessments\MedicationDrugUpdaterequest;
use App\Http\Resources\v2\Assessments\MedicationDrugResource;
use App\Http\Services\v2\Assessments\ValidatorService;
use App\Imports\v2\Assessments\MedicationDrugImport;
use App\Models\v2\Assessments\MedicationDrug;
use Maatwebsite\Excel\Facades\Excel;

class MedicationDrugController extends Controller
{
    public function __construct()
    {
        $this->validator = new ValidatorService;
    }

    public function index()
    {
        return MedicationDrugResource::collection(MedicationDrug::with('child')->where('parent_id', 0)->get());
    }

    public function store(MedicationDrugStoreRequest $request)
    {
        $this->validator->validateMedicationDrug($request);
        $medicationDrug = MedicationDrug::create($request->toArray());

        if (! $medicationDrug) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => 'Failed to create Medication drug',
                ],
            ], 500);
        }

        return response()->json([
            'data' => new MedicationDrugResource($medicationDrug),
            'message' => 'Medication drug created successfully',
            'success' => true,
        ], 200);
    }

    public function show($medicationDrugId)
    {
        $medicationDrug = MedicationDrug::find($medicationDrugId);

        if (! $medicationDrug) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medication drug with id {$medicationDrugId}",
                ],
            ], 404);
        }

        return response()->json([
            'data' => new MedicationDrugResource($medicationDrug),
        ]);
    }

    public function update(MedicationDrugUpdaterequest $request, $medicationDrugId)
    {
        $request->validate([
            'name' => ['required', 'string'],
        ]);

        $medicationDrug = MedicationDrug::where('id', $medicationDrugId)->first();

        if (! $medicationDrug) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medication drug with id {$medicationDrugId}",
                    'success' => false,
                ],
            ], 404);
        }

        if (! $medicationDrug->update($request->toArray())) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => 'Failed to update Medication drug',
                    'success' => false,
                ],
            ], 500);
        }

        return response()->json([
            'data' => new MedicationDrugResource($medicationDrug),
            'message' => 'Medication drug updated successfully',
            'success' => true,
        ]);
    }

    public function destroy($medicationDrugId)
    {
        $medicationDrug = MedicationDrug::find($medicationDrugId);

        if (! $medicationDrug) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medication drug with id {$medicationDrugId}",
                    'success' => false,
                ],
            ], 404);
        }

        if ($medicationDrug->child->count() > 0) {
            return response()->json([
                'error' => [
                    'code' => 403,
                    'message' => "Cannot perform delete operation, this id : {$medicationDrugId} has child",
                    'success' => false,
                ],
            ], 403);
        } else {
            $medicationDrug->delete();

            return response()->json([
                'data' => [],
                'message' => 'Medication drug deleted successfully',
                'success' => true,
            ]);
        }
    }

    public function search(MedicalHistorySearchRequest $request)
    {
        if ($request->has('query')) {
            $result = [];
            $query = $request->get('query');
            if (! is_null($query)) {
                $result = MedicationDrug::with('child')
                    ->where('medication_drugs.name', 'LIKE', '%' . $query . '%')
                    ->get();

                if (count($result)) {
                    return response()->json([
                        'data' => $result,
                        'message' => 'Data found',
                        'success' => true,
                    ]);
                } else {
                    return response()->json([
                        'error' => [
                            'code' => 404,
                            'message' => 'No Data found',
                            'success' => false,
                        ],
                    ], 404);
                }
            } else {
                return response()->json([
                    'error' => [
                        'code' => 404,
                        'message' => 'No Data found',
                        'success' => false,
                    ],
                ], 404);
            }
        } else {
            return response()->json([
                'error' => [
                    'code' => 400,
                    'message' => 'query key parameter is required',
                    'success' => false,
                ],
            ], 400);
        }
    }

    public function import(MedicationDrugImportRequest $request)
    {
        Excel::import(new MedicationDrugImport, request()->file('file'));

        return response()->json([
            'message' => 'Medication Drugs was Imported',
        ]);
    }
}
