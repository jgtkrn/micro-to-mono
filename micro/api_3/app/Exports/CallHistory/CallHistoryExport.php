<?php

namespace App\Exports\CallHistory;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Excel;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;

class CallHistoryExport implements FromArray, WithMapping, WithHeadings
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
            $call->caller_id,
            $call->call_date,
            $call->call_status,
            $call->remark
        ];
    }

    public function headings(): array
    {
        return [
            'Contact_Person',
            'Date',
            'Status',
            'Remark',
        ];
    }
}
