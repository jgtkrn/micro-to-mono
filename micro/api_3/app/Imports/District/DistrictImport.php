<?php

namespace App\Imports\District;

use App\Models\District;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DistrictImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new District([
            'district_name' => $row['district_name'],
            'bzn_code' => $row['bzn_code'],
        ]);
    }

    public function rules(): array
    {
        return [
            'district_name' => ['required', 'unique:districts,district_name'],
            'bzn_code' => ['required'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'district_name.required' => 'District name required please check your excel file',
            'district_name.unique' => 'District name already exits please check your excel file',
            'bzn_code.required' => 'Bzn Code required please check your excel file',
        ];
    }
}
