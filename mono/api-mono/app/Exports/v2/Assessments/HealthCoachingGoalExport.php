<?php

namespace App\Exports\v2\Assessments;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;

class HealthCoachingGoalExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $cga_care_target;

    private $writerType = Excel::CSV;
    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    public function __construct($cga_care_target)
    {
        $this->cga_care_target = $cga_care_target;
    }

    public function collection()
    {
        return $this->cga_care_target;
    }

    public function map($cga_care_target): array
    {
        return [
            $cga_care_target->uid,
            $cga_care_target->updated_at,
            $cga_care_target->health_vision,
            $cga_care_target->later_change_stage,
            $cga_care_target->early_change_stage,
            $cga_care_target->long_term_goal,
            $cga_care_target->short_term_goal,
            $cga_care_target->motivation,
        ];
    }

    public function headings(): array
    {
        return [
            'UID',
            'Date',
            'Health_Vision',
            'State_Post',
            'State_Pre',
            'Goal_LT',
            'Goal_ST',
            'Goal_Session',
        ];
    }
}
