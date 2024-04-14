<?php

namespace App\Exports\MeetingNotes;

use App\Models\MeetingNotes;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;

class MeetingNotesExport implements FromQuery, WithHeadings, ShouldAutoSize, WithMapping
{
    use Exportable;
    private $writerType = Excel::CSV;
    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    public function query()
    {
        $data = MeetingNotes::query()->select(['id', 'cases_id', 'notes', 'updated_at', 'updated_by_name']);
        return $data;
    }

    public function map($data): array
    {
        return [
            $data->updated_at,
            !$data->cases ? null : (
                !$data->cases->elder ? null : (
                    !$data->cases->elder->uid ? null : $data->cases->elder->uid
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
            'Note'
        ];
    }
}
