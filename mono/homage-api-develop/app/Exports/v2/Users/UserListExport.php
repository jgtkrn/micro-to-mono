<?php

namespace App\Exports\v2\Users;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserListExport implements FromCollection, WithHeadings, WithMapping
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
            $user->name,
            $user->nickname,
            $user->staff_number,
            $user->user_status,
            $user->email,
            $user->email_cityu,
            $user->phone_number,
        ];
        for ($i = 0; $i < count($this->roles); $i++) {
            $user_role = in_array($this->roles[$i]['id'], $user->role_ids) ? '1' : '0';
            array_push($data, $user_role);
        }
        for ($i = 0; $i < count($this->teams); $i++) {
            $user_team = in_array($this->teams[$i]['id'], $user->team_ids) ? '1' : '0';
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
            'Name',
            'Nick Name',
            'Staff_ID',
            'Status',
            'Email',
            'Email_CityU',
            'Phone',
        ];
        for ($i = 0; $i < count($this->roles); $i++) {
            $count = $i + 1;
            array_push($data, "Role_{$count}");
        }
        for ($i = 0; $i < count($this->teams); $i++) {
            $count = $i + 1;
            array_push($data, "Team_{$count}");
        }
        array_push($data, 'Employment', 'Access');

        return $data;

    }
}
