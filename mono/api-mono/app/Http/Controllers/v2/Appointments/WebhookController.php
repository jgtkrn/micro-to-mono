<?php

namespace App\Http\Controllers\v2\Appointments;

use App\Http\Controllers\Controller;
use App\Http\Services\v2\Appointments\AppointmentService;
use App\Http\Services\v2\Appointments\EventUtil;
use App\Http\Services\v2\Appointments\WiringServiceAppointment;
use App\Models\v2\Appointments\Event;
use App\Models\v2\Appointments\UserEvent;
use App\Traits\RespondsWithHttpStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    use RespondsWithHttpStatus;
    private $appointmentService;
    private $eventUtil;
    private $wiringService;

    public function __construct()
    {
        $this->appointmentService = new AppointmentService;
        $this->eventUtil = new EventUtil;
        $this->wiringService = new WiringServiceAppointment;
    }

    public function reshape($payload, $submitDate)
    {
        $user_ids = [];
        $email = null;
        $employee = $payload['employee'];
        if ($employee) {
            $email = $employee['email'];
        }
        $user_ids = ($email == null) ? [] : $this->wiringService->getUserByEmail($email);

        $data = [
            'created_by' => 1,
            'updated_by' => 1,
            'created_by_name' => 'Automatic System',
            'updated_by_name' => 'Automatic System',
            'title' => $payload['leaveType'],
            'day_date' => $submitDate,
            'start_time' => '00:00',
            'end_time' => '23:59',
            'remark' => $payload['remarks'],
            'case_id' => null,
            'elder_id' => null,
            'category_id' => 5,
            'user_ids' => [$user_ids['id']],
        ];

        return $data;
    }

    public function checkExistEvent($formattedDate, $userId)
    {
        $startDate = Carbon::parse($formattedDate)->startOfDay();
        $endDate = Carbon::parse($formattedDate)->endOfDay();

        $userHasEventInRange = UserEvent::whereHas('event', function ($query) use ($startDate, $endDate) {
            $query->whereDate('start', '>=', $startDate)
                ->whereDate('end', '<=', $endDate);
        })
            ->where('user_id', $userId)
            ->first();
        $appointment = null;
        if ($userHasEventInRange) {
            $appointment = Event::where('id', $userHasEventInRange->event_id)->first();
        }

        $data = [
            'status' => (! $userHasEventInRange) ? false : true,
            'appointment' => ($appointment != null) ? $appointment : null,
        ];

        return $data;
    }

    public function store(Request $request)
    {
        switch ($request->context) {
            case 'create.leave':
                try {
                    $results = [];
                    if ($request->payload) {
                        $startDateString = $request->payload['leaveDate'];
                        $endDateString = $request->payload['backToWork'];

                        $startDate = Carbon::parse($startDateString);
                        $endDate = Carbon::parse($endDateString);

                        while ($startDate->lte($endDate)) {
                            $formattedDate = $startDate->format('Y-m-d');
                            $data = $this->reshape($request->payload, $formattedDate);
                            $request->merge($data);
                            $exists = $this->checkExistEvent($formattedDate, $data['user_ids'][0]);
                            if ($exists['status'] == false) {
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
                    if ($request->payload) {
                        $startDateString = $request->payload['leaveDate'];
                        $endDateString = $request->payload['backToWork'];

                        $startDate = Carbon::parse($startDateString);
                        $endDate = Carbon::parse($endDateString);

                        while ($startDate->lte($endDate)) {
                            $formattedDate = $startDate->format('Y-m-d');
                            $data = $this->reshape($request->payload, $formattedDate);
                            $request->merge($data);
                            $exists = $this->checkExistEvent($formattedDate, $data['user_ids'][0]);
                            if ($exists['status'] == false) {
                                $result = $this->appointmentService->store($request);
                                array_push($results, $result);
                            }
                            if ($exists['status'] == true) {
                                if ($exists['appointment'] != null) {
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
                    if ($request->payload) {
                        $startDateString = $request->payload['leaveDate'];
                        $endDateString = $request->payload['backToWork'];

                        $startDate = Carbon::parse($startDateString);
                        $endDate = Carbon::parse($endDateString);

                        while ($startDate->lte($endDate)) {
                            $formattedDate = $startDate->format('Y-m-d');
                            $data = $this->reshape($request->payload, $formattedDate);
                            $request->merge($data);
                            $exists = $this->checkExistEvent($formattedDate, $data['user_ids'][0]);
                            if ($exists['status'] == true) {
                                if ($exists['appointment'] != null) {
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
