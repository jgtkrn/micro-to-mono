<?php

namespace App\Http\Controllers\v2\Elders;

use App\Http\Controllers\Controller;
use App\Http\Requests\v2\Elders\ElderRequest;
use App\Http\Resources\v2\Elders\ElderSingleResource;
use App\Models\v2\Elders\Cases;
use App\Models\v2\Elders\CentreResponsibleWorker;
use App\Models\v2\Elders\District;
use App\Models\v2\Elders\Elder;
use App\Models\v2\Elders\RecordUid;
use App\Models\v2\Elders\Referral;
use App\Models\v2\Elders\Zone;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QueryController extends Controller
{
    //Function create UID
    public function createUID($district)
    {
        $checkDistrict = District::select('district_name', 'bzn_code')->where('district_name', 'like', '%' . ucwords($district) . '%')->first();
        $code = $checkDistrict->bzn_code;
        $uid = RecordUid::select('uid')->where('uid', 'LIKE', '%' . $code . '%')->max('uid');
        $data = (int) substr($uid, strlen($code) < 4 ? 3 : 4, strlen($code) < 4 ? 3 : 4);
        $data++;
        $code = $code . sprintf('%04s', $data);

        return $code;
    }
    //===============================================================================================================================================

    // Function create DistrictID
    public function createDistrictID($district)
    {
        $id = District::select('id')->where('district_name', 'like', '%' . ucwords($district) . '%')->first();
        if ($id === null) {
            return response()->json([
                'message' => 'District not found',
            ]);
        }

        return $id->id;
    }
    //================================================================================================================================================

    // Duplicate Elder Validation
    public function elderValidation($request)
    {

        // find duplicate elder
        $existElder = Elder::where([
            'name' => $request->name,
            'gender' => $request->gender,
            'birth_year' => $request->birth_year,
            'contact_number' => $request->contact_number,
        ])->first();

        // get all data from request
        $data = $request->all();

        if ($existElder) {
            return response()->json([
                'status' => [
                    'code' => 400,
                    'message' => 'Elder already exists',
                    'data' => [],
                ],
            ]);
        } else {
            return response()->json([
                'status' => [
                    'code' => 200,
                    'message' => 'Elder Validation Success - No duplicate elder found',
                    'data' => $data,
                ],
            ]);
        }
    }

    //Save Elder
    public function createElder($request)
    {
        DB::beginTransaction();
        try {
            $district = District::where('district_name', $request->district)->first();
            $zone_other = null;
            $zone = Zone::where('name', $request->zone)->first();
            if (! $zone) {
                $zone_other = $request->zone;
                $zone_id = Zone::where('code', 'other')->first()->id;
            } else {
                $zone_id = $zone->id;
            }
            $referral = Referral::where('label', $request->source_of_referral)->first();

            if ($request->uid) {
                $newUID = $request->uid;
            } else {
                $newUID = Elder::generateUID($request->case_type, $referral);
            }

            if (! $newUID || ! $district || ! $zone_id || ! $referral) {
                return response()->json([
                    'status' => [
                        'code' => 400,
                        'message' => 'Case Type, District, Refferal, or Zone Not found',
                        'errors' => [],
                    ],
                ]);
            }

            $data = $request->except(['uid']);

            //mapping M/F to male/female
            if ($data['gender'] == 'M' || $data['gender'] == 'm') {
                $data['gender'] = 'male';
            } elseif ($data['gender'] == 'F' || $data['gender'] == 'f') {
                $data['gender'] = 'female';
            }

            if ($request->has('centre_responsible_worker') && ! empty($request->centre_responsible_worker)) {
                $centre = CentreResponsibleWorker::where('name', $request->centre_responsible_worker)->first();
                if (! $centre) {
                    $centre = CentreResponsibleWorker::where('code', 'other')->first();
                    $data['centre_responsible_worker_other'] = $request->centre_responsible_worker;
                }
                $data['centre_responsible_worker_id'] = $centre->id;
            }

            $data['uid'] = $newUID;
            $data['district_id'] = $district->id;
            $data['zone_id'] = $zone_id;
            $data['zone_other'] = $zone_other;
            $data['referral_id'] = $referral->id;
            $elder = Elder::create($data);
            $this->saveHistoryUID($newUID);

            $caseNumber = Cases::max('case_number');

            Cases::create([
                'case_name' => $request->case_type,
                'case_number' => $caseNumber + 1,
                'case_status' => 'Enrolled - ' . $request->case_type,
                'elder_id' => $elder->id,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => [
                    'code' => 500,
                    'message' => 'Failed to enroll elder. Error: ' . $e->getMessage(),
                    'errors' => [],
                ],
            ], 500);
        }

        DB::commit();

        return new ElderSingleResource($elder);
    }

    //=================================================================================================================================================
    public function saveHistoryUID($data)
    {
        RecordUid::create([
            'uid' => $data,
        ]);
    }

    public function updateElder($request, $elder)
    {
        if ($request->district !== null) {
            $newDistrictID = $this->createDistrictID($request->district);
            $request['district_id'] = $newDistrictID;
        }

        if ($request->gender == 'M' || $request->gender == 'm') {
            $request->merge(['gender' => 'male']);
        } elseif ($request->gender == 'F' || $request->gender == 'f') {
            $request->merge(['gender' => 'female']);
        }

        $zone_other = null;
        $zone = Zone::where('name', $request->zone)->first();
        if (! $zone) {
            $zone_other = $request->zone;
            $zone_id = Zone::where('code', 'other')->first()->id;
        } else {
            $zone_id = $zone->id;
        }

        $data['centre_responsible_worker_other'] = null;
        $data['centre_responsible_worker_id'] = null;
        if ($request->has('centre_responsible_worker') && ! empty($request->centre_responsible_worker)) {
            $centre = CentreResponsibleWorker::where('name', $request->centre_responsible_worker)->first();
            if (! $centre) {
                $centre = CentreResponsibleWorker::where('code', 'other')->first();
                $data['centre_responsible_worker_other'] = $request->centre_responsible_worker;
            }
            $data['centre_responsible_worker_id'] = $centre->id;
        }
        if ($request->case_type || $request->source_of_referral) {
            return response()->json([
                'status' => [
                    'code' => 401,
                    'message' => 'Not allowed to edit user type and source of referral',
                    'errors' => [],
                ],
            ]);
        }
        $request->merge([
            'zone_other' => $zone_other,
            'zone_id' => $zone_id,
            'centre_responsible_worker_id' => $data['centre_responsible_worker_id'],
            'centre_responsible_worker_other' => $data['centre_responsible_worker_other'],
        ]);

        $update = [
            'name' => $request->name,
            'name_en' => $request->name_en,
            'gender' => $request->gender,
            'contact_number' => $request->contact_number,
            'second_contact_number' => $request->second_contact_number,
            'third_contact_number' => $request->third_contact_number,
            'address' => $request->address,
            'birth_day' => $request->birth_day,
            'birth_month' => $request->birth_month,
            'birth_year' => $request->birth_year,
            'district' => $request->district,
            'zone' => $request->zone,
            'zone_id' => $request->zone_id,
            'language' => $request->language,
            'centre_case_id' => $request->centre_case_id,
            'centre_responsible_worker_other' => $request->centre_responsible_worker_other,
            'centre_responsible_worker_id' => $request->centre_responsible_worker_id,
            'responsible_worker_contact' => $request->responsible_worker_contact,
            // 'case_type' => $request->case_type,
            // 'source_of_referral' => $request->source_of_referral,
            'relationship' => $request->relationship,
            // 'referral_id' => $request->referral_id,
            'uid_connected_with' => $request->uid_connected_with,
            'emergency_contact_number' => $request->emergency_contact_number,
            'emergency_contact_number_2' => $request->emergency_contact_number_2,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_relationship_other' => $request->emergency_contact_relationship_other,
            'emergency_contact_2_number' => $request->emergency_contact_2_number,
            'emergency_contact_2_number_2' => $request->emergency_contact_2_number_2,
            'emergency_contact_2_name' => $request->emergency_contact_2_name,
            'emergency_contact_2_relationship_other' => $request->emergency_contact_2_relationship_other,
            'elder_remark' => $request->elder_remark,
            'ccec_number' => $request->ccec_number,
            'ccec_number_2' => $request->ccec_number_2,
            'ccec_2_number' => $request->ccec_2_number,
            'ccec_2_number_2' => $request->ccec_2_number_2,
        ];
        $elder->update($update);

        return $elder;
    }

    public function deleteElder($elder)
    {
        $elder->delete();
    }

    public function dateConvertion($data)
    {

        return date('Y-m-d', strtotime($data));
    }

    public function dateForDisplay($data)
    {

        return date('d-m-Y', strtotime($data));
    }

    //================================================================================================================================================

    //Function bulk validation and creation
    public function validateElders($elders, $isCreate = false)
    {
        $success = [];
        $failed = [];
        $duplicates = [];

        for ($i = 0; $i < count($elders); $i++) {
            $elder = $elders[$i];
            //mapping
            if (array_key_exists('related_uid', $elder)) {
                $elder['uid_connected_with'] = $elder['related_uid'];
            }

            $filtered_elder = array_filter($elder);
            $elder_request = ElderRequest::create('', 'POST', $filtered_elder);
            $validator = Validator::make($filtered_elder, $elder_request->rules());
            $elder['no'] = $i + 1;

            $district = District::where('district_name', $elder['district'])->first();
            $zone_other = null;
            $zone = Zone::where('name', $elder['zone'])->first();
            if (! $zone) {
                $zone_other = $elder['zone'];
                $zone_id = Zone::where('code', 'other')->first()->id;
            } else {
                $zone_id = $zone->id;
            }
            $referral = Referral::where('label', $elder['source_of_referral'])->first();
            $newUID = null;
            if ($elder['uid']) {
                $newUID = $elder['uid'];
            } else {
                if ($referral) {
                    $newUID = Elder::generateUID($elder['case_type'], $referral);
                }
            }

            if (! $newUID || ! $district || ! $zone_id || ! $referral) {
                $elder['source_of_referral'] = ! $referral ? 'Invalid' : $elder['source_of_referral'];
                $elder['zone'] = ! $zone_id ? 'Invalid' : $elder['zone'];
                $elder['district'] = ! $district ? 'Invalid' : $elder['district'];
                $elder['uid'] = ! $newUID ? 'Invalid' : $elder['uid'];
                array_push($failed, $elder);
            }

            // Check duplicate data
            $combinedData = [
                'name' => $elder['name'],
                'gender' => $elder['gender'],
                'birth_year' => $elder['birth_year'],
                'contact_number' => $elder['contact_number'],
            ];

            $existingElder = Elder::where($combinedData)->first();

            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $field => $messages) {
                    $elder[$field] = 'Invalid';
                }
                array_push($failed, $elder);
            } elseif ($existingElder) {
                $elder['status'] = 'Duplicate';
                array_push($duplicates, $elder);

                continue;
            } else {
                if ($isCreate) {
                    $elder = $this->createElder($elder_request);
                }
                array_push($success, $elder);
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'duplicates' => $duplicates,
        ];
    }
}
