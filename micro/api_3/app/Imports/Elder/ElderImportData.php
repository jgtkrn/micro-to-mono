<?php

namespace App\Imports\Elder;

use App\Http\Controllers\Query\QueryController;
use App\Models\District;
use App\Models\Elder;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ElderImportData implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $function = new QueryController();
        $uid = '';
        foreach ($rows as $row) {
            $districtId = $function->createDistrictID($row['district_name']);
            if ($row['uid'] == '') {
                $uid = $function->createUID($row['district_name']);
            } else {
                $uid = $row['uid'];
            }
            Elder::create([
                'UID' => $uid,
                'case_type' => $row['case_type'],
                'name' => $row['name'],
                'gender' => $row['gender'],
                'birth_day' => $row['birth_day'],
                'birth_month' => $row['birth_month'],
                'birth_year' => $row['birth_year'],
                'contact_number' => $row['contact_number'],
                'address' => $row['address'],
                'district_id' => $districtId,
                'emergency_contact_name' => $row['emergency_contact_name'],
                'relationship' => $row['relationship'],
                'uid_connected_with' => $row['uid_connected'],
                'health_issue' => $row['health_issue'],
                'medication' => $row['medication'],
                'limited_mobility' => $row['limited_mobility'],
            ]);
            $function->saveHistoryUID($uid);
        }
    }

    public function rules(): array
    {
        $allDistrict = District::select('district_name')->get();
        $data = array();
        foreach ($allDistrict as $district) {
            array_push($data, $district->district_name);
        }

        return [
            // 'uid' => ['unique:record_uids,UID'],
            'name' => ['required', 'max:120'],
            'case_type' => ['required', 'in:CGA,BZN'],
            'district_name' => [
                'required',
                Rule::in(array_values($data))
            ],
            'contact_number' => ['sometimes'],
            'address' => ['required'],
            'gender' => ['in:male,female'],
            'birth_day' => ['required', 'integer', 'max:31'],
            'birth_month' => ['required', 'integer', 'max:12'],
            'birth_year' => ['required', 'digits:4', 'integer', 'min:1900', 'max:' . date('Y') + 1],
            'emergency_contact_number' => ['sometimes'],
            'emergency_contact_name' => ['sometimes'],
            'emergency_contact_relationship_other' => ['sometimes'],
            'relationship' => ['sometimes'],
            'UID_connected_with' => ['sometimes'],
            'health_issue' => ['sometimes'],
            'medication' => ['sometimes'],
            'limited_mobility' => ['sometimes'],
            'created_by' => ['sometimes'],
            'updated_by' => ['sometimes'],

        ];
    }
}
