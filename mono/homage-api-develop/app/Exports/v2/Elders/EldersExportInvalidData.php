<?php

namespace App\Exports\v2\Elders;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;

class EldersExportInvalidData implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    use Exportable;

    protected $invalid_datas;

    private $writerType = Excel::XLSX;
    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    public function __construct(array $invalid_datas)
    {
        $this->invalid_datas = $invalid_datas;
    }

    public function collection()
    {
        return collect($this->invalid_datas);
    }

    public function map($invalid_datas): array
    {
        return [
            $invalid_datas['uid'],
            $invalid_datas['name'],
            $invalid_datas['name_en'],
            $invalid_datas['gender'],
            $invalid_datas['birth_day'],
            $invalid_datas['birth_month'],
            $invalid_datas['birth_year'],
            $invalid_datas['contact_number'],
            $invalid_datas['second_contact_number'],
            $invalid_datas['third_contact_number'],
            $invalid_datas['address'],
            $invalid_datas['district'],
            $invalid_datas['zone'],
            $invalid_datas['language'],
            $invalid_datas['centre_case_id'],
            $invalid_datas['centre_responsible_worker'],
            $invalid_datas['responsible_worker_contact'],
            $invalid_datas['related_uid'],
            $invalid_datas['relationship'],
            $invalid_datas['case_type'],
            $invalid_datas['source_of_referral'],
            $invalid_datas['emergency_contact_name'],
            $invalid_datas['emergency_contact_number'],
            $invalid_datas['emergency_contact_number_2'],
            $invalid_datas['emergency_contact_relationship_other'],
            $invalid_datas['emergency_contact_2_name'],
            $invalid_datas['emergency_contact_2_number'],
            $invalid_datas['emergency_contact_2_number_2'],
            $invalid_datas['emergency_contact_2_relationship_other'],
        ];
    }

    public function headings(): array
    {
        return [
            'UID',
            'Name',
            'Name_en',
            'Gender',
            'Birth Day',
            'Birth Month',
            'Birth Year',
            'Contact Number',
            'Second Contact Number',
            'Third Contact Number',
            'Address',
            'District',
            'Zone',
            'Language',
            'Centre Case ID',
            'Centre Responsible Worker',
            'Responsible Worker Contact',
            'Related UID',
            'Relationship',
            'Case Type',
            'Source of Referral',
            'Emergency Contact Name',
            'Emergency Contact Number',
            'Emergency Contact Number 2',
            'Emergency Contact Relationship Other',
            'Emergency Contact 2 Name',
            'Emergency Contact 2 Number',
            'Emergency Contact 2 Number 2',
            'Emergency Contact 2 Relationship Other',
        ];
    }
}
