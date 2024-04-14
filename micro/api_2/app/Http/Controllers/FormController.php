<?php

namespace App\Http\Controllers;

use App\Http\Services\FormService;
use Illuminate\Http\Request;
use App\Models\AssessmentCase;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Validation\Rule;

class FormController extends Controller
{
    use RespondsWithHttpStatus;
    private $formService;

    public function __construct()
    {
        $this->formService = new FormService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/assessment-case-forms/{id}",
     *     tags={"AssessmentCaseForm"},
     *     summary="Assessment case form details",
     *     operationId="assessmentCaseFormDetails",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of assessment case",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="form_name",
     *         in="query",
     *         description="Form name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="data",
     *                 oneOf={
     *                      @OA\Schema(ref="#/components/schemas/PhysiologicalMeasurementForm"),
     *                      @OA\Schema(ref="#/components/schemas/RePhysiologicalMeasurementForm"),
     *                      @OA\Schema(ref="#/components/schemas/MedicalConditionForm"),
     *                      @OA\Schema(ref="#/components/schemas/MedicationAdherenceForm"),
     *                      @OA\Schema(ref="#/components/schemas/LubbenSocialNetworkScaleForm"),
     *                      @OA\Schema(ref="#/components/schemas/SocialBackgroundForm"),
     *                      @OA\Schema(ref="#/components/schemas/FunctionMobilityForm"),
     *                      @OA\Schema(ref="#/components/schemas/BarthelIndexForm"),
     *                      @OA\Schema(ref="#/components/schemas/GeriatricDepressionScaleForm"),
     *                      @OA\Schema(ref="#/components/schemas/IadlForm"),
     *                      @OA\Schema(ref="#/components/schemas/MontrealCognitiveAssessmentForm"),
     *                      @OA\Schema(ref="#/components/schemas/GenogramForm"),
     *                      @OA\Schema(ref="#/components/schemas/PhysicalConditionForm"),
     *                      @OA\Schema(ref="#/components/schemas/AssessmentCaseStatus"),
     *                      @OA\Schema(ref="#/components/schemas/AssessmentCaseAttachment"),
     *                      @OA\Schema(ref="#/components/schemas/AssessmentCaseSignature"),
     *                      @OA\Schema(ref="#/components/schemas/QualtricsForm"),
     *                      @OA\Schema(ref="#/components/schemas/SocialWorkerForm")
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="422"),
     *                 @OA\Property(property="message", type="string", example=""),
     *                 @OA\Property(property="errors", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="field", type="string", description="Field name", example="form_name"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The selected form name is invalid")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Assessment case not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Assessment case not found"),
     *                 @OA\Property(property="errors", type="array", description="Empty array", example="[]",
     *                     @OA\Items()
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $request->validate([
            "form_name" => ["required", Rule::in($this->formService->getFormNames())]
        ]);

        $form_name = $request->query('form_name');

        $assessment_case = AssessmentCase::where('id', $id)->first();
        if (!$assessment_case) {
            return $this->failure('Assessment case not found', 404);
        }

        $form = $this->formService->show($assessment_case, $form_name);

        return response()->json(['data' => $form], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/assessments-api/v1/assessment-case-forms/{id}",
     *     tags={"AssessmentCaseForm"},
     *     summary="Update assessment case form",
     *     operationId="assessmentCaseFormUpdate",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of assessment case",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input form assessment (in json)",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="form_name", type="string", example="physiological_measurement"),
     *             oneOf={
     *                 @OA\Schema(ref="#/components/schemas/PhysiologicalMeasurementForm"),
     *                 @OA\Schema(ref="#/components/schemas/RePhysiologicalMeasurementForm"),
     *                 @OA\Schema(ref="#/components/schemas/MedicalConditionForm"),
     *                 @OA\Schema(ref="#/components/schemas/MedicationAdherenceForm"),
     *                 @OA\Schema(ref="#/components/schemas/LubbenSocialNetworkScaleForm"),
     *                 @OA\Schema(ref="#/components/schemas/SocialBackgroundForm"),
     *                 @OA\Schema(ref="#/components/schemas/FunctionMobilityForm"),
     *                 @OA\Schema(ref="#/components/schemas/BarthelIndexForm"),
     *                 @OA\Schema(ref="#/components/schemas/GeriatricDepressionScaleForm"),
     *                 @OA\Schema(ref="#/components/schemas/IadlForm"),
     *                 @OA\Schema(ref="#/components/schemas/MontrealCognitiveAssessmentForm"),
     *                 @OA\Schema(ref="#/components/schemas/PhysicalConditionForm"),
     *                 @OA\Schema(ref="#/components/schemas/AssessmentCaseStatus"),
     *                 @OA\Schema(ref="#/components/schemas/QualtricsForm"),
     *                 @OA\Schema(ref="#/components/schemas/SocialWorkerForm")
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="data",
     *                 oneOf={
     *                      @OA\Schema(ref="#/components/schemas/PhysiologicalMeasurementForm"),
     *                      @OA\Schema(ref="#/components/schemas/RePhysiologicalMeasurementForm"),
     *                      @OA\Schema(ref="#/components/schemas/MedicalConditionForm"),
     *                      @OA\Schema(ref="#/components/schemas/MedicationAdherenceForm"),
     *                      @OA\Schema(ref="#/components/schemas/LubbenSocialNetworkScaleForm"),
     *                      @OA\Schema(ref="#/components/schemas/SocialBackgroundForm"),
     *                      @OA\Schema(ref="#/components/schemas/FunctionMobilityForm"),
     *                      @OA\Schema(ref="#/components/schemas/BarthelIndexForm"),
     *                      @OA\Schema(ref="#/components/schemas/GeriatricDepressionScaleForm"),
     *                      @OA\Schema(ref="#/components/schemas/IadlForm"),
     *                      @OA\Schema(ref="#/components/schemas/MontrealCognitiveAssessmentForm"),
     *                      @OA\Schema(ref="#/components/schemas/PhysicalConditionForm"),
     *                      @OA\Schema(ref="#/components/schemas/AssessmentCaseStatus"),
     *                      @OA\Schema(ref="#/components/schemas/QualtricsForm"),
     *                      @OA\Schema(ref="#/components/schemas/SocialWorkerForm")
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="422"),
     *                 @OA\Property(property="message", type="string", example=""),
     *                 @OA\Property(property="errors", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="field", type="string", description="Field name", example="form_name"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The selected form name is invalid")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Assessment case not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Assessment case not found"),
     *                 @OA\Property(property="errors", type="array", description="Empty array", example="[]",
     *                     @OA\Items()
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            "form_name" => ["required", Rule::in($this->formService->getFormNames())]
        ]);
        if(in_array($request->form_name, [
            'physiological_measurement',
            're_physiological_measurement',
            'medical_condition',
            'medication_adherence',
            'lubben_social_network_scale',
            'social_background',
            'function_mobility',
            'barthel_index',
            'geriatric_depression_scale',
            'iadl',
            'physical_condition',
        ]) && !$request->is_bzn && $request->access_role !== 'admin') {
            return response()->json([
                'data' => null,
                'message' => 'User not in BZN team access'
            ], 401);
        } else if (in_array($request->form_name, [
            'geriatric_depression_scale',
            'qualtrics',
            'social_worker'
        ]) && !$request->is_cga  && $request->access_role !== 'admin') {
           return response()->json([
                'data' => null,
                'message' => 'User not in CGA team access'
            ], 401);
        }

        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }

        $assessment_case = AssessmentCase::where('id', $id)->first();
        if (!$assessment_case) {
            return $this->failure('Assessment case not found', 404);
        }

        $form = $this->formService->updateOrCreate($request, $id);

        return response()->json(['data' => $form], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
