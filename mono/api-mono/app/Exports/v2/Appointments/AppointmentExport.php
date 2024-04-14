<?php

namespace App\Exports\v2\Appointments;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AppointmentExport implements FromCollection, WithHeadings, WithMapping
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
            $appointment->id,
            $appointment->title,
            $appointment->start,
            $appointment->elder ? $appointment->elder->name : null,
            $appointment->elder ? $appointment->elder->uid : null,
            $appointment->elder ? $appointment->elder->contact_number : null,
            $appointment->elder ? $appointment->elder->address : null,
        ];
    }

    public function headings(): array
    {
        return [
            'Id',
            'Event Subject',
            'Time Date',
            'Elder Name',
            'UID',
            'Elder Contact',
            'Elder Address',
        ];
    }
}
