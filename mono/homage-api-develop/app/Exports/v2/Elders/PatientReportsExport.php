<?php

namespace App\Exports\v2\Elders;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PatientReportsExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $patients;

    public function __construct($patients)
    {
        $this->patients = $patients;
    }

    public function collection()
    {
        return $this->patients;
    }

    public function map($patient): array
    {
        return [
            $patient->patient_name,
            $patient->uid,
            $patient->case_manager,
            $patient->case_status,
            $patient->first_visit,
            $patient->last_visit,
            $patient->total_visit,
            $patient->calls_log,
            $patient->contact_total_number,
            $patient->case_phone_contact,
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'UID',
            'Case Manager',
            'Case Status',
            'First Home Visit',
            'Final Visit',
            'Visits',
            'Number of Successful Calls',
            'Case Home Visit Contact Total Time',
            'Total Hours of Case Phone Contact',
        ];
    }
}
