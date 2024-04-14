<?php

namespace App\Http\Services\v2\Appointments;

use App\Models\v2\Assessments\CarePlan;
use App\Models\v2\Elders\Cases;
use App\Models\v2\Elders\Elder;
use App\Models\v2\Users\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use stdClass;

class WiringServiceAppointment
{
    public function getUsersData($userIds, $teamIds = null)
    {
        try {
            $users = User::query()
                ->select('id', 'nickname', 'email')
                ->when($teamIds, function ($query, $teamIds) {
                    $teamIdList = explode(',', $teamIds);
                    $query->whereHas('teams', function (Builder $team) use ($teamIdList) {
                        $team->whereIn('teams.id', $teamIdList);
                    });
                })
                ->when($userIds, function ($query, $userIds) {
                    $query->whereIn('id', $userIds);
                })
                ->orderBy('nickname')
                ->get();

            return $users;
        } catch (Exception $e) {
            return [];
        }
    }

    public function getEldersData($elder_ids)
    {
        try {
            $elders = Elder::select(['id', 'name', 'uid', 'contact_number', 'second_contact_number', 'third_contact_number', 'address', 'case_type', 'elder_remark']);
            if (! $elders) {
                return null;
            }
            if ($elder_ids && count($elder_ids) > 0) {
                $elders->whereIn('id', $elder_ids);
            }
            $elders = $elders->get();
            $response = new stdClass;
            for ($i = 0; $i < count($elders); $i++) {
                $key = $elders[$i]->id;
                if (! isset($response->$key)) {
                    $response->$key = $elders[$i];
                }
            }

            return $response;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getUserByEmail($email)
    {
        try {
            $response = User::select(['id', 'email'])->where('email', $email)->get();
            if (! $response || count($response) == 0) {
                return [];
            }

            return $response->toArray();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getEldersDataBySearch($search, $case_type)
    {
        try {
            $response = Elder::select('id', 'uid', 'case_type')
                ->when($search, function ($query, $search) {
                    $query->where('uid', $search);
                })
                ->when($case_type, function ($query, $case_type) {
                    $query->where('case_type', $case_type);
                })
                ->get();

            return $response;
        } catch (Exception $e) {
            return [];
        }
    }

    public function getElderFromEvent($elderId)
    {
        try {
            $elder = Elder::select([
                'id',
                'name',
                'uid',
                'case_type',
                'contact_number',
                'second_contact_number',
                'third_contact_number',
                'address',
                'elder_remark',
            ])->where('id', $elderId)->with([
                'cases' => function ($query) {
                    $query->select(['id', 'elder_id'])->oldest()->first();
                },
            ])->first();
            if (! $elder) {
                return null;
            }
            $elder->case_id = (count($elder->cases) == 0) ? null : $elder->cases[0]->id;
            unset($elder->cases);

            return $elder;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getUsersFromEvent($userIds)
    {
        try {
            $users = User::select(['id', 'name', 'email'])->whereIn('id', $userIds)->orderBy('id', 'asc')->get();
            if (count($users) == 0) {
                return null;
            }

            return $users;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getManyElderForEventList($elderIds)
    {
        try {
            $ids = explode(',', $elderIds);
            $elders = Elder::select([
                'id',
                'name',
                'uid',
                'case_type',
                'contact_number',
                'second_contact_number',
                'third_contact_number',
                'address',
                'elder_remark',
            ])
                ->whereIn('id', $ids)
                ->get();
            $result = new stdClass;
            if (count($elders) == 0) {
                return null;
            }
            for ($i = 0; $i < count($elders); $i++) {
                $elderId = $elders[$i]->id;
                if (! property_exists($result, $elderId)) {
                    $result->$elderId = $elders[$i];
                }
            }

            return (array) $result;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getUidSet()
    {
        try {
            $cases = Cases::with('elder')->get();
            if (count($cases) == 0) {
                return null;
            }
            $result = new stdClass;
            for ($i = 0; $i < count($cases); $i++) {
                $casesId = $cases[$i]->id;
                if (! property_exists($result, $casesId)) {
                    $result->$casesId = ['uid' => $cases[$i]['elder'] ? $cases[$i]['elder']['uid'] : null];
                }
            }

            return (array) $result;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getCaseManagerSet()
    {
        try {
            $care_plans = CarePlan::select(['id', 'case_id', 'case_manager'])->get();
            if (! $care_plans) {
                return null;
            }
            $result = new stdClass;
            for ($i = 0; $i < count($care_plans); $i++) {
                $caseId = $care_plans[$i]->case_id;
                if (
                    ! isset($result->$caseId) &&
                    $caseId !== null &&
                    $care_plans[$i]->case_manager !== null
                ) {
                    $result->$caseId['case_manager'] = $care_plans[$i]->case_manager;
                }
            }

            return (array) $result;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getCasesByElderId($elderId)
    {
        $cases = Cases::where('elder_id', $elderId)->get();
        if (! $cases || count($cases) == 0) {
            return [];
        }

        return $cases->toArray();
    }
}
