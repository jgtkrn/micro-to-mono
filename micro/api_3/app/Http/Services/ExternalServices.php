<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ExternalServices
{
    // public function getAssessmentData($token)
    public function getAssessmentData($token, $id)
    {
        try {
            // $response = Http::acceptJson()->withToken($token)->get(env('ASSESSMENTS_SERVICE_API_URL') . '/medication-histories');
            // return $response->collect('data');
            $medication = Http::acceptJson()->withToken($token)->get(env('ASSESSMENTS_SERVICE_API_URL') . '/medication-histories');
            $med_data = $medication->collect('data');
            $result = [];
            for ($i = 0; $i < count($med_data); $i++) {
                if ($med_data[$i]['case_id'] == $id) {
                    $result[0] = $med_data[$i];
                }
            }
            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getCarePlanData($token)
    {
        try{
            $care_plan = Http::acceptJson()->withToken($token)->get(env('ASSESSMENTS_SERVICE_API_URL') . '/care-plans');
            $result = $care_plan->collect('data');
            return $result->keyBy('case_id');
        } catch (\Exception $e){
            return [];
        }
    }

    public function getCarePlanForReport($cases_id)
    {
        try{
            $care_plan = Http::acceptJson()->get(env('ASSESSMENTS_SERVICE_API_URL') . '/care-plans-report');
            $result = $care_plan->collect('data');
            if(count($result) == 0){
                return $result;
            }
            return $result;
        } catch(\Exception $e) {
            return null;
        }
    }

    public function getAppointmentsForReport($from, $to){
        try {
            $appointments = Http::acceptJson()->get(env('APPOINTMENTS_SERVICE_API_URL') . '/appointments-report' . "?from=$from&to=$to");
            $result = $appointments->collect('data');
            if(count($result) == 0){
                return null;
            }
            return $result;
        } catch(\Exception $e){
            return null;
        }
    }

    public function getCaseManager($caseId){
        try {
            $carePlan = Http::acceptJson()->get(env('ASSESSMENTS_SERVICE_API_URL') . '/case-manager' . "?case_id=$caseId");
            $result = $carePlan->collect('data');
            if(!$result){
                return null;
            }
            $data = [
                'id' => $result['manager_id'] ? $result['manager_id'] : null, 
                'managers' => !$result['case_managers'] 
                            ? [] 
                            : collect($result['case_managers'])->pluck('manager_id')->toArray()
            ];
            return $data;
        } catch(\Exception $e){
            return null;
        }
    }

    public function getElderAppointments(){
        try {
            $appointments = Http::acceptJson()->get(env('APPOINTMENTS_SERVICE_API_URL') . '/elder-appointments');
            $result = $appointments->collect('data');
            if(count($result) == 0){
                return null;
            }
            return $result;
        } catch(\Exception $e){
            return null;
        }
    }
}
