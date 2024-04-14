<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Http\Requests\v2\Assessments\AppointmentIndexRequest;
use App\Http\Requests\v2\Assessments\SearchAppointmentRequest;
use App\Http\Requests\v2\Assessments\StoreAppointmentRequest;
use App\Http\Requests\v2\Assessments\UpdateAppointmentRequest;
use App\Http\Resources\v2\Assessments\AppointmentResource;
use App\Http\Services\v2\Assessments\ValidatorService;
use App\Models\v2\Assessments\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->validator = new ValidatorService;
    }

    public function search(SearchAppointmentRequest $request)
    {
        if ($request->has('query')) {
            $result = [];
            $query = $request->get('query');
            if (! is_null($query)) {

                $result = Appointment::select(
                    'appointments.cluster as cluster',
                    'appointments.type as type',
                    'appointments.name_en as name_en',
                    'appointments.name_sc as name_sc',
                )
                    ->where('appointments.cluster', 'LIKE', '%' . $query . '%')
                    ->orWhere('appointments.type', 'LIKE', '%' . $query . '%')
                    ->orWhere('appointments.name_en', 'LIKE', '%' . $query . '%')
                    ->orWhere('appointments.name_sc', 'LIKE', '%' . $query . '%')
                    ->get();

                if (count($result)) {
                    return response()->json([
                        'data' => $result,
                        'message' => 'Data found',
                        'success' => true,
                    ]);
                } else {
                    return response()->json([
                        'error' => [
                            'code' => 404,
                            'message' => 'No Data found',
                            'success' => false,
                        ],
                    ], 404);
                }
            } else {
                return response()->json([
                    'error' => [
                        'code' => 404,
                        'message' => 'No Data found',
                        'success' => false,
                    ],
                ], 404);
            }
        } else {
            return response()->json([
                'error' => [
                    'code' => 400,
                    'message' => 'query key parameter is required',
                    'success' => false,
                ],
            ], 400);
        }
    }

    public function index(AppointmentIndexRequest $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer',
            'sort_by' => 'nullable|in:id,created_at,updated_at',
            'sort_dir' => 'nullable|in:asc,desc',
        ]);

        // take request

        $per_page = $request->query('per_page', 10);
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'asc');
        $appointment = Appointment::query();

        return AppointmentResource::collection($appointment
            ->orderBy($sortBy, $sortDir)
            ->paginate($per_page)
            ->appends($request->except(['page']))
        );
    }

    public function store(StoreAppointmentRequest $request)
    {
        $this->validator->validateApointment($request);
        $appointment = Appointment::create($request->toArray());

        if (! $appointment) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => 'Failed to create Appointment',
                ],
            ], 500);
        }

        return response()->json([
            'data' => new AppointmentResource($appointment),
            'message' => 'Appointment created successfully',
            'success' => true,
        ], 200);
    }

    public function show($appointmentId)
    {
        $appointment = Appointment::find($appointmentId);

        if (! $appointment) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Appointment with id {$appointmentId}",
                ],
            ], 404);
        }

        return response()->json([
            'data' => new AppointmentResource($appointment),
        ]);
    }

    public function update(UpdateAppointmentRequest $request, $appointmentId)
    {
        $this->validator->validateAppointment($request);

        $appointment = Appointment::where('id', $appointmentId)->first();

        if (! $appointment) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Appointment with id {$appointmentId}",
                    'success' => false,
                ],
            ], 404);
        }

        if (! $appointment->update($request->toArray())) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => 'Failed to update Appointment',
                    'success' => false,
                ],
            ], 500);
        }

        return response()->json([
            'data' => new AppointmentResource($appointment),
            'message' => 'Appointment updated successfully',
            'success' => true,
        ]);
    }

    public function destroy($appointmentId)
    {
        $appointment = Appointment::find($appointmentId);

        if (! $appointment) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Appointment with id {$appointmentId}",
                    'success' => false,
                ],
            ], 404);
        }

        $appointment->delete();

        return response()->json([
            'data' => [],
            'message' => 'Appointment deleted successfully',
            'success' => true,
        ]);

    }
}
