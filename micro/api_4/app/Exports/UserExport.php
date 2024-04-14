<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Excel;
use Carbon\Carbon;

class UserExport implements FromCollection, WithMapping, WithHeadings
{
    use Exportable;

    protected $users;
    protected $roles;
    protected $teams;

    public function __construct($users, $roles, $teams)
    {
        $this->users = $users;
        $this->roles = $roles;
        $this->teams = $teams;
    }

    public function collection()
    {
        return $this->users;
    }

    public function map($user): array
    {
        $data = [
            $user->staff_name,
            $user->on_going,
            $user->pending,
            $user->finished,
            $user->reservations,
            $user->followup,
            $user->appointment,
            $user->calls_log,
            $user->case_contact_hour,
            $user->administrative_work,
            $user->meeting,
        ];
        for ($i = 0; $i < count($this->roles); $i++){
            $user_role = in_array($this->roles[$i]['id'], $user->role_ids) ? "1" : "0";
            array_push($data, $user_role);
        }
        for ($i = 0; $i < count($this->teams); $i++){
            $user_team = in_array($this->teams[$i]['id'], $user->team_ids) ? "1" : "0";
            array_push($data, $user_team);
        }
        $employment = $user->employment_status == 'FT' ? 1 : 2;
        array_push($data, $employment);
        array_push($data, $user->access_role_id);
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
        for ($i = 0; $i < count($this->roles); $i++){
            $count = $i + 1;
            array_push($data, "Role_$count");
        }
        for ($i = 0; $i < count($this->teams); $i++){
            $count = $i + 1;
            array_push($data, "Team_$count");
        }
        array_push($data, 'Employment', 'Access');
        return $data;
    }
}
