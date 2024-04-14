<?php

namespace App\Http\Controllers\v2\Appointments;

use App\Exports\v2\Appointments\AppointmentExport;
use App\Exports\v2\Appointments\AppointmentExportCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\v2\Appointments\CalendarRequest;
use App\Http\Requests\v2\Appointments\EventIndexRequest;
use App\Http\Requests\v2\Appointments\GetTodayUsersRequest;
use App\Http\Requests\v2\Appointments\MassDestroyAppointmentRequest;
use App\Http\Requests\v2\Appointments\ReportResourceSetRequest;
use App\Http\Requests\v2\Appointments\StoreAppointmentRequest;
use App\Http\Services\v2\Appointments\AppointmentService;
use App\Http\Services\v2\Appointments\CapacitorService;
use App\Http\Services\v2\Appointments\EventUtil;
use App\Http\Services\v2\Appointments\WiringServiceAppointment;
use App\Models\v2\Appointments\Event;
use App\Models\v2\Appointments\UserEvent;
use App\Rules\AttendeeVerification;
use App\Rules\CarbonTime;
use App\Rules\FileExist;
use App\Traits\RespondsWithHttpStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Excel as MaatExcel;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class AppointmentController extends Controller
{
    use RespondsWithHttpStatus;
    private $appointmentService;
    private $eventUtil;
    private $capacitorService;
    private $wiringService;

    public function __construct()
    {
        $this->appointmentService = new AppointmentService;
        $this->eventUtil = new EventUtil;
        $this->capacitorService = new CapacitorService;
        $this->wiringService = new WiringServiceAppointment;
    }

    public function index(EventIndexRequest $request)
    {
        $request->validate([
            'search' => 'nullable|string',
        ]);

        $query['categories'] = array_filter(explode(',', $request->query('category_id')));
        $query['users'] = array_filter(explode(',', $request->query('user_ids')));
        $query['search'] = $request->query('search');
        $query['case_type'] = $request->query('case_type');
        $query['from'] = $request->query('from');
        $query['to'] = $request->query('to');
        $teams = $request->query('team_ids');
        $size = $request->query('per_page') > 0 ? (int) $request->query('per_page') : 10;
        $page = $request->query('page') > 0 ? (int) $request->query('page') : 1;
        $skip = ($page - 1) * $size;
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');

        if ($teams) {
            $user_collection = $this->wiringService->getUsersData($query['users'], $teams);
            if (count($user_collection) == 0) {
                $query['users'] = [-1];
            } else {
                $query['users'] = $user_collection->pluck('id')->toArray();
            }
        }

        if ($query['search'] || $query['case_type']) {
            $elders_collection = $this->wiringService->getEldersDataBySearch($query['search'], $query['case_type']);
            $query['elders'] = collect($elders_collection)->pluck('id')->toArray();
        }

        $last_page = 1;
        $appointments_collection = $this->eventUtil->filterEvents($query);
        $total_filtered_appointments = $appointments_collection->count();
        if ($total_filtered_appointments > $size) {
            $last_page = ceil($total_filtered_appointments / $size);
        }

        $filtered_appointments = $appointments_collection->skip($skip)->take($size)->orderBy($sortBy, $sortDir)->get();

        $elderIds = implode(',', $filtered_appointments->pluck('elder_id')->toArray());
        $elders_detail = $this->wiringService->getManyElderForEventList($elderIds);
        for ($i = 0; $i < count($filtered_appointments); $i++) {
            $elderId = $filtered_appointments[$i]->elder_id;
            if ($elderId !== null && isset($elders_detail[$elderId])) {
                $filtered_appointments[$i]->elder = $elders_detail[$elderId];
            } else {
                $filtered_appointments[$i]->elder = null;
            }
        }

        return response()->json([
            'data' => $filtered_appointments,
            'meta' => [
                'current_page' => intval($page),
                'last_page' => $last_page,
                'per_page' => $size,
                'total' => $total_filtered_appointments,
            ],
        ], 200);
    }

    public function getTodayUsers(GetTodayUsersRequest $request)
    {
        $date = $request->query('date');
        if (! $date) {
            return [];
        }
        $from = Carbon::parse($date)->startOfDay();
        $to = Carbon::parse($date)->endOfDay();

        $events = Event::with('user')->where('category_id', 5)->whereDate('start', '<=', $from)->whereDate('end', '>=', $to)->get();
        $data = $events->pluck('user')->collapse()->pluck('user_id')->toArray();

        return $data;
    }

    public function detail(Request $request, $id)
    {
        $appointment = Event::where('id', $id)
            ->with('user:user_id,event_id')
            ->with('file:id,event_id,file_name')
            ->first([
                'id',
                'title',
                'start',
                'end',
                'remark',
                'category_id',
                'elder_id',
                'created_by',
                'updated_by',
                'created_by_name',
                'updated_by_name',
            ]);

        if (! $appointment) {
            return $this->failure('Appointment not found', 404);
        }

        $resultWithElder = $this->eventUtil->responseDetails($request->bearerToken(), $appointment);

        if ($resultWithElder) {
            $case = $$this->wiringService->getCasesByElderId($appointment->elder_id);
            $resultWithElder['case_id'] = count($case) == 0 ? null : $case[0]['id'];
        }

        return $this->success($resultWithElder);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $case_id = null;
        if ($request->elder_id) {
            $case = $this->wiringService->getCasesByElderId($request->elder_id);
            if ($case) {
                $case_id = count($case) == 0 ? null : $case[0]['id'];
            }
        }
        $request->merge([
            'created_by' => $request->user_id,
            'updated_by' => $request->user_id,
            'created_by_name' => $request->user_name,
            'updated_by_name' => $request->user_name,
            'case_id' => $case_id,
        ]);

        $request->validate([
            'title' => 'required',
            'day_date' => ['required', 'date'],
            'start_time' => ['required', new CarbonTime],
            'end_time' => ['required', new CarbonTime, 'after:start_time'], //end_time must be greater than start_time
            'category_id' => ['required', 'integer', Rule::in([1, 2, 3, 4, 5, 6]), new AttendeeVerification($request->elder_id)], //internal meeting shouldn't have elder
            'case_id' => ['integer', 'nullable'],
            'elder_id' => 'integer',
            'user_ids' => ['required', 'array'],
            'user_ids.*' => 'integer',
            'attachment_ids' => ['array', new FileExist($request->attachment_ids)],
            'attachment_ids.*' => 'integer',
        ]);
        $appointment = $this->appointmentService->store($request);

        return $this->success($appointment, 201);
    }

    public function update(Request $request, $id)
    {
        $case_id = null;
        if ($request->elder_id) {
            $case = $this->wiringService->getCasesByElderId($request->elder_id);
            if ($case) {
                $case_id = count($case) == 0 ? null : $case[0]['id'];
            }
        }
        $current_event = Event::where('id', $id)->first();
        $request->merge([
            'created_by' => $request->user_id,
            'updated_by' => $request->user_id,
            'created_by_name' => $request->user_name,
            'updated_by_name' => $request->user_name,
            'case_id' => $case_id,
        ]);

        $request->validate([
            'title' => 'required',
            'day_date' => ['required', 'date'],
            'start_time' => ['required', new CarbonTime],
            'end_time' => ['required', new CarbonTime, 'after:start_time'], //end_time must be greater than start_time
            'category_id' => ['required', 'integer', Rule::in([1, 2, 3, 4, 5, 6]), new AttendeeVerification($request->elder_id)], //internal meeting and on leave shouldn't have elder
            'case_id' => ['integer', 'nullable'],
            'elder_id' => 'integer',
            'user_ids' => ['required', 'array'],
            'user_ids.*' => 'integer',
            'attachment_ids' => ['array', new FileExist($request->attachment_ids)],
            'attachment_ids.*' => 'integer',
        ]);

        $appointment = Event::find($id);
        if (! $appointment) {
            return $this->failure('Appointment not found', 404);
        }

        $result = $this->appointmentService->update($request, $appointment);

        return $this->success($result);
    }

    public function destroy($id)
    {
        $appointment = Event::find($id);
        if (! $appointment) {
            return $this->failure('Appointment not found', 404);
        }
        $this->appointmentService->destroy($appointment);

        return response(null, 204);
    }

    public function massDestroy(MassDestroyAppointmentRequest $request)
    {
        $appointment_ids = array_filter(explode(',', $request->query('ids')));
        $appointments = Event::whereIn('id', $appointment_ids)->get();
        if (count($appointments) == 0) {
            return $this->failure('Appointment not found', 404);
        }
        $this->appointmentService->massDestroy($appointments);

        return response(null, 204);
    }

    public function calendar(CalendarRequest $request)
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
        ]);

        $query['categories'] = array_filter(explode(',', $request->query('category_id')));
        $query['users'] = array_filter(explode(',', $request->query('user_ids')));
        $query['start'] = $request->query('start');
        $query['end'] = $request->query('end');
        $query['search'] = $request->query('search');
        $query['case_type'] = $request->query('case_type');
        $teams = $request->query('team_ids');

        if ($teams) {
            $user_collection = $this->wiringService->getUsersData($query['users'], $teams);
            if (count($user_collection) == 0) {
                $query['users'] = [-1];
            } else {
                $query['users'] = $user_collection->pluck('id')->toArray();
            }
        }
        if ($query['search'] || $query['case_type']) {
            $elders_collection = $this->wiringService->getEldersDataBySearch($query['search'], $query['case_type']);
            $query['elders'] = collect($elders_collection)->pluck('id')->toArray();
        }

        $appointments = $this->eventUtil->filterEvents($query)->orderBy('start', 'asc')->get(['id', 'title', 'start', 'end', 'elder_id', 'category_id']);

        $resultWithUserElder = $this->eventUtil->responseCalendar($appointments);

        return $this->success($resultWithUserElder);
    }

    public function exportCsv(Request $request)
    {
        $this->authorize('export_csv', $request->access_role);
        //get result from appointment list
        $result_with_elder = $this->index($request);
        $result_collection = collect($result_with_elder->getData()->data);

        return Excel::download(new AppointmentExport($result_collection), 'appointments.csv', MaatExcel::CSV);
    }

    public function getLeave()
    {
        return $this->capacitorService->getLeave();
    }

    public function reportResourceSet(ReportResourceSetRequest $request)
    {
        $events = Event::select('elder_id');
        if ($request->query('from') && $request->query('to')) {
            $from = Carbon::parse($request->query('from'))->startOfDay();
            $to = Carbon::parse($request->query('to'))->endOfDay();
            $events = $events->whereBetween('end', [$from, $to])->orderBy('end', 'asc');
        }
        $events = $events->get();
        if (! $events) {
            return response()->json(['data' => null], 404);
        }
        $elder_ids = array_values(array_filter($events->pluck('elder_id')->toArray()));

        $elderEvents = Event::select(['elder_id', 'start', 'end', 'category_id'])->whereIn('elder_id', $elder_ids)->get();
        if (! $elderEvents) {
            return response()->json(['data' => null], 404);
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

        return response()->json(['data' => $results], 200);
    }

    public function staffReportRecordSet(Request $request)
    {
        $results = new stdClass;
        $userEvents = UserEvent::select(['*']);
        if ($request->query('user_ids')) {
            $userIds = explode(',', $request->query('user_ids'));
            $userEvents = $userEvents->whereIn('user_id', $userIds);
        }

        // date filter by created_at
        if ($request->query('from') && $request->query('to')) {
            $from = Carbon::parse($request->query('from'))->startOfDay();
            $to = Carbon::parse($request->query('to'))->endOfDay();
            $userEvents = $userEvents->whereBetween('created_at', [$from, $to])->orderBy('created_at', 'asc');
        }

        $userEvents = $userEvents->with([
            'event' => function ($query) {
                $query->select('id', 'category_id')->get();
            },
        ])->select('user_id', 'event_id')->get();
        if (count($userEvents) == 0) {
            $results = null;
        }

        $user_ids = array_values(array_filter($userEvents->pluck('user_id')->toArray()));
        $results->user_ids = $user_ids;

        for ($i = 0; $i < count($userEvents); $i++) {
            $userId = $userEvents[$i]['user_id'];
            if (! property_exists($results, $userId)) {
                $results->$userId['appointment'] = 1;
                $results->$userId['followup'] = 0;
                $results->$userId['meeting'] = 0;
                $results->$userId['booking'] = 0;
                $results->$userId['administrative_work'] = 0;
                if ($userEvents[$i]['event']['category_id'] === 6) {
                    $results->$userId['administrative_work'] = 1;
                }
                if ($userEvents[$i]['event']['category_id'] === 4) {
                    $results->$userId['followup'] = 1;
                }
                if ($userEvents[$i]['event']['category_id'] === 3) {
                    $results->$userId['meeting'] = 1;
                }
                if ($userEvents[$i]['event']['category_id'] === 1) {
                    $results->$userId['booking'] = 1;
                }
            } elseif (property_exists($results, $userId)) {
                $results->$userId['appointment'] += 1;
                if ($userEvents[$i]['event']['category_id'] === 6) {
                    $results->$userId['administrative_work'] += 1;
                }
                if ($userEvents[$i]['event']['category_id'] === 4) {
                    $results->$userId['followup'] += 1;
                }
                if ($userEvents[$i]['event']['category_id'] === 3) {
                    $results->$userId['meeting'] += 1;
                }
                if ($userEvents[$i]['event']['category_id'] === 1) {
                    $results->$userId['booking'] += 1;
                }
            }
        }

        return response()->json(['data' => $results], 200);
    }

    public function newDetails($id)
    {
        $appointment = Event::where('id', $id)
            ->with('user:user_id,event_id')
            ->with('file:id,event_id,file_name')
            ->first([
                'id',
                'title',
                'start',
                'end',
                'remark',
                'category_id',
                'elder_id',
                'created_by',
                'updated_by',
                'created_by_name',
                'updated_by_name',
            ]);

        if (! $appointment) {
            return $this->failure('Appointment not found', 404);
        }
        $elder = $this->wiringService->getElderFromEvent($appointment->elder_id);
        $appointment->elder = null;
        $appointment->case_id = null;
        if ($elder) {
            $appointment->elder = $elder;
            $appointment->case_id = $elder['case_id'];
            unset($appointment->elder['case_id']);
        }
        unset($appointment->elder_id);
        $userIds = $appointment->user()->pluck('user_id')->toArray();
        $users = $this->wiringService->getUsersFromEvent($userIds);
        unset($appointment->user);
        $appointment->user = [];
        if ($users) {
            $appointment->user = $users;
        }

        return $this->success($appointment);
    }

    public function elderReportRecordSet()
    {
        $results = new stdClass;
        $event = Event::select('start', 'end', 'case_id', 'category_id')->whereNotNull('case_id')->get();
        // return count($event);
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

        return response()->json(['data' => $results], 200);
    }

    public function exportAppointments(EventIndexRequest $request)
    {

        $data = Event::select(['start', 'end', 'case_id', 'category_id'])->with('case_id');
        $caseManagerSet = $this->wiringService->getCaseManagerSet();
        $appointments = collect($this->index($request)->getData()->data);
        $from = $request->query('from');
        $to = $request->query('to');
        if ($request->query('from') && $request->query('to')) {
            $from = Carbon::parse($request->query('from'))->startOfDay();
            $to = Carbon::parse($request->query('to'))->endOfDay();
            $data = $data->whereBetween('start', [$from, $to]);
        }
        for ($i = 0; $i < count($appointments); $i++) {
            $appointments[$i]->case_manager = null;
            $caseId = $appointments[$i]->case_id;
            if ($caseManagerSet && isset($caseManagerSet[$caseId])) {
                $appointments[$i]->case_manager = $caseManagerSet[$caseId]['case_manager'];
            }
        }

        return Excel::download(new AppointmentExportCase($appointments), 'appointments.csv', MaatExcel::CSV);
    }
}
