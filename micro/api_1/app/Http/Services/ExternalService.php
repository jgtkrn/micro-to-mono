<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Event;

class ExternalService
{
    public function getUsersData($token, $user_ids, $team_ids = null)
    {
        try {        
            $params = [];
            if ($user_ids) {
                array_push($params, 'ids=' . implode(',', $user_ids));
            }
            if ($team_ids) {
                array_push($params, 'team_ids=' . $team_ids);
            }
            $per_page = $user_ids ? count($user_ids) : 10000; //get all from team
            array_push($params, 'per_page=' . $per_page);

            $response = Http::acceptJson()->withToken($token)->get(env('USER_SERVICE_API_URL') . '/users/autocompletenew?' . implode('&', $params));
            return $response->collect('data');
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getUserByEmail($email)
    {
        try {        
            $response = Http::acceptJson()->get(env('USER_SERVICE_API_URL') . '/check-email' . "?email=$email");
            return $response->collect('data');
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getEldersData($elder_ids)
    {
        try {        
            $params = [];
            array_push($params, 'ids=' . implode(',', $elder_ids));
            $per_page = count($elder_ids);
            array_push($params, 'per_page=' . $per_page);

            $response = Http::acceptJson()->get(env('ELDER_SERVICE_API_URL') . '/elders?' . implode('&', $params));
            $elder_collection =  $response->collect('data')->map(function ($item) {
                return collect($item)->only(['id', 'name', 'uid', 'contact_number', 'second_contact_number', 'third_contact_number', 'address', 'case_type', 'elder_remark']);
            });

            return $elder_collection->groupBy('id');
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getEldersDataBySearch($search, $case_type)
    {
        try {
            $params = [];
            $elder_ids = array_filter(array_unique(Event::all()->pluck('elder_id')->toArray()));
            array_push($params, 'ids=' . implode(',', $elder_ids));
            array_push($params, 'uid=' . $search);
            array_push($params, 'case_type=' . $case_type);
            $per_page = count($elder_ids);
            array_push($params, 'per_page=' . $per_page);

            $response = Http::acceptJson()->get(env('ELDER_SERVICE_API_URL') . '/elders-autocomplete?' . implode('&', $params));
            return $response->collect('data');
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getElderFromEvent($elderId) {
        try {
            $response = Http::acceptJson()->get(env('ELDER_SERVICE_API_URL') . '/elder-event' . "?elder_id=$elderId");
            $result = $response->collect('data');
            if(count($result) === 0){
                return null;
            }
            return $result;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getUsersFromEvent($userIds) {
        try {
            $response = Http::acceptJson()->get(env('USER_SERVICE_API_URL') . '/user-event' . "?userIds=$userIds");
            $result = $response->collect('data');
            if(count($result) === 0){
                return null;
            }
            return $result;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getManyElderForEventList($elderIds){
        try {
            $response = Http::acceptJson()->get(env('ELDER_SERVICE_API_URL') . '/many-elder-event?' . "elderIds=$elderIds");
            $elder_collection =  $response->collect('data');
            return $elder_collection;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getUidSet($token){
        try {
            $response = Http::connectTimeout(1)->acceptJson()->withToken($token)->get(env('ELDER_SERVICE_API_URL') . '/cases-uid-set');
            $result = $response->collect('data');
            if(!$result) {
                return null;
            }
            return $result;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getCaseManagerSet($token){
        try {
            $response = Http::connectTimeout(1)->acceptJson()->withToken($token)->get(env('ASSESSMENTS_SERVICE_API_URL') . '/case-manager-set');
            $result = $response->collect('data');
            if(!$result) {
                return null;
            }
            return $result;
        } catch (\Exception $e) {
            return null;
        }
    }
}
