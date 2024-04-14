<?php

namespace App\Exports\v2\Users;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    public function collection()
    {
        return $this->users;
    }

    public function map($user): array
    {
        $data = [
            $user->staff_name ?? null,
            $user->on_going ?? null,
            $user->pending ?? null,
            $user->finished ?? null,
            $user->reservations ?? null,
            $user->followup ?? null,
            $user->appointment ?? null,
            $user->calls_log ?? null,
            $user->case_contact_hour ?? null,
            $user->administrative_work ?? null,
            $user->meeting ?? null,
        ];

        return $data;
    }

    public function headings(): array
    {
        $data = [
            'Employee Name',
            'Ongoing Cases',
            'Pending Cases',
            'Finished Cases',
            'Number Of Reservations Made',
            'Number Of Evaluations',
            'Number Of Case Interviews',
            'Total Number Of Calls',
            'Total Hours Of Case Contact',
            'Total Number Of Administrative Work',
            'Total Meeting Hours',
        ];

        return $data;
    }
}
