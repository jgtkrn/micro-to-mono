<?php

namespace App\Http\Services\v2\Elders;

use App\Models\v2\Appointments\Event;
use App\Models\v2\Assessments\BznConsultationNotes;
use App\Models\v2\Assessments\CarePlan;
use App\Models\v2\Assessments\CgaConsultationNotes;
use App\Models\v2\Assessments\MedicationHistory;
use Carbon\Carbon;
use Exception;
use stdClass;

class WiringServiceElder
{
    public function getAssessmentData($caseId)
    {
        try {
            $medication = MedicationHistory::where('case_id', $caseId)->first();
            $result = [];
            if ($medication) {
                $result[0] = $medication;
            }

            return $result;
        } catch (Exception $e) {
            return [];
        }
    }

    public function getCarePlanData()
    {
        try {
            $result = CarePlan::orderBy('created_at', 'desc')->get();
            if (! $result) {
                return [];
            }

            return $result->keyBy('case_id');
        } catch (Exception $e) {
            return [];
        }
    }

    public function getCarePlanForReport($cases_id)
    {
        try {
            $care_plans = CarePlan::select(['id', 'case_id', 'case_manager', 'manager_id']);
            if ($cases_id) {
                $cases_id = explode(',', $cases_id);
                $care_plans = $care_plans->whereIn('case_id', $cases_id);
            }
            $care_plans = $care_plans->without(['coachingPam', 'bznNotes', 'cgaNotes'])
                ->with([
                    'bznCareTarget' => function ($query) {
                        $query->select(['care_plan_id', 'id'])->without('bznConsultationNotes')->get();
                    },
                    'cgaCareTarget' => function ($query) {
                        $query->select(['care_plan_id', 'id'])->without('cgaConsultationNotes')->get();
                    },
                ])
                ->get();
            if (count($care_plans) == 0) {
                return null;
            }
            $results = new stdClass;
            for ($i = 0; $i < count($care_plans); $i++) {
                $case_id = $care_plans[$i]['case_id'];
                if ($case_id !== null && ! property_exists($results, $case_id)) {
                    $results->$case_id['care_plan_id'] = $care_plans[$i]['id'];
                    $results->$case_id['case_id'] = $care_plans[$i]['case_id'];
                    $results->$case_id['case_manager'] = $care_plans[$i]['case_manager'];
                    $results->$case_id['bzn_care_target'] = $care_plans[$i]['bznCareTarget'];
                    $results->$case_id['cga_care_target'] = $care_plans[$i]['cgaCareTarget'];
                }
            }

            return (array) $results;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getAppointmentsForReport($from, $to)
    {
        try {
            $events = Event::select('elder_id');
            if ($from && $to) {
                $from = Carbon::parse($from)->startOfDay();
                $to = Carbon::parse($to)->endOfDay();
                $events = $events->whereBetween('end', [$from, $to])->orderBy('end', 'asc');
            }
            $events = $events->get();
            if (! $events) {
                return null;
            }
            $elder_ids = array_values(array_filter($events->pluck('elder_id')->toArray()));

            $elderEvents = Event::select(['elder_id', 'start', 'end', 'category_id'])->whereIn('elder_id', $elder_ids)->get();
            if (! $elderEvents) {
                return null;
            }
            $results = new stdClass;
            for ($i = 0; $i < count($elderEvents); $i++) {
                $elder_id = $elderEvents[$i]['elder_id'];
                if (! property_exists($results, $elder_id)) {
                    $results->$elder_id['elder_id'] = $elderEvents[$i]['elder_id'];
                    $results->$elder_id['face_visit'] = $elderEvents[$i]['category_id'] == 2 ? 1 : 0;
                    $results->$elder_id['tele_visit'] = $elderEvents[$i]['category_id'] == 4 ? 1 : 0;
                    $results->$elder_id['first_visit'] = $elderEvents[$i]['start'] !== null ? date('Y-m-d', strtotime($elderEvents[$i]['start'])) : null;
                    $results->$elder_id['last_visit'] = $elderEvents[$i]['end'] !== null ? date('Y-m-d', strtotime($elderEvents[$i]['end'])) : null;
                    $results->$elder_id['patient_care_hour'] = 0;
                    if ($elderEvents[$i]['start'] && $elderEvents[$i]['end']) {
                        $full_time = (strtotime($elderEvents[$i]['end']) - strtotime($elderEvents[$i]['start'])) / 3600;
                        $results->$elder_id['patient_care_hour'] += floor($full_time);
                    }
                }
                if (property_exists($results, $elder_id)) {
                    $results->$elder_id['elder_id'] = $elderEvents[$i]['elder_id'];
                    $results->$elder_id['face_visit'] = $elderEvents[$i]['category_id'] == 2 ? $results->$elder_id['face_visit'] += 1 : $results->$elder_id['face_visit'];
                    $results->$elder_id['tele_visit'] = $elderEvents[$i]['category_id'] == 4 ? $results->$elder_id['tele_visit'] += 1 : $results->$elder_id['tele_visit'];
                    $results->$elder_id['first_visit'] = $elderEvents[$i]['start'] !== null ? ($results->$elder_id['first_visit'] > $elderEvents[$i]['start'] ? date('Y-m-d', strtotime($elderEvents[$i]['start'])) : $results->$elder_id['first_visit']) : $results->$elder_id['first_visit'];
                    $results->$elder_id['last_visit'] = $elderEvents[$i]['end'] !== null ? ($results->$elder_id['last_visit'] < $elderEvents[$i]['end'] ? date('Y-m-d', strtotime($elderEvents[$i]['end'])) : $results->$elder_id['last_visit']) : $results->$elder_id['last_visit'];
                    if ($elderEvents[$i]['start'] && $elderEvents[$i]['end']) {
                        $full_time = (strtotime($elderEvents[$i]['end']) - strtotime($elderEvents[$i]['start'])) / 3600;
                        $results->$elder_id['patient_care_hour'] += floor($full_time);
                    }
                }
            }
            $results->elder_ids = $elder_ids;

            return (array) $results;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getCaseManager($caseId)
    {
        try {
            if (! $caseId) {
                return null;
            }

            $result = CarePlan::select(['id', 'case_id', 'case_manager', 'manager_id'])->without(['coachingPam', 'bznNotes', 'cgaNotes'])->where('case_id', $caseId)->with('caseManagers')->first();
            if (! $result) {
                return null;
            }

            if ($result->case_manager === null) {
                return null;
            }

            $data = [
                'id' => $result['manager_id'] ? $result['manager_id'] : null,
                'managers' => ! $result['case_managers']
                            ? []
                            : collect($result['case_managers'])->pluck('manager_id')->toArray(),
            ];

            return $data;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getElderAppointments($from = null, $to = null)
    {
        try {
            $results = new stdClass;
            $event = Event::select('start', 'end', 'case_id', 'category_id');
            if ($from && $to) {
                $event = $event->whereBetween('start', [$from, $to]);
            }
            $event = $event->whereNotNull('case_id')->get();
            if (count($event) == 0) {
                return null;
            }

            for ($i = 0; $i < count($event); $i++) {
                $caseId = $event[$i]->case_id;
                if (! property_exists($results, $caseId)) {
                    $results->$caseId['case_phone_contact'] = 0;
                    $results->$caseId['contact_total_number'] = 0;
                    if ($event[$i]['category_id'] == 4) {
                        $results->$caseId['case_phone_contact'] = 0;
                    }
                    if ($event[$i]['category_id'] == 1 || $event[$i]['category_id'] == 2) {
                        $results->$caseId['contact_total_number'] = 0;
                    }
                } elseif (property_exists($results, $caseId)) {
                    if ($event[$i]['category_id'] == 4) {
                        $full_time = (strtotime($event[$i]['end']) - strtotime($event[$i]['start'])) / 3600;
                        $results->$caseId['case_phone_contact'] += floor($full_time);
                    }
                    if ($event[$i]['category_id'] == 1 || $event[$i]['category_id'] == 2) {
                        $full_time = (strtotime($event[$i]['end']) - strtotime($event[$i]['start'])) / 3600;
                        $results->$caseId['contact_total_number'] += floor($full_time);
                    }
                }
            }

            return (array) $results;
        } catch (Exception $e) {
            return null;
        }
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
            $caseId = $this->extractCaseIdFromNotes($bznNotes[$i]);
            $contact_total_number = $this->extractCaseHour($bznNotes[$i])['contact_total_number'];
            $case_phone_contact = $this->extractCaseHour($bznNotes[$i])['case_phone_contact'];
            $visit_type = $bznNotes[$i]->visit_type;
            $target_id = $this->extractCaseHour($bznNotes[$i])['target_id'];
            if (! property_exists($results, $caseId)) {
                $results->$caseId['contact_total_number'] = $contact_total_number;
                $results->$caseId['case_phone_contact'] = $case_phone_contact;
                $results->$caseId['target_id'] = $target_id;
                $results->$caseId['visit_type'] = $visit_type;
                $results->$caseId['is_phone'] = $visit_type == 'Phone' ? true : false;
                $results->$caseId['is_onsite'] = $visit_type == 'Onsite' ? true : false;
            } else if($target_id < $results->$caseId['target_id']) {
                $results->$caseId['contact_total_number'] = $results->$caseId['is_onsite'] ? $contact_total_number : $results->$caseId['contact_total_number'];
                $results->$caseId['case_phone_contact'] = $results->$caseId['is_phone'] ? $case_phone_contact : $results->$caseId['case_phone_contact'];
            } else if($target_id >= $results->$caseId['target_id']) {
                $results->$caseId['contact_total_number'] = $results->$caseId['is_onsite'] ? $results->$caseId['contact_total_number'] : $contact_total_number;
                $results->$caseId['case_phone_contact'] = $results->$caseId['is_phone'] ? $results->$caseId['case_phone_contact'] : $case_phone_contact;
                $results->$caseId['is_phone'] = true;
                $results->$caseId['is_onsite'] = true;
            }
        }
        for ($i = 0; $i < count($cgaNotes); $i++) {
            $caseId = $this->extractCaseIdFromNotes($cgaNotes[$i]);
            $contact_total_number = $this->extractCaseHour($cgaNotes[$i])['contact_total_number'];
            $case_phone_contact = $this->extractCaseHour($cgaNotes[$i])['case_phone_contact'];
            if (! property_exists($results, $caseId)) {
                $results->$caseId['contact_total_number'] = $contact_total_number;
                $results->$caseId['case_phone_contact'] = $case_phone_contact;
            } else {
                $results->$caseId['contact_total_number'] += $contact_total_number;
                $results->$caseId['case_phone_contact'] += $case_phone_contact;
            }
        }

        return $results;
    }

    public function extractCaseIdFromNotes($notes)
    {
        $carePlanFirstLevel = $notes->carePlan;
        if (! $carePlanFirstLevel) {
            return null;
        }
        $carePlanSecondLevel = $carePlanFirstLevel->carePlan;
        if (! $carePlanSecondLevel) {
            return null;
        }
        $case_id = $carePlanSecondLevel->case_id;
        if (! $case_id) {
            return null;
        }

        return $case_id;
    }

    public function extractCaseHour($notes)
    {
        $result = [
            'contact_total_number' => $notes->visit_type == 'Onsite' ? (int) $notes->visiting_duration : 0,
            'case_phone_contact' => $notes->visit_type == 'Phone' ? (int) $notes->visiting_duration : 0,
            'is_phone' => $notes->visit_type == 'Phone' ? true : false,
            'target_id' => $notes->carePlan ?  $notes->carePlan->id : null
        ];

        return $result;
    }
    
}
