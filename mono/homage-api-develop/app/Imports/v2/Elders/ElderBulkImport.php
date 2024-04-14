<?php

namespace App\Imports\v2\Elders;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ElderBulkImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection)
    {
        return [
            //
        ];
    }
}
