<?php

namespace App\Exports\Elder;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Excel;

class EldersFormatExport implements WithHeadings, ShouldAutoSize
{
    use Exportable;
    private $writerType = Excel::XLSX;
    private $headers = [
        'Content-Type' => 'text/csv',
    ];



    public function headings(): array
    {
        return [
            'uid',
            'case_type',
            'name',
            'gender',
            'birth_day',
            'birth_month',
            'birth_year',
            'contact_number',
            'address',
            'district_name',
            'emergency_contact_name',
            'emergency_contact_number',
            'relationship',
            'uid_connected',
            'health_issue',
            'medication',
            'limited_mobility',
        ];
    }
}
