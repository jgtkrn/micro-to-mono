<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Models\AssessmentCase;
use App\Http\Services\ExternalService;

class AssessmentCaseTypeSeeder extends Seeder
{
    private $externalService;

    public function __construct()
    {
        $this->externalService = new ExternalService();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $output = new ConsoleOutput();

        $assessment_cases = AssessmentCase::where('case_type', null)->get();
        $output->writeln('Empty case type count: ' . count($assessment_cases));

        $updated_assessment = 0;
        $failed_update = 0;

        foreach ($assessment_cases as $assessment_case) {
            $updated_assessment++;
            $case_id = $assessment_case->case_id;
            $case_type = $this->externalService->getElderCaseId($case_id);
            if ($case_type == null) {
                $failed_update++;
                $output->writeln('Assessment case ' . $updated_assessment . ' failed to update with case id ' . $case_id);
            } else {
                $assessment_case->case_type = $case_type;
                $assessment_case->save();
                $output->writeln('Assessment case ' . $updated_assessment . ' updated successfully with case id ' . $case_id);
            }
            sleep(1); //delay
        }

        $output->writeln('Finished update of ' . $updated_assessment . ' assessment case with ' . $failed_update . ' failed case');
    }
}
