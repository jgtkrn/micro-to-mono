<?php

namespace App\Exports\v2\Assessments;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;

class HealthCoachingSessionExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $data;

    private $writerType = Excel::CSV;
    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function map($data): array
    {
        return [
            $data->uid,
            $data->assessor_1,
            $data->assessor_2,
            $data->assessment_date,
            $data->assessment_time,
            $data->visit_type,
            $data->sbp,
            $data->dbp,
            $data->pulse,
            $data->pao,
            $data->hstix,
            $data->body_weight,
            $data->waist,
            $data->circumference,
            $data->purpose,
            $data->followup,
            $data->progress,
            $data->personal_insight,
            $data->case_summary,
            $data->case_status,
            $data->case_remark,
        ];
    }

    public function headings(): array
    {
        return [
            'UID',
            'Assessor_1',
            'Assessor_2',
            'Date',
            'Time',
            'Visit_Type',
            'BP_S',
            'BP_D',
            'Pulse',
            'Blood_Oxy',
            'Blood_Sugar',
            'Weight',
            'Waistline',
            'VS_Other',
            'Session_Aim',
            'Session_Item',
            'Session_Content',
            'Session_Observation',
            'Session_Summary',
            'Case_Progress',
            'Session_Remark',
        ];
    }
}
