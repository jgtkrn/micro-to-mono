<?php

namespace App\Http\Controllers\Api\v1;

use App\Exports\AppointmentExport;
use App\Exports\AppointmentExportCase;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;
use App\Http\Services\AppointmentService;
use App\Http\Services\ExternalService;
use App\Http\Services\CapacitorService;
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

class AppointmentController extends Controller
{
    use RespondsWithHttpStatus;
    private $appointmentService;
    private $eventUtil;
    private $externalService;
    private $capacitorService;

    public function __construct()
    {
        $this->appointmentService = new AppointmentService();
        $this->eventUtil = new EventUtil();
        $this->externalService = new ExternalService();
        $this->capacitorService = new CapacitorService();
    }

    /**
     * @OA\Get(
     *     path="/appointments-api/v1/appointments",
     *     tags={"Appointments"},
     *     summary="Appoinment List",
     *     operationId="appointmentList",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Page size (default 10)",
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="case_type",
     *          description="case type filter",
     *          example="BZN",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="team_ids",
     *         in="query",
     *         description="Team Id separated by comma",
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         example="1,2"
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Category Id separated by comma. Option: 1, 2, 3",
     *         example="1,2"
     *     ),
     *     @OA\Parameter(
     *         name="user_ids",
     *         in="query",
     *         description="User Id separated by comma",
     *         example="1,2,3,4"
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort by (default created_at). Option: id, start, end, created_at, updated_at, title, remark",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_dir",
     *         in="query",
     *         description="Sort direction (default desc). Option: asc, desc",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data", 
     *                 type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", description="Id of appointment", example="1"),
     *                     @OA\Property(property="title", type="string", description="Event subject / title", example="Title"),
     *                     @OA\Property(property="start", type="string", description="Start datetime", example="2022-05-25 13:00:00"),
     *                     @OA\Property(property="end", type="string", description="End datetime", example="2022-05-25 14:00:00"),
     *                     @OA\Property(property="remark", type="string", description="Remarks", example="Remarks"),
     *                     @OA\Property(property="category_id", type="integer", description="Id of meeting purpose / category", example="1"),
     *                     @OA\Property(property="elder", type="object", description="Elder object (Null if category is internal meeting)", 
     *                         @OA\Property(property="id", type="integer", description="Id of elder", example="1"),
     *                         @OA\Property(property="name", type="string", description="Name of elder", example="John Doe"),
     *                         @OA\Property(property="uid", type="string", description="UID of elder", example="NAAC0001"),
     *                         @OA\Property(property="contact_number", type="string", description="Contact number of elder", example="22678661"),
     *                         @OA\Property(property="address", type="string", description="Address of elder", example="45 Yuen Chim Mung Lane")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="422"),
     *                 @OA\Property(property="message", type="string", example=""),
     *                 @OA\Property(property="errors", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="field", type="string", description="Field name", example="title"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The title field is required")
     *                     ) 
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $request->validate([
            "search" => "nullable|string"
        ]);

        // $data_count = count(Event::get());
        $query['categories'] = array_filter(explode(',', $request->query("category_id")));
        $query['users'] = array_filter(explode(',', $request->query("user_ids")));
        $query['search'] = $request->query("search");
        $query['case_type'] = $request->query("case_type");
        $query['from'] = $request->query('from');
        $query['to'] = $request->query('to');
        $teams = $request->query("team_ids");
        $size = $request->query('per_page') > 0 ? (int)$request->query('per_page') : 10;
        $page = $request->query('page') > 0 ? $request->query('page') : 1;
        $skip = ((int)$page - 1) * $size;
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');

        if ($teams) {
            $user_collection = $this->externalService->getUsersData($request->bearerToken(), $query['users'], $teams);
            if (count($user_collection) == 0) {
                $query['users'] = [-1];
            } else {
                $query['users'] = $user_collection->pluck('id')->toArray();
            }
        }

        if ($query['search'] || $query['case_type']) {
            $elders_collection = $this->externalService->getEldersDataBySearch($query['search'], $query['case_type']);
            $query['elders'] = $elders_collection->pluck('id')->toArray();
        }

        $last_page = 1;
        $total_filtered_appointments = $this->eventUtil->FilterEvents($query)->count();
        if($total_filtered_appointments > $size){
            $last_page = round($total_filtered_appointments / $size, 0, PHP_ROUND_HALF_UP);
        }
        $filtered_appointments = $this->eventUtil->FilterEvents($query)->skip($skip)->take($size)->orderBy($sortBy, $sortDir)->get();
        $elderIds = implode(',', $filtered_appointments->pluck('elder_id')->toArray());
        $elders_detail = $this->externalService->getManyElderForEventList($elderIds);
        for($i =  0; $i < count($filtered_appointments); $i++){
            $elderId = $filtered_appointments[$i]->elder_id;
            if($elderId !== null && isset($elders_detail[$elderId])){
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
                'total' => $total_filtered_appointments
            ]
        ], 200);
    }

    public function getTodayUsers(Request $request){
        $date = $request->query('date');
        if(!$date){
            return [];
        }
        $from = Carbon::parse($date)->startOfDay();
        $to = Carbon::parse($date)->endOfDay();
        
        $events = Event::with('user')->where('category_id', 5)->whereDate('start', '<=', $from)->whereDate('end', '>=', $to)->get();
        $data = $events->pluck('user')->collapse()->pluck('user_id')->toArray();
        
        return $data;
    }

    /**
     * @OA\Get(
     *     path="/appointments-api/v1/appointments/{id}",
     *     tags={"Appointments"},
     *     summary="Appointment details by Id",
     *     operationId="appointmentDetails",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Appointment Id to be viewed",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data", 
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", description="Id of appointment", example="1"),
     *                 @OA\Property(property="title", type="string", description="Event subject / title", example="Title"),
     *                 @OA\Property(property="start", type="string", description="Start datetime", example="2022-05-25 13:00:00"),
     *                 @OA\Property(property="end", type="string", description="End datetime", example="2022-05-25 14:00:00"),
     *                 @OA\Property(property="remark", type="string", description="Remarks", example="Remarks"),
     *                 @OA\Property(property="category_id", type="integer", description="Id of meeting purpose / category", example="1"),
     *                 @OA\Property(property="case_id", type="integer", description="Id of meeting purpose / category", example="1"),
     *                 @OA\Property(property="user", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", description="Id of user", example="1"),
     *                         @OA\Property(property="name", type="string", description="Name of user", example="Dr Jane Doe"),
     *                         @OA\Property(property="email", type="string", description="Email of user", example="test@gmail.com")
     *                     )
     *                 ),
     *                 @OA\Property(property="file", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", description="Id of file", example="1"),
     *                         @OA\Property(property="name", type="string", description="Name of file", example="image.png"),
     *                     )
     *                 ),
     *                 @OA\Property(property="elder", type="object", description="Elder object (Null if category is internal meeting)", 
     *                     @OA\Property(property="id", type="integer", description="Id of elder", example="1"),
     *                     @OA\Property(property="name", type="string", description="Name of elder", example="John Doe"),
     *                     @OA\Property(property="uid", type="string", description="UID of elder", example="NAAC0001"),
     *                     @OA\Property(property="contact_number", type="string", description="Contact number of elder", example="22678661"),
     *                     @OA\Property(property="second_contact_number", type="string", description="Contact number of elder", example="22678662"),
     *                     @OA\Property(property="third_contact_number", type="string", description="Contact number of elder", example="22678663"),
     *                     @OA\Property(property="address", type="string", description="Address of elder", example="45 Yuen Chim Mung Lane")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Appointment not found"),
     *                 @OA\Property(property="errors", type="array", description="Empty array", example="[]",
     *                     @OA\Items()
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function detail(Request $request, $id)
    {
        $appointment = Event::where("id", $id)
            ->with("user:user_id,event_id")
            ->with("file:id,event_id,file_name")
            ->first([
                "id",
                "title",
                "start",
                "end",
                "remark",
                "category_id",
                "elder_id",
                "created_by",
                "updated_by",
                "created_by_name",
                "updated_by_name"
            ]);

        if (!$appointment) {
            return $this->failure('Appointment not found', 404);
        }

        $resultWithElder = $this->eventUtil->ResponseDetails($request->bearerToken(), $appointment);

        // get case id
        if($resultWithElder['elder']){        
            $elder_uid = $resultWithElder['elder']['uid'];
            $elder_case = Http::acceptJson()->get(env('ELDER_SERVICE_API_URL') . '/cases?' . "search={$elder_uid}");
            $case = $elder_case->collect('data');
            $resultWithElder['case_id'] = count($case) == 0 ? null : $case[0]['id'];
        }

        return $this->success($resultWithElder);
    }

    /**
     * @OA\Post(
     *     path="/appointments-api/v1/appointments",
     *     tags={"Appointments"},
     *     summary="Store new appointment",
     *     operationId="appointmentStore",
     *     @OA\Response(
     *         response=201,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Event")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="422"),
     *                 @OA\Property(property="message", type="string", example=""),
     *                 @OA\Property(property="errors", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="field", type="string", description="Field name", example="title"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The title field is required")
     *                     ) 
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input required appointment information (in string)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"title, day_date, start_time, end_time, category_id, user_ids"},
     *                 @OA\Property(property="title", type="string", example="Title", description="Title"),
     *                 @OA\Property(property="day_date", type="string", example="2022-05-25", description="Date of appointment"),
     *                 @OA\Property(property="start_time", type="string", example="01:00 PM", description="Start time"),
     *                 @OA\Property(property="end_time", type="string", example="02:00 PM", description="End time"),
     *                 @OA\Property(property="remark", type="string", example="Remarks", description="Remarks"),
     *                 @OA\Property(property="category_id", type="integer", example="1", description="Category Id"),
     *                 @OA\Property(property="elder_id", type="integer", example="1", description="Elder Id"),
     *                 @OA\Property(property="case_id", type="integer", example="1", description="Case Id"),
     *                 @OA\Property(property="user_ids", type="array", example="[1,2]", description="User Ids",
     *                      @OA\Items(type="integer", format="int32")),
     *                 @OA\Property(property="attachment_ids", type="array", example="[1,2]", description="Attachment file Ids",
     *                      @OA\Items(type="integer", format="int32"))
     *             )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $case_id = null;
        if($request->elder_id){        
            $elder = Http::acceptJson()->get(env('ELDER_SERVICE_API_URL') . '/elders' . "/{$request->elder_id}");
            $elder_data = $elder->collect('data');
            if($elder_data['uid']){            
                $elder_uid = $elder_data['uid'];
                $elder_case = Http::acceptJson()->get(env('ELDER_SERVICE_API_URL') . '/cases?' . "search={$elder_uid}");
                $case = $elder_case->collect('data');
                $case_id = count($case) == 0 ? null : $case[0]['id'];
            }
        }
        $request->merge([
            'created_by' => $request->user_id,
            'updated_by' => $request->user_id,
            'created_by_name' => $request->user_name,
            'updated_by_name' => $request->user_name,
            'case_id' => $case_id
        ]);
        
        $request->validate([
            "title" => "required",
            "day_date" => ["required", "date"],
            "start_time" => ["required", new CarbonTime],
            "end_time" => ["required", new CarbonTime, "after:start_time"], //end_time must be greater than start_time
            "category_id" => ["required", "integer", Rule::in([1, 2, 3, 4, 5, 6]), new AttendeeVerification($request->elder_id)], //internal meeting shouldn't have elder
            "case_id" => ["integer", "nullable"],
            "elder_id" => "integer",
            "user_ids" => ["required", "array"],
            "user_ids.*" => "integer",
            "attachment_ids" => ["array", new FileExist($request->attachment_ids)],
            "attachment_ids.*" => "integer"
        ]);
        $appointment = $this->appointmentService->store($request);

        // DISABLE SEND EMAIL
        // $user_ids = $appointment
        //     ->user()
        //     ->pluck("user_id")
        //     ->toArray();
        // $user_collection = $this->externalService->getUsersData($request->bearerToken(), $user_ids);
        // if (env('APP_ENV') == 'local') //testing
        // {
        //     $test_emails_collection = $user_collection->map(function ($user) {
        //         $email = env('MAIL_RECEIVER_TEST_NAME') . '+' . str_replace(' ', '', $user['name'])  . env('MAIL_RECEIVER_TEST_DOMAIN');
        //         return $email;
        //     });
        //     $user_emails = $test_emails_collection->toArray();
        // } else {
        //     $user_emails = $user_collection->pluck('email')->toArray();
        // }
        // Notification::route('mail', $user_emails)
        //     ->notify(new AppointmentInvitation($appointment));

        return $this->success($appointment, 201);
    }

    /**
     * @OA\Put(
     *     path="/appointments-api/v1/appointments/{id}",
     *     tags={"Appointments"},
     *     summary="Update appointment by Id",
     *     operationId="appointmentUpdate",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Appointment Id to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Event")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="422"),
     *                 @OA\Property(property="message", type="string", example=""),
     *                 @OA\Property(property="errors", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="field", type="string", description="Field name", example="title"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The title field is required")
     *                     ) 
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Appointment not found"),
     *                 @OA\Property(property="errors", type="array", description="Empty array", example="[]",
     *                     @OA\Items()
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input required appointment information (in string)",
     *         required=true,
     *              @OA\JsonContent(
     *                 required={"title, day_date, start_time, end_time, category_id, user_ids"},
     *                 @OA\Property(property="title", type="string", example="Title", description="Title"),
     *                 @OA\Property(property="day_date", type="string", example="2022-05-25", description="Date of appointment"),
     *                 @OA\Property(property="start_time", type="string", example="01:00 PM", description="Start time"),
     *                 @OA\Property(property="end_time", type="string", example="02:00 PM", description="End time"),
     *                 @OA\Property(property="remark", type="string", example="Remarks", description="Remarks"),
     *                 @OA\Property(property="category_id", type="integer", example="1", description="Category Id"),
     *                 @OA\Property(property="elder_id", type="integer", example="1", description="Elder Id"),
     *                 @OA\Property(property="case_id", type="integer", example="1", description="Case Id"),
     *                 @OA\Property(property="user_ids", type="array", example="[1,2]", description="User Ids",
     *                      @OA\Items(type="integer", format="int32")),
     *                 @OA\Property(property="attachment_ids", type="array", example="[1,2]", description="Attachment file Ids",
     *                      @OA\Items(type="integer", format="int32"))
     *             )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $case_id = null;
        if($request->elder_id){        
            $elder = Http::acceptJson()->get(env('ELDER_SERVICE_API_URL') . '/elders' . "/{$request->elder_id}");
            $elder_data = $elder->collect('data');
            if($elder_data['uid']){            
                $elder_uid = $elder_data['uid'];
                $elder_case = Http::acceptJson()->get(env('ELDER_SERVICE_API_URL') . '/cases?' . "search={$elder_uid}");
                $case = $elder_case->collect('data');
                $case_id = count($case) == 0 ? null : $case[0]['id'];
            }
        }
        $current_event = Event::where('id', $id)->first();
        $request->merge([
            'created_by' => $request->user_id,
            'updated_by' => $request->user_id,
            'created_by_name' => $request->user_name,
            'updated_by_name' => $request->user_name,
            'case_id' => $case_id
        ]);

        $request->validate([
            "title" => "required",
            "day_date" => ["required", "date"],
            "start_time" => ["required", new CarbonTime],
            "end_time" => ["required", new CarbonTime, "after:start_time"], //end_time must be greater than start_time
            "category_id" => ["required", "integer", Rule::in([1, 2, 3, 4, 5, 6]), new AttendeeVerification($request->elder_id)], //internal meeting and on leave shouldn't have elder
            "case_id" => ["integer", "nullable"],
            "elder_id" => "integer",
            "user_ids" => ["required", "array"],
            "user_ids.*" => "integer",
            "attachment_ids" => ["array", new FileExist($request->attachment_ids)],
            "attachment_ids.*" => "integer"
        ]);

        $appointment = Event::find($id);
        if (!$appointment) {
            return $this->failure('Appointment not found', 404);
        }

        $result = $this->appointmentService->update($request, $appointment);
        return $this->success($result);
    }

    /**
     * @OA\Delete(
     *     path="/appointments-api/v1/appointments/{id}",
     *     tags={"Appointments"},
     *     summary="Delete appointment by Id",
     *     operationId="appointmentDelete",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Appointment Id to be viewed",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Successful"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Appointment not found"),
     *                 @OA\Property(property="errors", type="array", description="Empty array", example="[]",
     *                     @OA\Items()
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $appointment = Event::find($id);
        if (!$appointment) {
            return $this->failure('Appointment not found', 404);
        }
        $this->appointmentService->destroy($appointment);
        return response(null, 204);
    }

    /**
     * @OA\Delete(
     *     path="/appointments-api/v1/appointments",
     *     tags={"Appointments"},
     *     summary="Bulk delete appointment by Id",
     *     operationId="appointmentBulkDelete",
     *     @OA\Parameter(
     *         name="ids",
     *         in="query",
     *         description="Appointment Id to be deleted separated by comma",
     *         example="1,2,3,4"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Successful"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Appointment not found"),
     *                 @OA\Property(property="errors", type="array", description="Empty array", example="[]",
     *                     @OA\Items()
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function massDestroy(Request $request)
    {
        $appointment_ids = array_filter(explode(',', $request->query("ids")));
        $appointments = Event::whereIn("id", $appointment_ids)->get();
        if (count($appointments) == 0) {
            return $this->failure('Appointment not found', 404);
        }
        $this->appointmentService->massDestroy($appointments);
        return response(null, 204);
    }

    /**
     * @OA\Get(
     *     path="/appointments-api/v1/appointments/events",
     *     tags={"Appointments"},
     *     summary="Calendar Event List",
     *     operationId="calendarEventList",
     *     @OA\Parameter(
     *         name="start",
     *         in="query",
     *         required=true,
     *         description="Start date (YYYY-MM-DD)",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="end",
     *         in="query",
     *         required=true,
     *         description="End date (YYYY-MM-DD)",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="team_ids",
     *         in="query",
     *         description="Team Id separated by comma",
     *         @OA\Schema(
     *             type="string"
     *         ),
     *         example="1,2"
     *     ),
     *     @OA\Parameter(
     *         name="category_id[]",
     *         in="query",
     *         description="Category Id (array)",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="integer", format="int32")
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="user_ids[]",
     *         in="query",
     *         description="User Id (array)",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="integer", format="int32")
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="case_type",
     *          description="case type filter",
     *          example="BZN",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data", 
     *                 type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", description="Id of appointment", example="1"),
     *                     @OA\Property(property="title", type="string", description="Event subject / title", example="Title"),
     *                     @OA\Property(property="start", type="string", description="Start datetime", example="2022-05-25 13:00:00"),
     *                     @OA\Property(property="end", type="string", description="End datetime", example="2022-05-25 14:00:00"),
     *                     @OA\Property(property="category_id", type="integer", description="Id of meeting purpose / category", example="1"), 
     *                     @OA\Property(property="elder_name", type="string", description="Name of elder (Null if category is internal meeting)", example="John Doe"),
     *                     @OA\Property(property="users_name", type="string", description="Name of users separated by comma", example="Dr. John Doe, Dr. Dre"),
     *                     @OA\Property(property="elder", type="object", description="Elder object (Null if category is internal meeting)", 
     *                         @OA\Property(property="id", type="integer", description="Id of elder", example="1"),
     *                         @OA\Property(property="name", type="string", description="Name of elder", example="John Doe"),
     *                         @OA\Property(property="uid", type="string", description="UID of elder", example="NAAC0001"),
     *                         @OA\Property(property="contact_number", type="string", description="Contact number of elder", example="22678661"),
     *                         @OA\Property(property="address", type="string", description="Address of elder", example="45 Yuen Chim Mung Lane")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="422"),
     *                 @OA\Property(property="message", type="string", example=""),
     *                 @OA\Property(property="errors", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="field", type="string", description="Field name", example="title"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The title field is required")
     *                     ) 
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function calendar(Request $request)
    {
        $request->validate([
            "start" => ["required", "date"],
            "end" => ["required", "date", "after_or_equal:start"]
        ]);

        $query['categories'] = array_filter(explode(',', $request->query("category_id")));
        $query['users'] = array_filter(explode(',', $request->query("user_ids")));
        $query['start'] = $request->query("start");
        $query['end'] = $request->query("end");
        $query['search'] = $request->query("search");
        $query['case_type'] = $request->query("case_type");
        $teams = $request->query("team_ids");

        if ($teams) {
            $user_collection = $this->externalService->getUsersData($request->bearerToken(), $query['users'], $teams);
            if (count($user_collection) == 0) {
                $query['users'] = [-1];
            } else {
                $query['users'] = $user_collection->pluck('id')->toArray();
            }
        }

        if ($query['search'] || $query['case_type']) {
            $elders_collection = $this->externalService->getEldersDataBySearch($query['search'], $query['case_type']);
            $query['elders'] = $elders_collection->pluck('id')->toArray();
        }

        $filtered_appointments = $this->eventUtil->FilterEvents($query)->orderBy('start', 'asc');

        $appointments = $filtered_appointments->get(['id', 'title', 'start', 'end', 'elder_id', 'category_id']);

        $resultWithUserElder = $this->eventUtil->ResponseCalendar($request->bearerToken(), $appointments);
        return $this->success($resultWithUserElder);
    }

    /**
     * @OA\Get(
     *     path="/appointments-api/v1/appointments-csv",
     *     tags={"Appointments"},
     *     summary="export appointments to csv",
     *     operationId="exportAppointments",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful"
     *     ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     ),
     * )
     */
    public function exportCsv(Request $request)
    {
		$this->authorize('export_csv', $request->access_role);
        //get result from appointment list
        $result_with_elder = $this->index($request);
        $result_collection = collect($result_with_elder->getData()->data);
        return Excel::download(new AppointmentExport($result_collection), 'appointments.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function getLeave(){
        return $this->capacitorService->getLeave();
    }

    public function reportResourceSet(Request $request){
        $events = Event::select('elder_id');
        if ($request->query('from') && $request->query('to')) 
        {
            $from = Carbon::parse($request->query('from'))->startOfDay();
            $to = Carbon::parse($request->query('to'))->endOfDay();
            $events = $events->whereBetween('end', [$from, $to])->orderBy('end', 'asc');
        }
        $events = $events->get();
        if(!$events){
            return response()->json(['data' => null], 404);
        }
        $elder_ids = array_values(array_filter($events->pluck('elder_id')->toArray()));

        $elderEvents = Event::select(['elder_id', 'start', 'end', 'category_id'])->whereIn('elder_id', $elder_ids)->get();
        if(!$elderEvents){
            return response()->json(['data' => null], 404);
        }
        $results = new \stdClass();
        for($i = 0; $i < count($elderEvents); $i++){
            $elder_id = $elderEvents[$i]['elder_id'];
            if(!property_exists($results, $elder_id)){
                $results->$elder_id['elder_id'] = $elderEvents[$i]['elder_id'];
                $results->$elder_id['face_visit'] = $elderEvents[$i]['category_id'] == 2 ? 1 : 0;
                $results->$elder_id['tele_visit'] = $elderEvents[$i]['category_id'] == 4 ? 1 : 0;
                $results->$elder_id['first_visit'] = $elderEvents[$i]['start'] !== null ? date("Y-m-d", strtotime($elderEvents[$i]['start'])) : null; 
                $results->$elder_id['last_visit'] = $elderEvents[$i]['end'] !== null ? date("Y-m-d", strtotime($elderEvents[$i]['end'])) : null;
                $results->$elder_id['patient_care_hour'] = 0;
                if($elderEvents[$i]['start'] && $elderEvents[$i]['end']){
                    $full_time = (strtotime($elderEvents[$i]['end']) - strtotime($elderEvents[$i]['start'])) / 3600;
                    $results->$elder_id['patient_care_hour']+= floor($full_time);
                }
            }
            if(property_exists($results, $elder_id)){
                $results->$elder_id['elder_id'] = $elderEvents[$i]['elder_id'];
                $results->$elder_id['face_visit'] = $elderEvents[$i]['category_id'] == 2 ? $results->$elder_id['face_visit'] += 1 : $results->$elder_id['face_visit'];
                $results->$elder_id['tele_visit'] = $elderEvents[$i]['category_id'] == 4 ? $results->$elder_id['tele_visit']+=1 : $results->$elder_id['tele_visit'];
                $results->$elder_id['first_visit'] = $elderEvents[$i]['start'] !== null ? ( $results->$elder_id['first_visit'] > $elderEvents[$i]['start'] ? date("Y-m-d", strtotime($elderEvents[$i]['start'])) : $results->$elder_id['first_visit']) : $results->$elder_id['first_visit'];
                $results->$elder_id['last_visit'] = $elderEvents[$i]['end'] !== null ? ( $results->$elder_id['last_visit'] < $elderEvents[$i]['end'] ? date("Y-m-d", strtotime($elderEvents[$i]['end'])) : $results->$elder_id['last_visit']) : $results->$elder_id['last_visit'];
                if($elderEvents[$i]['start'] && $elderEvents[$i]['end']){
                    $full_time = (strtotime($elderEvents[$i]['end']) - strtotime($elderEvents[$i]['start'])) / 3600;
                    $results->$elder_id['patient_care_hour']+= floor($full_time);
                }
            }
        }
        $results->elder_ids = $elder_ids;
        return response()->json(['data' => $results], 200);
    }

    public function staffReportRecordSet(Request $request){
        $results = new \stdClass();
        $userEvents = UserEvent::select(['*']);
        if($request->query('user_ids')){
            $userIds = explode(',', $request->query('user_ids'));
            $userEvents = $userEvents->whereIn('user_id', $userIds);
        }
        $userEvents = $userEvents->with([
            'event' => function ($query) {
                $query->select('id', 'category_id')->get();
            }
        ])->select('user_id', 'event_id')->get();
        if(count($userEvents) == 0){
            $results = null;
        }
        for($i = 0; $i < count($userEvents); $i++){
            $userId =  $userEvents[$i]['user_id'];
            if(!property_exists($results, $userId)){
                $results->$userId['appointment'] = 1;
                $results->$userId['followup'] = 0;
                $results->$userId['meeting'] = 0;
                $results->$userId['booking'] = 0;
                $results->$userId['administrative_work'] = 0;
                if($userEvents[$i]['event']['category_id'] === 6){
                    $results->$userId['administrative_work'] = 1;
                }
                if($userEvents[$i]['event']['category_id'] === 4){
                    $results->$userId['followup'] = 1;
                }
                if($userEvents[$i]['event']['category_id'] === 3){
                    $results->$userId['meeting'] = 1;
                }
                if($userEvents[$i]['event']['category_id'] === 1){
                    $results->$userId['booking'] = 1;
                }
            } else if(property_exists($results, $userId)){
                $results->$userId['appointment'] += 1;
                if($userEvents[$i]['event']['category_id'] === 6){
                    $results->$userId['administrative_work'] = 1;
                }
                if($userEvents[$i]['event']['category_id'] === 4){
                    $results->$userId['followup'] += 1;
                }
                if($userEvents[$i]['event']['category_id'] === 3){
                    $results->$userId['meeting'] += 1;
                }
                if($userEvents[$i]['event']['category_id'] === 1){
                    $results->$userId['booking'] += 1;
                }
            }
        }
        return response()->json(['data' => $results], 200);
    }

    public function newDetails($id) {
        $appointment = Event::where("id", $id)
            ->with("user:user_id,event_id")
            ->with("file:id,event_id,file_name")
            ->first([
                "id",
                "title",
                "start",
                "end",
                "remark",
                "category_id",
                "elder_id",
                "created_by",
                "updated_by",
                "created_by_name",
                "updated_by_name"
            ]);

        if (!$appointment) {
            return $this->failure('Appointment not found', 404);
        }
        $elder = $this->externalService->getElderFromEvent($appointment->elder_id);
        $appointment->elder = null;
        $appointment->case_id = null;
        if($elder){
            $appointment->elder = $elder;
            $appointment->case_id = $elder['case_id'];
            unset($appointment->elder['case_id']);
        }
        unset($appointment->elder_id);
        $userIds = implode(',', $appointment->user()->pluck("user_id")->toArray());
        $users = $this->externalService->getUsersFromEvent($userIds);
        unset($appointment->user);
        $appointment->user = [];
        if($users){
            $appointment->user = $users;
        }

        return $this->success($appointment);
    }

    public function elderReportRecordSet()
    {
        $results = new \stdClass();
        $event = Event::select('start','end','case_id','category_id')->whereNotNull('case_id')->get();
        // return count($event);
        if(count($event) == 0){
            return null; 
        }

        for($i = 0; $i < count($event); $i++){
            $caseId = $event[$i]->case_id;
            if(!property_exists($results, $caseId)){
                $results->$caseId['case_phone_contact'] = 0;
                $results->$caseId['contact_total_number'] = 0;
                if($event[$i]['category_id'] === 4){
                    $results->$caseId['case_phone_contact'] = 0;
                }
                if($event[$i]['category_id'] === 3){
                    $results->$caseId['contact_total_number'] = 0;
                }
            } else if(property_exists($results, $caseId)){
                if($event[$i]['category_id'] === 4){
                    $full_time = (strtotime($event[$i]['end']) - strtotime($event[$i]['start'])) / 3600;
                    $results->$caseId['case_phone_contact']+= floor($full_time);
                }
                if($event[$i]['category_id'] === 3){
                    $full_time = (strtotime($event[$i]['end']) - strtotime($event[$i]['start'])) / 3600;
                    $results->$caseId['contact_total_number']+= floor($full_time);
                }
            }
        }
        return response()->json(['data' => $results], 200);
    }
    /**
     * @OA\Get(
     *     path="/appointments-api/v1/appointments-export",
     *     tags={"Appointments"},
     *     summary="export appointments with case to csv",
     *     operationId="exportAppointmentsWithCase",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful"
     *     ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     ),
     * )
     */
    public function exportAppointments(Request $request)
    {
        $this->authorize('export_csv', $request->access_role);
        $caseManagerSet = $this->externalService->getCaseManagerSet($request->bearerToken());
        //get result from appointment list
        $appointments = collect($this->index($request)->getData()->data);
        for($i = 0; $i < count($appointments); $i++){
            $appointments[$i]->case_manager = null;
            $caseId = $appointments[$i]->case_id;
            if($caseManagerSet && isset($caseManagerSet[$caseId])){
                $appointments[$i]->case_manager = $caseManagerSet[$caseId]['case_manager'];
            }
        }
        return Excel::download(new AppointmentExportCase($appointments), 'appointments.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
