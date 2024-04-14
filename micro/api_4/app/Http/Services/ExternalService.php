<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExternalService
{

    public function getTodayUserEvents($date)
    {
        try{
            $urlText = "appointments-today";
            if($date !== 'undefined' || $date !== null){
                $urlText = "appointments-today?date=$date";
            }
            $response = Http::acceptJson()->get(env('APPOINTMENTS_SERVICE_API_URL') . '/' . $urlText);
            return $response;
        } catch (\Exception $e){
            return null;
        }
    }

    public function getStaffEvents($userIds){
        try{
            $urlText = "staff-appointments?user_ids=$userIds";
            $appointments = Http::acceptJson()->get(env('APPOINTMENTS_SERVICE_API_URL') . '/' . $urlText);
            $response = $appointments->collect('data');
            return $response;
        } catch (\Exception $e){
            return null;
        }
    }

    public function getStaffCalls($staffNames){
        try{
            $urlText = "staff-calls?by_name=$staffNames";
            $calls = Http::acceptJson()->get(env('ELDER_SERVICE_API_URL') . '/' . $urlText);
            $response = $calls->collect('data');
            return $response;
        } catch (\Exception $e){
            return null;
        }
    }

    public function getStaffCarePlans($staffNames){
        try{
            $urlText = "staff-care-plans?by_name=$staffNames";
            $calls = Http::acceptJson()->get(env('ASSESSMENTS_SERVICE_API_URL') . '/' . $urlText);
            $response = $calls->collect('data');
            return $response;
        } catch (\Exception $e){
            return null;
        }
    }

    public function getCasesStatus(){
        try{
            $urlText = "case-status";
            $case_status = Http::acceptJson()->get(env('ASSESSMENTS_SERVICE_API_URL') . '/' . $urlText);
            $response = $case_status->collect('data');
            return $response;
        } catch (\Exception $e){
            return null;
        }
    }
}
