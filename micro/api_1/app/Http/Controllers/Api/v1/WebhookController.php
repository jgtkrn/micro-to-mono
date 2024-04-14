<?php

namespace App\Http\Controllers\Api\v1;

use App\Exports\AppointmentExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;
use App\Http\Services\AppointmentService;
use App\Http\Services\ExternalService;
use App\Rules\CarbonTime;
use App\Models\Event;
use App\Models\UserEvent;
use App\Notifications\AppointmentInvitation;
use App\Rules\AttendeeVerification;
use App\Rules\FileExist;
use App\Utils\EventUtil;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class WebhookController extends Controller
{
    use RespondsWithHttpStatus;
    private $appointmentService;
    private $eventUtil;
    private $externalService;

    public function __construct()
    {
        $this->appointmentService = new AppointmentService();
        $this->eventUtil = new EventUtil();
        $this->externalService = new ExternalService();
    }

    public function reshape($payload, $submitDate)
    {
        $user_ids = [];
        $email = null;
        $employee = $payload['employee'];
        if($employee) {
            $email = $employee['email']; 
        }
        $user_ids = ($email == null) ? [] : $this->externalService->getUserByEmail($email);
        
        $data = [
            'created_by' => 1,
            'updated_by' => 1,
            'created_by_name' => 'Automatic System',
            'updated_by_name' => 'Automatic System',
            "title" => $payload['leaveType'],
            "day_date" => $submitDate,
            "start_time" => "00:00",
            "end_time" => "23:59",
            'remark' => $payload['remarks'],
            'case_id' => null,
            'elder_id' => null,
            'category_id' => 5,
            'user_ids' => array($user_ids['id'])
        ];
        return $data;
    }

    public function checkExistEvent($formattedDate, $userId){
        $startDate = Carbon::parse($formattedDate)->startOfDay();
        $endDate = Carbon::parse($formattedDate)->endOfDay();

        $userHasEventInRange = UserEvent::whereHas('event', function ($query) use ($startDate, $endDate) {
            $query->whereDate('start', '>=', $startDate)
                ->whereDate('end', '<=', $endDate);
        })
        ->where('user_id', $userId)
        ->first();
        $appointment = null;
        if($userHasEventInRange) {
            $appointment = Event::where('id', $userHasEventInRange->event_id)->first();
        }

        $data = [
            'status' => (!$userHasEventInRange) ? false : true,
            'appointment' => ($appointment != null) ? $appointment : null
        ];

        return $data;
    }



    public function store(Request $request)
    {
        switch ($request->context) {
            case 'create.leave':
                try {
                    $results = [];
                    if($request->payload){
                        $startDateString = $request->payload['leaveDate'];
                        $endDateString = $request->payload['backToWork'];

                        $startDate = Carbon::parse($startDateString);
                        $endDate = Carbon::parse($endDateString);

                        while ($startDate->lte($endDate)) {
                            $formattedDate = $startDate->format('Y-m-d');
                            $data = $this->reshape($request->payload, $formattedDate);
                            $request->merge($data);
                            $exists = $this->checkExistEvent($formattedDate, $data['user_ids'][0]);
                            if($exists['status'] == false){
                                $result = $this->appointmentService->store($request);
                                array_push($results, $result);
                            }
                            $startDate->addDay();
                        }
                        return response()->json(['data' => $results, 'message' => 'Success create leave'], 201);
                    }
                    return response()->json(['data' => null, 'message' => 'Please use the proper payload.'], 400);
                } catch (Exception $e) {
                    return response()->json(['data' => null, 'message' => $e], 500);
                }
                
                break;
            case 'update.leave':
                try {
                    $results = [];
                    if($request->payload){
                        $startDateString = $request->payload['leaveDate'];
                        $endDateString = $request->payload['backToWork'];

                        $startDate = Carbon::parse($startDateString);
                        $endDate = Carbon::parse($endDateString);

                        while ($startDate->lte($endDate)) {
                            $formattedDate = $startDate->format('Y-m-d');
                            $data = $this->reshape($request->payload, $formattedDate);
                            $request->merge($data);
                            $exists = $this->checkExistEvent($formattedDate, $data['user_ids'][0]);
                            if($exists['status'] == false){
                                $result = $this->appointmentService->store($request);
                                array_push($results, $result);
                            }
                            if($exists['status'] == true){
                                if($exists['appointment'] != null){
                                    $result = $this->appointmentService->update($request, $exists['appointment']);
                                    array_push($results, $result);
                                }
                            }
                            $startDate->addDay();
                        }
                        return response()->json(['data' => $results, 'message' => 'Success update leave'], 201);
                    }
                    return response()->json(['data' => null, 'message' => 'Please use the proper payload.'], 400);
                } catch (Exception $e) {
                    return response()->json(['data' => null, 'message' => $e], 500);
                }
                break;
            case 'delete.leave':
                try {
                    $results = [];
                    if($request->payload){
                        $startDateString = $request->payload['leaveDate'];
                        $endDateString = $request->payload['backToWork'];

                        $startDate = Carbon::parse($startDateString);
                        $endDate = Carbon::parse($endDateString);

                        while ($startDate->lte($endDate)) {
                            $formattedDate = $startDate->format('Y-m-d');
                            $data = $this->reshape($request->payload, $formattedDate);
                            $request->merge($data);
                            $exists = $this->checkExistEvent($formattedDate, $data['user_ids'][0]);
                            if($exists['status'] == true){
                                if($exists['appointment'] != null){
                                    $result = $this->appointmentService->destroy($exists['appointment']);
                                }
                            }
                            $startDate->addDay();
                        }
                        return response()->json(['data' => $results, 'message' => 'Success delete leave'], 200);
                    }
                    return response()->json(['data' => null, 'message' => 'Please use the proper payload.'], 400);
                } catch (Exception $e) {
                    return response()->json(['data' => null, 'message' => $e], 500);
                }
                break;  
            default:
                return response()->json(['data' => [], 'message' => 'No command leave'], 200);
                break;
        }
    }

}
