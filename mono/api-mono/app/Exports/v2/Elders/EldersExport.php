<?php

namespace App\Exports\v2\Elders;

use App\Models\v2\Elders\Elder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;

class EldersExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    use Exportable;
    private $writerType = Excel::CSV;
    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    public function query()
    {
        $elder = Elder::query()
            ->with('district')
            ->with('referral')
            ->with('centreResponsibleWorker')
            ->with('zone');

        return $elder;
    }

    public function map($elder): array
    {
        return [
            $elder->uid,
            $elder->name,
            $elder->name_en,
            $elder->gender,
            $elder->birth_day,
            $elder->birth_month,
            $elder->birth_year,
            $elder->contact_number,
            $elder->second_contact_number,
            $elder->third_contact_number,
            $elder->address,
            $elder->district->district_name,
            $elder->referral->label,
            $elder->emergency_contact_name,
            $elder->emergency_contact_number,
            $elder->emergency_contact_number_2,
            $elder->emergency_contact_relationship_other,
            $elder->emergency_contact_2_name,
            $elder->emergency_contact_2_number,
            $elder->emergency_contact_2_number_2,
            $elder->emergency_contact_2_relationship_other,
            $elder->uid_connected_with,
            $elder->relationship,
            $elder->case_type,
            $elder->language,
            $elder->elder_remark,
            $elder->centre_case_id,
            $elder->centreResponsibleWorker != null
                ? ($elder->centreResponsibleWorker->code != 'other'
                    ? $elder->centreResponsibleWorker->name
                    : $elder->centre_responsible_worker_other
                )
                : null,
            $elder->responsible_worker_contact,
            $elder->zone != null
                ? ($elder->zone->code != 'other'
                    ? $elder->zone->name
                    : $elder->zone_other
                )
                : null,
        ];
    }

    public function headings(): array
    {
        return [
            'UID',
            'Name',
            'Name En',
            'Gender',
            'Birth Day',
            'Birth Month',
            'Birth Year',
            'Contact Number',
            'Second Contact Number',
            'Third Contact Number',
            'Address',
            'District',
            'Source of Referral',
            'Emergency Contact Name',
            'Emergency Contact Number',
            'Emergency Contact Number 2',
            'Relationship',
            'Emergency Contact 2 Name',
            'Emergency Contact 2 Number',
            'Emergency Contact 2 Number 2',
            'Emergency Contact 2 Relationship Other',
            'Related UID',
            'Emergency Contact Relationship Other',
            'Case Type',
            'Language',
            'Elder Remark',
            'Centre Case ID',
            'Centre Responsible Worker',
            'Responsible Worker Contact',
            'Zone',
        ];
    }
}
