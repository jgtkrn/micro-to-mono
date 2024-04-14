<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Http\Services\v2\Assessments\WiringServiceAssessment;
use Illuminate\Http\Request;

class ReportsDashboardController extends Controller
{
    private $wiringService;

    public function __construct()
    {
        $this->wiringService = new WiringServiceAssessment;
    }

    public function indexInsight(Request $request)
    {
        return $this->wiringService->getInsightReports($request);
    }
}
