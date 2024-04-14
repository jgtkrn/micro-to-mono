<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Http\Requests\v2\Assessments\ElderIndexAssessmentRequest;
use App\Http\Services\v2\Assessments\WiringServiceAssessment;
use Illuminate\Http\Request;

class ElderProfile extends Controller
{
    private $wiringService;

    public function __construct()
    {
        $this->wiringService = new WiringServiceAssessment;
    }

    public function index(ElderIndexAssessmentRequest $request)
    {
        $case_id = $request->query('case_id');
        $case_type = $request->query('case_type');
        $data = null;
        if ($case_id && $case_type) {
            $regex_base = '/baseline/i';
            $regex_hc = '/hc/i';
            $regex_bzn = '/bzn/i';
            $regex_nurse = '/nurse/i';
            if (preg_match($regex_hc, $case_type) > 0) {
                $data = $this->wiringService->getCgaHcData($case_id);
            }
            if (preg_match($regex_bzn, $case_type) > 0) {
                $data = $this->wiringService->getBznData($case_id);
            }
            if (preg_match($regex_nurse, $case_type) > 0) {
                $data = $this->wiringService->getCgaNurseData($case_id);
            }
            if (preg_match($regex_base, $case_type) > 0) {
                $data = $this->wiringService->getCgaBaseData($case_id);
            }
        }
        if ($data != null) {
            return response()->json([
                'data' => $data,
            ], 200);
        }

        return response()->json([
            'data' => null,
            'message' => 'Data not Found',
        ], 404);
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
