<?php

namespace App\Imports\v2\Elders;

use App\Http\Controllers\v2\Elders\QueryController;
use App\Models\v2\Elders\Elder;
use App\Models\v2\Elders\Referral;
use App\Models\v2\Elders\Zone;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ElderImportData implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param  Collection  $collection
     */
    public function collection(Collection $rows)
    {
        $function = new QueryController;
        $uid = '';
        foreach ($rows as $row) {
            $districtId = $function->createDistrictID($row['district']);
            if ($row['uid'] == '') {
                $uid = $function->createUID($row['district']);
            } else {
                $uid = $row['uid'];
            }
            $zone = Zone::where('name', $row['zone'])->first();
            $referral = Referral::where('label', $row['source_of_referral'])->first();
            Elder::create([
                'uid' => $uid,
                'case_type' => $row['case_type'],
                'name' => $row['name'],
                'gender' => in_array($row['gender'], ['F', 'f', 'female']) ? 'female' : 'male',
                'birth_day' => $row['birth_day'],
                'birth_month' => $row['birth_month'],
                'birth_year' => $row['birth_year'],
                'contact_number' => $row['contact_number'],
                'address' => $row['address'],
                'district_id' => $districtId,
                'zone_id' => $zone->id,
                'referral_id' => $referral->id,
                'emergency_contact_name' => $row['emergency_contact_name'],
                'relationship' => $row['relationship'],
                'uid_connected_with' => $row['related_uid'],
                'health_issue' => $row['health_issue'],
                'medication' => $row['medication'],
                'limited_mobility' => $row['limited_mobility'],
            ]);
            $function->saveHistoryUID($uid);
        }
    }

    public function rules(): array
    {
        return [
            'uid' => ['unique:record_uids,UID'],
            'name' => ['required', 'max:120'],
            'case_type' => ['required', 'in:CGA,BZN'],
            'district' => [
                'required', 'exists:districts,district_name',
            ],
            'source_of_referral' => 'nullable|exists:referrals,label',
            'zone' => 'nullable|exists:zones,name',
            'contact_number' => ['nullable'],
            'address' => ['required'],
            'gender' => ['in:male,female,m,l,M,L'],
            'birth_day' => ['required', 'integer', 'max:31'],
            'birth_month' => ['required', 'integer', 'max:12'],
            'birth_year' => ['required', 'digits:4', 'integer', 'min:1900', 'max:' . date('Y') + 1],
            'emergency_contact_number' => ['nullable'],
            'emergency_contact_name' => ['nullable'],
            'emergency_contact_relationship_other' => ['nullable'],
            'relationship' => ['nullable'],
            'related_uid' => ['nullable'],
            'health_issue' => ['nullable'],
            'medication' => ['nullable'],
            'limited_mobility' => ['nullable'],
        ];
    }
}
