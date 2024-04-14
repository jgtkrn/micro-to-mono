<?php

namespace App\Imports\v2\Assessments;

use App\Models\v2\Assessments\MedicationDrug;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MedicationDrugImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $category = $row['category'];
            $parent = MedicationDrug::select('id', 'name')->where('name', $category)->first();
            MedicationDrug::create([
                'parent_id' => $parent?->id ?? null,
                'name' => $row['name'],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'category' => ['required', 'exists:medication_drugs,name,deleted_at,NULL'],
        ];
    }
}
