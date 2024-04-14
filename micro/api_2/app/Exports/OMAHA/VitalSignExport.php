<?php

namespace App\Exports\OMAHA;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class VitalSignExport implements FromCollection, WithMapping, WithHeadings
{
    use Exportable;

    protected $vitalSigns;

    public function __construct($vitalSigns)
    {
        $this->vitalSigns = $vitalSigns;
    }

    public function collection()
    {
        return $this->vitalSigns;
    }

    public function map($vitalSign): array
    {
        return [
            $vitalSign['assessment_date'],
            $vitalSign['assessment_time'],
            $vitalSign['uid'],
            $vitalSign['assessor'],
            $vitalSign['meeting'],
            $vitalSign['sbp'],
            $vitalSign['dbp'],
            $vitalSign['pulse'],
            $vitalSign['pao'],
            $vitalSign['hstix'],
            $vitalSign['visiting_duration'],
            $vitalSign['case_remark'],
        ];
    }

    public function headings(): array
    {
        return [
            'Date',
            'Time',
            'UID',
            'Assessor',
            'Section',
            'BP S',
            'BP D',
            'Pulse',
            'PAO',
            'Hstix',
            'Stay Time',
            'Remark',
        ];
    }
}
