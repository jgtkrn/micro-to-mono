<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Http\Services\v2\Assessments\FormService;
use App\Models\v2\Assessments\AssessmentCase;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FormController extends Controller
{
    use RespondsWithHttpStatus;
    private $formService;

    public function __construct()
    {
        $this->formService = new FormService;
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
    }

    public function show(Request $request, $id)
    {
        $request->validate([
            'form_name' => ['required', Rule::in($this->formService->getFormNames())],
        ]);

        $form_name = $request->query('form_name');

        $assessment_case = AssessmentCase::where('id', $id)->first();
        if (! $assessment_case) {
            return $this->failure('Assessment case not found', 404);
        }

        $form = $this->formService->show($assessment_case, $form_name);

        return response()->json(['data' => $form], 200);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'form_name' => ['required', Rule::in($this->formService->getFormNames())],
        ]);

        $assessment_case = AssessmentCase::where('id', $id)->first();
        if (! $assessment_case) {
            return $this->failure('Assessment case not found', 404);
        }

        $form = $this->formService->updateOrCreate($request, $id);

        return response()->json(['data' => $form], 200);
    }

    public function destroy($id)
    {
        //
    }
}
