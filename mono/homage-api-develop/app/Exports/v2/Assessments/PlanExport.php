<?php

namespace App\Exports\v2\Assessments;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PlanExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $plans;

    public function __construct($plans)
    {
        $this->plans = $plans;
    }

    public function collection()
    {
        return $this->plans;
    }

    public function map($plan): array
    {
        return [
            $plan['assessment_date'],
            $plan['uid'],
            $plan['meeting'],
            $plan['domain'],
            $plan['area'],
            $plan['modifier'],
            $plan['urgency'],
            $plan['priority'],
            $plan['category'],
            $plan['target'],
            $plan['intervention_remark'],
            $plan['knowledge'],
            $plan['behaviour'],
            $plan['status'],
            $plan['case_remark'],
        ];
    }

    public function headings(): array
    {
        return [
            'Date',
            'UID',
            'Section',
            'Domain',
            'Area',
            'Modifier',
            'Urgency',
            'Priority',
            'Inter Categ',
            'Inter Target',
            'Inter Remark',
            'Knowledge',
            'Behaviour',
            'Status',
            'Remark',
        ];
    }
}
