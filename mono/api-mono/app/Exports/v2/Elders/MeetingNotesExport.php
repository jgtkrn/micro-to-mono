<?php

namespace App\Exports\v2\Elders;

use App\Models\v2\Elders\MeetingNotes;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;

class MeetingNotesExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    use Exportable;
    private $writerType = Excel::CSV;
    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    public function query()
    {
        $data = MeetingNotes::query()->select(['id', 'cases_id', 'notes', 'updated_at', 'updated_by_name', 'created_at']);

        return $data;
    }

    public function map($data): array
    {
        return [
            $data->created_at,
            $data->updated_at,
            ! $data->cases ? null : (
                ! $data->cases->elder ? null : (
                    ! $data->cases->elder->uid ? null : $data->cases->elder->uid
                )
            ),
            $data->updated_by_name,
            $data->notes,
        ];
    }

    public function headings(): array
    {
        return [
            'Date',
            'UID',
            'Person Update',
            'Note',
        ];
    }
}
