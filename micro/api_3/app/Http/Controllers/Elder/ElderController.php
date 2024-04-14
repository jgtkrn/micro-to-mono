<?php

namespace App\Http\Controllers\Elder;

use App\Models\Cases;
use App\Models\Elder;
use Illuminate\Http\Request;
use App\Exports\Elder\EldersExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Elder\ElderBulkImport;
use App\Imports\Elder\ElderImportData;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\HeadingRowImport;
use App\Exports\Elder\EldersFormatExport;
use App\Http\Requests\Elder\ElderRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Elder\ElderResource;
use App\Exports\Elder\EldersExportInvalidData;
use App\Http\Controllers\Query\QueryController;
use App\Http\Requests\Elder\ElderImportRequest;
use App\Http\Resources\Elder\ElderCallResource;
use App\Http\Resources\Elder\ElderCasesResource;
use App\Http\Resources\Elder\ElderDetailResource;
use App\Http\Resources\Elder\ElderSingleResource;
use App\Http\Resources\ElderAutocompleteCollection;
use App\Http\Requests\Elder\ElderInvalidDataRequest;
use App\Http\Resources\Elder\ElderCasesCallResource;

class ElderController extends Controller
{

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/elders",
     *     tags={"elders"},
     *     summary="get elders no raw",
     *     operationId="getElders",
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
     *          name="name",
     *          example="Steven"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="uid",
     *          example="UID0001"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="ids",
     *          example="1,2,3,4,5"
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
        $perPage = $request->query('per_page', 25);
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        $allowedFields = ['id', 'name', 'created_at', 'updated_at'];
        $sortByField = in_array($sortBy, $allowedFields) ? $sortBy : 'created_at';
        $sortDirection = $sortDir == 'asc' ? $sortDir : 'desc';

        $elders = Elder::when($request->query('name'), function ($query, $name) {
            $query->where('name', 'like', "%$name%");
        })->when($request->query('uid'), function ($query, $uid) {
            $query->where('uid', 'like', "$uid%");
        })->when($request->query('ids'), function ($query, $ids) {
            $idList = explode(',', $ids);
            $query->whereIn('id', $idList);
        })
            ->orderBy($sortByField, $sortDir)
            ->paginate($perPage);

