<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class AppointmentExportCase implements FromCollection, WithMapping, WithHeadings
{
    use Exportable;

    protected $appointments;

    public function __construct($appointments)
    {
        $this->appointments = $appointments;
    }

    public function collection()
    {
        return $this->appointments;
    }

    public function map($appointment): array
    {
        return [
            $appointment->title,
            $appointment->category_id,
            $appointment->elder ? $appointment->elder->uid : null,
            $appointment->case_manager,
            $appointment->start,
            $appointment->start,
            $appointment->end,
            $appointment->remark,
        ];
    }

    public function headings(): array
    {
        return [
            'Title',
            'Type',
            'UID',
            'Case Manager',
            'Date',
            'Start',
            'End',
            'Remark',
        ];
    }
}
