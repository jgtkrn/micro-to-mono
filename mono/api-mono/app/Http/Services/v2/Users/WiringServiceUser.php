<?php

namespace App\Http\Services\v2\Users;

use App\Models\v2\Appointments\Event;
use App\Models\v2\Appointments\UserEvent;
use App\Models\v2\Assessments\BznConsultationNotes;
use App\Models\v2\Assessments\CarePlan;
use App\Models\v2\Assessments\CaseManager;
use App\Models\v2\Assessments\CgaConsultationNotes;
use App\Models\v2\Elders\Cases;
use App\Models\v2\Elders\ElderCalls;
use Carbon\Carbon;
use Exception;
use stdClass;

class WiringServiceUser
{
    public function getTodayUserEvents($date)
    {
        try {
            if (! $date) {
                return [];
            }
            $from = Carbon::parse($date)->startOfDay();
            $to = Carbon::parse($date)->endOfDay();

            $events = Event::with('user')->where('category_id', 5)->whereDate('start', '<=', $from)->whereDate('end', '>=', $to)->get();
            $data = $events->pluck('user')->collapse()->pluck('user_id')->toArray();

            return $data;
        } catch (Exception $e) {
            return [];
        }
    }

    public function getStaffEvents($from, $to)
    {
        try {
            $eventIds = [];
            $results = new stdClass;
            $userEvents = UserEvent::select(['*']);
            if ($from && $to) {
                $from = Carbon::parse($from)->startOfDay();
                $to = Carbon::parse($to)->endOfDay();
                $eventIds = Event::whereBetween('start', [$from, $to])->get()->pluck('id');
            }
            if (count($eventIds) == 0 && ($from || $to)) {
                return null;
            }
            if (count($eventIds) > 0) {
                $userEvents = $userEvents->whereIn('event_id', $eventIds);
            }

            $userEvents = $userEvents->with([
                'event' => function ($query) {
                    $query->select('id', 'category_id', 'end', 'start')->get();
                },
            ])->select('user_id', 'event_id')->orderBy('created_at', 'asc')->get();
            if (count($userEvents) == 0) {
                return null;
            }

            $user_ids = array_values(array_filter($userEvents->pluck('user_id')->toArray()));
            $results->user_ids = $user_ids;
            for ($i = 0; $i < count($userEvents); $i++) {
                $userId = $userEvents[$i]['user_id'];
                $eventStart = $userEvents[$i]['event']['start'];
                $eventEnd = $userEvents[$i]['event']['end'];
                $diffInHours = $this->getDiffHours($eventStart, $eventEnd);
                if (! property_exists($results, $userId)) {
                    $results->$userId['appointment'] = 1;
                    $results->$userId['followup'] = 0;
                    $results->$userId['meeting'] = 0;
                    $results->$userId['booking'] = 0;
                    $results->$userId['administrative_work'] = 0;
                    if ($userEvents[$i]['event']['category_id'] == 6) {
                        $results->$userId['administrative_work'] = $diffInHours;
                    }
                    if ($userEvents[$i]['event']['category_id'] == 4) {
                        $results->$userId['followup'] = 1;
                    }
                    if ($userEvents[$i]['event']['category_id'] == 3) {
                        $results->$userId['meeting'] = $diffInHours;
                    }
                    if ($userEvents[$i]['event']['category_id'] == 1) {
                        $results->$userId['booking'] = 1;
                    }
                } elseif (property_exists($results, $userId)) {
                    $results->$userId['appointment'] += 1;
                    if ($userEvents[$i]['event']['category_id'] == 6) {
                        $results->$userId['administrative_work'] += $diffInHours;
                    }
                    if ($userEvents[$i]['event']['category_id'] == 4) {
                        $results->$userId['followup'] += 1;
                    }
                    if ($userEvents[$i]['event']['category_id'] == 3) {
                        $results->$userId['meeting'] += $diffInHours;
                    }
                    if ($userEvents[$i]['event']['category_id'] == 1) {
                        $results->$userId['booking'] += 1;
                    }
                }
            }

            return (array) $results;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getDiffHours($from, $to)
    {
        if (! $from || ! $to) {
            return 0;
        }
        $start = Carbon::parse($from);
        $end = Carbon::parse($to);

        return $end->diffInHours($start);
    }

    public function getStaffCalls($staffNames, $from, $to)
    {
        try {
            $by_name = $staffNames ? explode(',', $staffNames) : null;
            $date = null;
            if ($from || $to) {
                $date = [
                    'from' => Carbon::parse($from)->startOfDay() ?? Carbon::parse($to)->startOfDay(),
                    'to' => Carbon::parse($to)->endOfDay() ?? Carbon::parse($from)->endOfDay(),
                ];
            }

            $calls = ElderCalls::select(['id', 'call_date', 'updated_by_name'])
                ->when($by_name, function ($query, $name) {
                    $query->whereIn('updated_by_name', $name);
                })
                ->when(($date), function ($query, $date) {
                    $query->whereBetween('call_date', [$date['from'], $date['to']]);
                })
                ->get();
            $results = new stdClass;
            for ($i = 0; $i < count($calls); $i++) {
                $staff_name = $calls[$i]['updated_by_name'];
                if ($staff_name !== null) {
                    $swipespace = preg_replace('/[^A-Za-z0-9]+/', '_', $staff_name);
                    $snakeCaseStaffName = strtolower($swipespace);
                    $snakeCaseStaffName = trim($snakeCaseStaffName, '_');
                    if (! property_exists($results, $snakeCaseStaffName)) {
                        $results->$snakeCaseStaffName = 1;
                    } elseif (property_exists($results, $snakeCaseStaffName)) {
                        $results->$snakeCaseStaffName += 1;
                    }
                }
            }

            return (array) $results;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getStaffCarePlans($staffNames)
    {
        try {
            $care_plans = CarePlan::select(['id', 'case_manager']);
            if ($staffNames) {
                $staffNames = explode(',', $staffNames);
                $care_plans = $care_plans->whereIn('case_manager', $staffNames);
            }
            $care_plans = $care_plans->without(['coachingPam', 'bznNotes', 'cgaNotes'])
                ->get();
            if (count($care_plans) == 0) {
                return response()->json(['data' => null], 404);
            }
            $results = new stdClass;
            for ($i = 0; $i < count($care_plans); $i++) {
                $staffName = $care_plans[$i]['case_manager'];
                if ($staffName !== null) {
                    $swipespace = preg_replace('/[^A-Za-z0-9]+/', '_', $staffName);
                    $snakeCaseStaffName = strtolower($swipespace);
                    $snakeCaseStaffName = trim($snakeCaseStaffName, '_');
                    if (! property_exists($results, $staffName)) {
                        $results->$snakeCaseStaffName = 1;
                    } elseif (property_exists($results, $staffName)) {
                        $results->$snakeCaseStaffName += 1;
                    }
                }
            }

            return (array) $results;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getCasesStatus()
    {
        try {
            $cases = Cases::select('id', 'case_status')->get();
            if (count($cases) == 0) {
                return response()->json([
                    'data' => null,
                ], 404);
            }

            $on_going_keys = ['On going', 'Baseline Completed', 'Enrolled - BZN', 'Enrolled - CGA', 'Enrolled-BZN', 'Enrolled-CGA'];
            $pending_keys = ['Pending', 'Pending for waiting 1st visit'];
            $finished_keys = ['Reject', 'Dropout', 'Completed'];
            $result = new stdClass;
            for ($i = 0; $i < count($cases); $i++) {
                $case_id = $cases[$i]['id'];

                if (! property_exists($result, $cases[$i]['id'])) {
                    $result->$case_id['on_going'] = 0;
                    $result->$case_id['pending'] = 0;
                    $result->$case_id['finished'] = 0;
                    $result->$case_id['total_visit'] = 0;
                    if (in_array($cases[$i]['case_status'], $on_going_keys)) {
                        $result->$case_id['on_going'] = 1;
                        $result->$case_id['total_visit'] = 1;
                    } elseif (in_array($cases[$i]['case_status'], $pending_keys)) {
                        $result->$case_id['pending'] = 1;
                        $result->$case_id['total_visit'] = 1;
                    } elseif (in_array($cases[$i]['case_status'], $finished_keys)) {
                        $result->$case_id['finished'] = 1;
                        $result->$case_id['total_visit'] = 1;
                    }

                } elseif (property_exists($result, $cases[$i]['id'])) {
                    if (in_array($cases[$i]['case_status'], $on_going_keys)) {
                        $result->$case_id['on_going'] = +1;
                        $result->$case_id['total_visit'] = +1;
                    } elseif (in_array($cases[$i]['case_status'], $pending_keys)) {
                        $result->$case_id['pending'] = +1;
                        $result->$case_id['total_visit'] = +1;
                    } elseif (in_array($cases[$i]['case_status'], $finished_keys)) {
                        $result->$case_id['finished'] = +1;
                        $result->$case_id['total_visit'] = +1;

                    }
                }
            }

            return (array) $result;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getUserCaseStatus()
    {
        try {
            $caseStatus = $this->getCasesStatus();

            return $this->getManagersCaseStatus($caseStatus);
        } catch (Exception $e) {
            return null;
        }
    }

    public function getManagerCaseStatus($caseStatus)
    {
        $managers = CarePlan::select(['case_id', 'manager_id'])
            ->without([
                'bznNotes', 'cgaNotes', 'coachingPam',
            ])->get();
        $result = new stdClass;
        if (count($managers) == 0) {
            return $result;
        }
        for ($i = 0; $i < count($managers); $i++) {
            $case_id = $managers[$i]['case_id'] ?? null;
            if (! $case_id) {
                continue;
            }
            $user_id = $managers[$i]['manager_id'] ?? null;
            if (! $user_id) {
                continue;
            }
            $case_status = $caseStatus[$case_id] ?? null;
            if (! $case_status) {
                continue;
            }

            if (! property_exists($result, $user_id)) {
                $result->$user_id['total_visit'] = $case_status['total_visit'] ?? 0;
                $result->$user_id['on_going'] = $case_status['on_going'] ?? 0;
                $result->$user_id['pending'] = $case_status['pending'] ?? 0;
                $result->$user_id['finished'] = $case_status['finished'] ?? 0;
            } elseif (property_exists($result, $user_id)) {
                $result->$user_id['total_visit'] += $case_status['total_visit'];
                $result->$user_id['on_going'] += $case_status['on_going'];
                $result->$user_id['pending'] += $case_status['pending'];
                $result->$user_id['finished'] += $case_status['finished'];
            }
        }

        return $result;
    }

    public function getManagersCaseStatus($caseStatus)
    {
        $managers = CaseManager::with('carePlan')->with([
            'carePlan' => function ($query) {
                return $query->select('case_id', 'id')->get();
            },
        ])->get();
        $result = $this->getManagerCaseStatus($caseStatus);
        if (count($managers) == 0) {
            return $result;
        }
        for ($i = 0; $i < count($managers); $i++) {
            $carePlan = $managers[$i]->carePlan ?? null;
            if (! $carePlan) {
                continue;
            }
            $case_id = $carePlan->case_id ?? null;
            if (! $case_id) {
                continue;
            }
            $user_id = $managers[$i]->manager_id ?? null;
            if (! $user_id) {
                continue;
            }
            $case_status = $caseStatus[$case_id] ?? null;
            if (! $case_status) {
                continue;
            }

            if (! property_exists($result, $user_id)) {
                $result->$user_id['total_visit'] = $case_status['total_visit'] ?? 0;
                $result->$user_id['on_going'] = $case_status['on_going'] ?? 0;
                $result->$user_id['pending'] = $case_status['pending'] ?? 0;
                $result->$user_id['finished'] = $case_status['finished'] ?? 0;

            } elseif (property_exists($result, $user_id)) {
                $result->$user_id['total_visit'] += $case_status['total_visit'];
                $result->$user_id['on_going'] += $case_status['on_going'];
                $result->$user_id['pending'] += $case_status['pending'];
                $result->$user_id['finished'] += $case_status['finished'];
            }
        }

        return (array) $result;
    }

    public function getCaseHour($from = null, $to = null)
    {
        $results = new stdClass;
        $bznNotes = BznConsultationNotes::select('id', 'bzn_target_id', 'visiting_duration', 'visit_type', 'assessment_date');
        if ($from && $to) {
            $from = Carbon::parse($from)->startOfDay();
            $to = Carbon::parse($to)->endOfDay();
            $bznNotes = $bznNotes->whereBetween('assessment_date', [$from, $to]);
        }
        $bznNotes = $bznNotes->whereNotNull('visiting_duration')->with([
            'carePlan' => function ($query) {
                $query->select('id', 'care_plan_id')->without('bznConsultationNotes')->get();
            },
        ])->get();
        $cgaNotes = CgaConsultationNotes::select('id', 'cga_target_id', 'visiting_duration', 'visit_type', 'assessment_date');
        if ($from && $to) {
            $from = Carbon::parse($from)->startOfDay();
            $to = Carbon::parse($to)->endOfDay();
            $cgaNotes = $cgaNotes->whereBetween('assessment_date', [$from, $to]);
        }
        $cgaNotes = $cgaNotes->whereNotNull('visiting_duration')->with([
            'carePlan' => function ($query) {
                $query->select('id', 'care_plan_id')->without('cgaConsultationNotes')->get();
            },
        ])->get();
        if (count($bznNotes) == 0 && count($cgaNotes) == 0) {
            return null;
        }
        $results = new stdClass;
        for ($i = 0; $i < count($bznNotes); $i++) {
            $staffId = $this->extractStaffIdFromNotes($bznNotes[0]);
            $caseHour = $this->extractCaseHour($bznNotes[0])['total_phone'];
            if (! property_exists($results, $staffId)) {
                $results->$staffId = $caseHour;
            } else {
                $results->$staffId += $caseHour;
            }
        }
        for ($i = 0; $i < count($cgaNotes); $i++) {
            $staffId = $this->extractStaffIdFromNotes($cgaNotes[0]);
            $caseHour = $this->extractCaseHour($cgaNotes[0])['total_phone'];
            if (! property_exists($results, $staffId)) {
                $results->$staffId = $caseHour;
            } else {
                $results->$staffId += $caseHour;
            }
        }

        return $results;
    }

    public function extractStaffIdFromNotes($notes)
    {
        $carePlanFirstLevel = $notes->carePlan;
        if (! $carePlanFirstLevel) {
            return null;
        }
        $carePlanSecondLevel = $carePlanFirstLevel->carePlan;
        if (! $carePlanSecondLevel) {
            return null;
        }
        $manager_id = $carePlanSecondLevel->manager_id;
        if (! $manager_id) {
            return null;
        }

        return $manager_id;
    }

    public function extractCaseHour($notes)
    {
        $result = [
            'contact_total_number' => $notes->visit_type == 'Onsite' ? (int) $notes->visiting_duration : 0,
            'case_phone_contact' => $notes->visit_type == 'Phone' ? (int) $notes->visiting_duration : 0,
            'total_phone' => (int) $notes->visiting_duration,
        ];

        return $result;
    }
}
