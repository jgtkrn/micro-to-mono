<?php

namespace App\Exports\v2\Elders;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CallHistoryExport implements FromArray, WithHeadings, WithMapping
{
    use Exportable;

    protected $calls;

    public function __construct(array $call_data)
    {
        $this->calls = $call_data;
    }

    public function array(): array
    {
        return $this->calls;
    }

    public function map($call): array
    {
        return [
            $call['uid'] ?? null,
            $call['created_by_name'] ?? null,
            $call['call_date'] ?? null,
            $call['call_status'] ?? null,
            $call['status_other'] ?? null,
            $call['remark'] ?? null,
        ];
    }

    public function headings(): array
    {
        return [
            'UID',
            'Contact_Person',
            'Date',
            'Status',
            'Status_Other_Spesific',
            'Remark',
        ];
    }
}
