<?php

namespace App\Exports\District;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;;

use Maatwebsite\Excel\Excel;

class DistrictFormatExport implements WithHeadings, ShouldAutoSize
{
    use Exportable;
    private $writerType = Excel::XLSX;
    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    public function headings(): array
    {
        return [
            'district_name',
            'bzn_code'
        ];
    }
}