        return ElderResource::collection($elders);
    }

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/elders-backend-detail",
     *     tags={"elders-backend"},
     *     summary="get elders detail for appointment backend",
     *     operationId="getEldersDetailBackEnd",
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

    public function elderDetail()
    {

        return ElderDetailResource::collection(Elder::get());
    }

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/elders-cases",
     *     tags={"elders"},
     *     summary="get elders with cases",
     *     operationId="getEldersCases",
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

    public function elderCases()
    {

        return ElderCasesResource::collection(Elder::latest()->paginate(15));
    }

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/elders-list",
     *     tags={"elders"},
     *     summary="get elders with cases & calls",
     *     operationId="getEldersCaseCalls",
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

    public function elderList()
    {

        return ElderCasesCallResource::collection(Elder::latest()->paginate(15));
    }

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/elders-calls",
     *     tags={"elders"},
     *     summary="get elders with list of calls",
     *     operationId="getEldersCalls",
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

    public function elderCalls()
    {

        return ElderCallResource::collection(Elder::latest()->paginate(15));
    }



    /**
     * @OA\Post(
     *     path="/elderly-api/v1/elders",
     *     operationId="v1CreateElder",
     *     tags={"elders"},
     *     @OA\RequestBody(
     *           description="Input required elder information (in string)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"name, case_type, district, zone, source_of_referral, address, gender, bod"},
     *                 @OA\Property(property="name", type="string", example="Toph Bei Fong", description="name for elder"),
     *                 @OA\Property(property="name_en", type="string", example="Sung Kang", description="name for elder"),
     *                 @OA\Property(property="gender", type="enum",format="male,female", example="female", description="Gender"),
     *                 @OA\Property(property="contact_number", type="string", example="85212345678", description="HK phone number"),
     *                 @OA\Property(property="second_contact_number", type="string", example="85212345678", description="HK phone number"),
     *                 @OA\Property(property="third_contact_number", type="string", example="85212345678", description="HK phone number"),
     *                 @OA\Property(property="address", type="string", example="Bei Fong Estate, Earh Kingdom", description="Address"),
     *                 @OA\Property(property="birth_day", type="integer", example="1", description="Birth day (optional)"),
     *                 @OA\Property(property="birth_month", type="integer", example="12", description="Birth month"),
     *                 @OA\Property(property="birth_year", type="integer", example="1992", description="Birth year"),
     *                 @OA\Property(property="district", type="string", example="Kowloon Bay", description="district name"),
     *                 @OA\Property(property="zone", type="string", example="Qince Village", description="zone name"),
     *                 @OA\Property(property="language", type="string", example="Cantonese", description="language"),
     *                 @OA\Property(property="centre_case_id", type="string", example="bb0001", description="language"),
     *                 @OA\Property(property="centre_responsible_worker", type="string", example="Baizu", description="language"),
     *                 @OA\Property(property="responsible_worker_contact", type="string", example="081234567890", description="language"),
     *                 @OA\Property(property="case_type", type="enum", example="CGA", description="case type"),
     *                 @OA\Property(property="source_of_referral", type="string", example="Bubu Pharmacy", description="source of referral"),
     *                 @OA\Property(property="relationship", type="string", example="Spouse", description="Relationship"),
     *                 @OA\Property(property="uid_connected_with", type="string", example="WP0005", description="UID connected with"),
     *                 @OA\Property(property="emergency_contact_number", type="string", example="081234567891", description="HK phone number for emergency contact"),
     *                 @OA\Property(property="emergency_contact_number_2", type="string", example="081234567892", description="HK phone number for emergency contact"),
     *                 @OA\Property(property="emergency_contact_name", type="string", example="Suyin Bei fong", description="emergency contact name"),
     *                 @OA\Property(property="emergency_contact_relationship_other", type="string", example="Child", description="emergency relationship contact other"),
     *                 @OA\Property(property="emergency_contact_2_number", type="string", example="081234567893", description="HK phone number for emergency contact"),
     *                 @OA\Property(property="emergency_contact_2_number_2", type="string", example="081234567894", description="HK phone number for emergency contact"),
     *                 @OA\Property(property="emergency_contact_2_name", type="string", example="Lin Bei Fong", description="emergency contact name"),
     *                 @OA\Property(property="emergency_contact_2_relationship_other", type="string", example="Child", description="emergency relationship contact other"),
     *             )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Elder created",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Elder")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Elder validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="The name field is required.")
     *          )
     *     ),
     *
     *     )
     * )
     *
     * @param  \App\Http\Requests\CreateUserRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function elderValidation(ElderRequest $request){
        $function = new QueryController();
        $newElder = $function->elderValidation($request);

        return $newElder;
    }
    
    public function store(ElderRequest $request)
    {
        $function = new QueryController();
        $newElder = $function->createElder($request);

        return $newElder;
    }

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/elders/{id}",
     *     operationId="GetEldersDetail",
     *     summary="get elders detail use ID elders",
     *     tags={"elders"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="id",
     *          description="The id of the elder",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Elder detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Elder")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Elder not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find elder with id {id}")
     *              )
     *          )
     *     )
     * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */


    public function show($elderId)
    {
        $elder = Elder::findOrFail($elderId);

        return new ElderSingleResource($elder);
    }


    /**
     * @OA\Put(
     *     path="/elderly-api/v1/elders/{id}",
     *     tags={"elders"},
     *     summary="Update elder by Id",
     *     operationId="elderUpdate",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Elder Id to be updated",
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
     *     @OA\RequestBody(
     *         description="Input required elder information (in string)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"name, case_type, district, address, gender, bod"},
     *                 @OA\Property(property="name", type="string", example="Toph Bei Fong", description="name for elder"),
     *                 @OA\Property(property="name_en", type="string", example="Sung Kang", description="name for elder"),
     *                 @OA\Property(property="gender", type="enum",format="male,female", example="female", description="Gender"),
     *                 @OA\Property(property="contact_number", type="string", example="85212345678", description="HK phone number"),
     *                 @OA\Property(property="second_contact_number", type="string", example="85212345678", description="HK phone number"),
     *                 @OA\Property(property="third_contact_number", type="string", example="85212345678", description="HK phone number"),
     *                 @OA\Property(property="address", type="string", example="Bei Fong Estate, Earh Kingdom", description="Address"),
     *                 @OA\Property(property="birth_day", type="integer", example="1", description="Birth day (optional)"),
     *                 @OA\Property(property="birth_month", type="integer", example="12", description="Birth month"),
     *                 @OA\Property(property="birth_year", type="integer", example="1992", description="Birth year"),
     *                 @OA\Property(property="district", type="string", example="Kowloon Bay", description="district name"),
     *                 @OA\Property(property="zone", type="string", example="Qince Village", description="zone name"),
     *                 @OA\Property(property="language", type="string", example="Cantonese", description="language"),
     *                 @OA\Property(property="centre_case_id", type="string", example="bb0001", description="language"),
     *                 @OA\Property(property="centre_responsible_worker", type="string", example="Baizu", description="language"),
     *                 @OA\Property(property="responsible_worker_contact", type="string", example="081234567890", description="language"),
     *                 @OA\Property(property="relationship", type="string", example="Spouse", description="Relationship"),
     *                 @OA\Property(property="uid_connected_with", type="string", example="WP0005", description="UID connected with"),
     *                 @OA\Property(property="emergency_contact_number", type="string", example="081234567891", description="HK phone number for emergency contact"),
     *                 @OA\Property(property="emergency_contact_number_2", type="string", example="081234567892", description="HK phone number for emergency contact"),
     *                 @OA\Property(property="emergency_contact_name", type="string", example="Suyin Bei fong", description="emergency contact name"),
     *                 @OA\Property(property="emergency_contact_relationship_other", type="string", example="Child", description="emergency relationship contact other"),
     *                 @OA\Property(property="emergency_contact_2_number", type="string", example="081234567893", description="HK phone number for emergency contact"),
     *                 @OA\Property(property="emergency_contact_2_number_2", type="string", example="081234567894", description="HK phone number for emergency contact"),
     *                 @OA\Property(property="emergency_contact_2_name", type="string", example="Lin Bei Fong", description="emergency contact name"),
     *                 @OA\Property(property="emergency_contact_2_relationship_other", type="string", example="Child", description="emergency relationship contact other"),
     *             )
     *     )
     * )
     */

    public function update(Request $request, $elderId)
    {
        $elder = Elder::where('id', $elderId)->first();
        
        if ($elder) {
            $existElder = Elder::where([
                'name' => $request->name,
                'birth_day' => $request->birth_day,
                'birth_month' => $request->birth_month,
                'birth_year' => $request->birth_year,
                'contact_number' => $request->contact_number,
                'gender' => $request->gender
            ])->first();
            if(($existElder && $elder->id == $existElder->id) || $existElder == null){
                $function = new QueryController();
                $new_elder = $function->updateElder($request, $elder);
                return response()->json([
                    'message' => 'Elder was updated',
                    // 'data' => new ElderSingleResource($new_elder)
                    'data' => $new_elder
                ]);
            } else {
                return response()->json([
                    'message' => 'Elder not updated, data duplicate',
                    'data' => null
                ]);
            }
        } else {
            return response()->json([
                'message' => 'Elder not updated',
                'data' => null
            ]);
        }
    }

    /**
     * @OA\Delete(
     *     path="/elderly-api/v1/elders/{id}",
     *     tags={"elders"},
     *     summary="Delete elder by Id",
     *     operationId="elderDelete",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Elder Id",
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

    public function destroy($elderId)
    {
        $elder = Elder::findOrFail($elderId);
        $function = new QueryController();
        $function->deleteElder($elder);
        return response()->json(null, 204);
    }

    /**
     * @OA\Post(
     *     path="/elderly-api/v1/elders-import",
     *     tags={"elders"},
     *     summary="File upload",
     *     operationId="importElders",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Elder")
     *         )
     *     ),
     *      @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="errors", type="object", description="Invalid request object"),
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input file",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="Single file",
     *                     property="file",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function import(ElderImportRequest $import)
    {
        Excel::import(new ElderImportData, request()->file('file'));
        return response()->json([
            'message' => 'Elders Was Imported',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/elderly-api/v1/elders-bulk-validation",
     *     tags={"elders"},
     *     summary="Elders Bulk Validation",
     *     operationId="ElderBulkValidation",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Elder")
     *         )
     *     ),
     *      @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="errors", type="object", description="Invalid request object"),
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input file",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="Single file",
     *                     property="file",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function bulkValidation(ElderImportRequest $import)
    {
        $function = new QueryController();

        //validation header / column name
        $document_columns = (new HeadingRowImport)->toArray(request()->file('file'))[0][0];
        $elderRequest = new ElderRequest;
        $rule_columns = array_keys($elderRequest->rules());
        $columns_difference = array_diff($rule_columns, $document_columns);
        if ($columns_difference) {
            if (!in_array('uid_connected_with', $columns_difference) || !in_array('related_uid', $document_columns) || count($columns_difference) > 1) {
                return response()->json([
                    'status' => [
                        'code' => 422,
                        'message' => "Document template are invalid",
                        'errors' => [],
                    ]
                ], 422);
            }
        }

        //validation data
        $elders = Excel::toArray(new ElderBulkImport, request()->file('file'))[0];
        if (!$elders) {
            return response()->json([
                'status' => [
                    'code' => 422,
                    'message' => "No data imported",
                    'errors' => [],
                ]
            ], 422);
        }

        $result = $function->validateElders($elders);

        return response()->json([
            'data' => $result
        ]);
    }

    /**
     * @OA\Post(
     *     path="/elderly-api/v1/elders-bulk-create",
     *     tags={"elders"},
     *     summary="Elders Bulk Create",
     *     operationId="ElderBulkCreate",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Elder")
     *         )
     *     ),
     *      @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="errors", type="object", description="Invalid request object"),
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input file",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="Single file",
     *                     property="file",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function bulkCreate(Request $request)
    {
        $function = new QueryController();
        $elders = $request->elders;
        $result = $function->validateElders($elders, true);

        return response()->json([
            'data' => $result
        ]);
    }

    public function exportEnrollmentTemplate()
    {
        $template_path = env('BULK_ENROLLMENT_TEMPLATE', 'enrollment_template.xlsx');
        if (Storage::disk('local')->exists($template_path)) {
            return Storage::disk('local')->download($template_path);
        } else {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Template file not found",
                    'errors' => [],
                ]
            ], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/elders-export",
     *     tags={"elders"},
     *     summary="export elders to csv",
     *     operationId="exportElders",
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
    public function export(Request $request)
    {
        $this->authorize('export', $request->access_role);
        return Excel::download(new EldersExport, 'elders.csv');
    }

    /**
     * @OA\Post(
     *     path="/elderly-api/v1/elders-export-invalid-data",
     *     tags={"elders"},
     *     operationId="jsonListToXlsx",
     *     summary="Convert List of Json to Xlsx File",
     *     @OA\RequestBody(
     *         required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                 property="failed",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="name", type="string", example="唐子敏", description="Name"),
     *                     @OA\Property(property="name_en", type="string", example="Testing", description="Name English"),
     *                     @OA\Property(property="gender", type="enum", format="M, F", example="M", description="Gender"),
     *                     @OA\Property(property="birth_day", type="integer", example=17, description="Birth Day"),
     *                     @OA\Property(property="birth_month", type="integer", example=10, description="Birth Month"),
     *                     @OA\Property(property="birth_year", type="integer", example=2021, description="Birth Year"),
     *                     @OA\Property(property="contact_number", type="string", example="96278599", description="Contact Number"),
     *                     @OA\Property(property="second_contact_number", type="string", example="26402929(PW:2368)", description="Second Contact Number"),
     *                     @OA\Property(property="third_contact_number", type="string", example="", description="Third Contact Number"),
     *                     @OA\Property(property="address", type="string", example="彩福邨彩喜樓2410室", description="Address"),
     *                     @OA\Property(property="district", type="string", example="觀塘", description="District"),
     *                     @OA\Property(property="zone", type="string", example="彩福邨", description="Zone"),
     *                     @OA\Property(property="language", type="string", example="", description="Language"),
     *                     @OA\Property(property="centre_case_id", type="string", example="91-03837", description="Centre Case ID"),
     *                     @OA\Property(property="centre_responsible_worker", type="string", example="", description="Center Responsible Worker"),
     *                     @OA\Property(property="responsible_worker_contact", type="string", example="1", description="Responsible Worker Contact"),
     *                     @OA\Property(property="related_uid", type="string", example="", description="Related UID"),
     *                     @OA\Property(property="relationship", type="string", example="", description="Relationship"),
     *                     @OA\Property(property="case_type", type="enum", format="CGA,BZN", example="CGA", description="Case Type"),
     *                     @OA\Property(property="source_of_referral", type="string", example="香港基督教服務處 - 樂暉長者地區中心", description="Source Of Referral"),
     *                     @OA\Property(property="emergency_contact_name", type="string", example="何小姐", description="Emergency Contact Name"),
     *                     @OA\Property(property="emergency_contact_number", type="string", example="24583382", description="Emergency Contact Number"),
     *                     @OA\Property(property="emergency_contact_number_2", type="string", example="", description="Emergency Contact Number 2"),
     *                     @OA\Property(property="emergency_contact_relationship_other", type="string", example="", description="Emergency Contact Relationship Other"),
     *                     @OA\Property(property="emergency_contact_2_name", type="string", example="", description="Emergency Contact 2 Name"),
     *                     @OA\Property(property="emergency_contact_2_number", type="string", example="", description="Emergency Contact 2 Number"),
     *                     @OA\Property(property="emergency_contact_2_number_2", type="string", example="", description="Emergency Contact 2 Number 2"),
     *                     @OA\Property(property="emergency_contact_2_relationship_other", type="string", example="", description="Emergency Contact 2 Relationship Other"),
     *                 ),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Export The Invalid Datas successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                 property="failed",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="name", type="string", example="唐子敏", description="Name"),
     *                     @OA\Property(property="name_en", type="string", example="Testing", description="Name English"),
     *                     @OA\Property(property="gender", type="enum", format="M, F", example="M", description="Gender"),
     *                     @OA\Property(property="birth_day", type="integer", example=17, description="Birth Day"),
     *                     @OA\Property(property="birth_month", type="integer", example=10, description="Birth Month"),
     *                     @OA\Property(property="birth_year", type="integer", example=2021, description="Birth Year"),
     *                     @OA\Property(property="contact_number", type="string", example="96278599", description="Contact Number"),
     *                     @OA\Property(property="second_contact_number", type="string", example="26402929(PW:2368)", description="Second Contact Number"),
     *                     @OA\Property(property="third_contact_number", type="string", example="", description="Third Contact Number"),
     *                     @OA\Property(property="address", type="string", example="彩福邨彩喜樓2410室", description="Address"),
     *                     @OA\Property(property="district", type="string", example="觀塘", description="District"),
     *                     @OA\Property(property="zone", type="string", example="彩福邨", description="Zone"),
     *                     @OA\Property(property="language", type="string", example="", description="Language"),
     *                     @OA\Property(property="centre_case_id", type="string", example="91-03837", description="Centre Case ID"),
     *                     @OA\Property(property="centre_responsible_worker", type="string", example="", description="Center Responsible Worker"),
     *                     @OA\Property(property="responsible_worker_contact", type="string", example="1", description="Responsible Worker Contact"),
     *                     @OA\Property(property="related_uid", type="string", example="", description="Related UID"),
     *                     @OA\Property(property="relationship", type="string", example="", description="Relationship"),
     *                     @OA\Property(property="case_type", type="enum", format="CGA,BZN", example="CGA", description="Case Type"),
     *                     @OA\Property(property="source_of_referral", type="string", example="香港基督教服務處 - 樂暉長者地區中心", description="Source Of Referral"),
     *                     @OA\Property(property="emergency_contact_name", type="string", example="何小姐", description="Emergency Contact Name"),
     *                     @OA\Property(property="emergency_contact_number", type="string", example="24583382", description="Emergency Contact Number"),
     *                     @OA\Property(property="emergency_contact_number_2", type="string", example="", description="Emergency Contact Number 2"),
     *                     @OA\Property(property="emergency_contact_relationship_other", type="string", example="", description="Emergency Contact Relationship Other"),
     *                     @OA\Property(property="emergency_contact_2_name", type="string", example="", description="Emergency Contact 2 Name"),
     *                     @OA\Property(property="emergency_contact_2_number", type="string", example="", description="Emergency Contact 2 Number"),
     *                     @OA\Property(property="emergency_contact_2_number_2", type="string", example="", description="Emergency Contact 2 Number 2"),
     *                     @OA\Property(property="emergency_contact_2_relationship_other", type="string", example="", description="Emergency Contact 2 Relationship Other"),
     *                 ),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Cannot Export List of Invalid Datas, Validation Fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="string", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to Export List of Invalid Datas",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to Export List of Invalid Datas")
     *              )
     *          )
     *     )
     * )
     *
     * Convert json format list to file.xlsx
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function exportInvalidData(Request $request)
    {
        $invalid_datas = $request->all();

        $failed = $invalid_datas['failed'];

        $elder_request = new ElderInvalidDataRequest();
        for ($i = 0; $i < count($failed); $i++) {
            $validator = Validator::make($failed[$i], $elder_request->rules());

            if ($validator->fails()) {
                return response()->json([
                    'status' => [
                        'code' => 422,
                        'message' => "Request cannot be process!",
                        'errors' => $validator->errors(),
                    ]
                ], 422);
            }
        }

        $now = date('d-m-Y_H-i-s');

        return Excel::download(new EldersExportInvalidData($failed), "elders_invalid_datas_$now.xlsx");
    }

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/elders-export-format",
     *     tags={"elders"},
     *     summary="export format elders",
     *     operationId="exportEldersFormat",
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

    public function exportFormat()
    {
        return Excel::download(new EldersFormatExport, 'elders_format.xlsx');
    }

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/elders-autocomplete",
     *     operationId="v1GetElderAutocomplete",
     *     tags={"elders"},
     *     @OA\Parameter(
     *          in="query",
     *          name="page",
     *          example="1"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="per_page",
     *          example="10"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="search",
     *          description="filter name or uid together",
     *          example="me"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="name",
     *          description="username filter",
     *          example="me"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="uid",
     *          description="uid filter",
     *          example="NAAC"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="case_type",
     *          description="case type filter",
     *          example="BZN"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="ids",
     *          description="user id separated by comma",
     *          example="1,2,3,4,5"
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="User List",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object",
     *                      @OA\Property(
     *                          property="id",
     *                          type="integer",
     *                          example="1"
     *                      ),
     *                      @OA\Property(
     *                          property="name",
     *                          type="string",
     *                          example="Grand Meister Aegon"
     *                      ),
     *                      @OA\Property(
     *                          property="uid",
     *                          type="string",
     *                          example="TGR00001"
     *                      ),
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="links",
     *                  type="object",
     *                  @OA\Property(
     *                      property="first",
     *                      type="string",
     *                      example="/elderly-api/v1/elders/autocomplete?page=1"
     *                  ),
     *                  @OA\Property(
     *                      property="last",
     *                      type="string",
     *                      example="/elderly-api/v1/elders/autocomplete?page=10"
     *                  ),
     *                  @OA\Property(
     *                      property="prev",
     *                      type="string",
     *                      example=null
     *                  ),
     *                  @OA\Property(
     *                      property="next",
     *                      type="string",
     *                      example="/elderly-api/v1/elders/autocomplete?page=2"
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="meta",
     *                  type="object",
     *                  @OA\Property(
     *                      property="current_page",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="from",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="last_page",
     *                      type="integer",
     *                      example="10"
     *                  ),
     *                  @OA\Property(
     *                      property="links",
     *                      type="array",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(
     *                              property="url",
     *                              type="string",
     *                              nullable=true,
     *                              example=null
     *                          ),
     *                          @OA\Property(
     *                              property="label",
     *                              type="string",
     *                              example="&laqou; Previous"
     *                          ),
     *                          @OA\Property(
     *                              property="active",
     *                              type="boolean",
     *                              example="false"
     *                          )
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="path",
     *                      type="string",
     *                      example="/elderly-api/v1/elders/autocomplete"
     *                  ),
     *                  @OA\Property(
     *                      property="per_page",
     *                      type="integer",
     *                      example="10"
     *                  ),
     *                  @OA\Property(
     *                      property="to",
     *                      type="integer",
     *                      example="10"
     *                  ),
     *                  @OA\Property(
     *                      property="total",
     *                      type="integer",
     *                      example="100"
     *                  )
     *              )
     *          )
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function autocomplete(Request $request)
    {
        $perPage = $request->query('per_page', 25);
        $elders = Elder::query()
            ->select('id', 'name', 'uid')
            ->when($request->query('name'), function ($query, $name) {
                $query->where('name', 'like', "%$name%");
            })
            ->when($request->query('uid'), function ($query, $uid) {
                $query->where('name', 'like', "%$uid%")
                    ->orWhere('uid', 'like', "%$uid%");
            })
            ->when($request->query('ids'), function ($query, $ids) {
                $elderIds = explode(',', $ids);
                $query->whereIn('id', $elderIds);
            })
            //search is for name OR uid
            ->when($request->query('search'), function ($query, $search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('uid', 'like', "%$search%");
            })
            ->when($request->query('case_type'), function ($query, $case_type) {
                $query->whereHas('cases', function ($q) use ($case_type) {
                    $q->where('case_name', $case_type);
                });
            })
            ->paginate($perPage);

        return new ElderAutocompleteCollection($elders);
    }

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/is-contact-number-available",
     *     operationId="v1IsContactNumberAvailable",
     *     tags={"elders"},
     *     @OA\Parameter(
     *          in="query",
     *          name="contact_number",
     *          example="081234567890"
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="Phone number available",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(
     *                      property="status",
     *                      type="boolean",
     *                      example="true"
     *                  )
     *              )
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response=422,
     *          description="Phone number available",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(
     *                      property="status",
     *                      type="boolean",
     *                      example="false"
     *                  )
     *              )
     *          )
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function isPhoneNumberAvailable(Request $request)
    {
        $phoneNumber = $request->query('contact_number');
        $phoneNumberExists = Elder::where('contact_number', $phoneNumber)->exists();

        return response()->json([
            'data' => [
                'status' => !$phoneNumberExists,
            ],
        ]);
    }

    public function elderEventResourceSet(Request $request) {
        $elderId = null;
        $validated = Validator::make($request->all(), [
            'elder_id' => ['nullable', 'integer']
        ]);
        if(!$validated->fails()){
            $elderId = $request->query('elder_id');
        }
        if(!$elderId){
            return response()->json([
                'data' => null
            ], 404);
        }
        $elder = Elder::select([
            "id",
            "name",
            "uid",
            "case_type",
            "contact_number",
            "second_contact_number",
            "third_contact_number",
            "address",
            "elder_remark",
        ])->where('id', $elderId)->with([
            'cases' => function ($query) {
                $query->select(['id', 'elder_id'])->oldest()->first();
            }
        ])->first();
        if(!$elder){
            return response()->json([
                'data' => null
            ], 404);
        }
        $elder->case_id = (count($elder->cases) == 0) ? null : $elder->cases[0]->id;
        unset($elder->cases);
        return response()->json([
            'data' => $elder
        ], 200);
    }

    public function elderEventManyResourceSet(Request $request) {
        $elderIds = $request->query('elderIds') ? explode(',', $request->query('elderIds')) : null;
        if(!$elderIds){
            return response()->json([
                'data' => null
            ], 404);
        }
        $elders = Elder::select([
            "id",
            "name",
            "uid",
            "case_type",
            "contact_number",
            "second_contact_number",
            "third_contact_number",
            "address",
            "elder_remark",
        ])
        ->whereIn('id', $elderIds)
        ->get();
        $result = new \stdClass();
        if(count($elders) > 0) {
            for($i = 0; $i < count($elders); $i++){
                $elderId = $elders[$i]->id;
                if(!property_exists($result, $elderId)){
                    $result->$elderId = $elders[$i];
                }
            }
        } else {
            return response()->json([
                'data' => null
            ], 404);
        }

        return response()->json([
            'data' => $result
        ], 200);
    }

    public function getCasesStatus(){
        // Check if there is any on-going case status
        $cases = Cases::select('id', 'case_status')->get();
        if(count($cases) == 0){
            return response()->json([
                'data' => null
            ], 404);
        }

        $on_going_keys = ['On-going - BZ', 'On-going - CGA'];
        $pending_keys = ['Pending - BZ', 'Pending - CGA'];
        $finished_keys = ['Finished - BZ', 'Finished - CGA'];
        $result = new \stdClass();
        for($i = 0; $i < count($cases); $i++){
            $case_id = $cases[$i]['id'];
            if(!property_exists($result, $cases[$i]['id'])){
                $result->$case_id['on_going'] = 0;
                $result->$case_id['pending'] = 0;
                $result->$case_id['finished'] = 0;
                if(in_array($cases[$i]['case_status'], $on_going_keys)){
                    $result->$case_id['on_going'] = 1;
                } else if(in_array($cases[$i]['case_status'], $pending_keys)){
                    $result->$case_id['pending'] = 1;
                } else if(in_array($cases[$i]['case_status'], $finished_keys)){
                    $result->$case_id['finished'] = 1;
                }

            } else if(property_exists($result, $cases[$i]['id'])) {
                if(in_array($cases[$i]['case_status'], $on_going_keys)){
                    $result->$case_id['on_going'] =+ 1;
                } else if(in_array($cases[$i]['case_status'], $pending_keys)){
                    $result->$case_id['pending'] =+ 1;
                } else if(in_array($cases[$i]['case_status'], $finished_keys)){
                    $result->$case_id['finished'] =+ 1;
                }
            } 
        }
        return response()->json(["data" => $result], 200);
    }

}
