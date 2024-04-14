<?php

namespace App\Http\Services\v2\Appointments;

use App\Models\v2\Appointments\Event;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class EventUtil
{
    private $wiringService;

    public function __construct()
    {
        $this->wiringService = new WiringServiceAppointment;
    }

    public function filterEvents($query)
    {
        $events = Event::query();

        if ((! empty($query['start'])) and (! empty($query['end']))) {
            $events->whereBetween('start', [
                Carbon::parse($query['start'])->startOfDay(),
                Carbon::parse($query['end'])->endOfDay(),
            ]);
        }

        if ((! empty($query['from'])) and (! empty($query['to']))) {
            $from = Carbon::parse($query['from'])->startOfDay();
            $to = Carbon::parse($query['to'])->endOfDay();
            $events->whereBetween('end', [$from, $to])->orderBy('end', 'asc')->get();
        }

        if (! empty($query['categories'])) {
            $events->whereIn('category_id', $query['categories']);
        }

        if (! empty($query['users'])) {
            $users = $query['users'];
            $events->whereHas('user', function ($q) use ($users) {
                $q->whereIn('user_id', $users);
            });
        }

        $events->with('user:user_id,event_id');

        if (! empty($query['search']) || ! empty($query['elders'])) {
            $events->where('remark', 'like', '%' . $query['search'] . '%')
                ->orWhere('title', 'like', '%' . $query['search'] . '%')
                ->orWhereIn('elder_id', $query['elders']);
        }

        return $events;

    }

    public function responseDetails($token, $appointment)
    {
        $elder_collection = null;
        //get elder data
        if ($appointment->elder_id) {
            $elder_collection = $this->wiringService->getEldersData([$appointment->elder_id]);
        }

        //get user data
        $user_ids = $appointment
            ->user()
            ->pluck('user_id')
            ->toArray();
        $user_collection = $this->wiringService->getUsersData($user_ids);
        $elder_key = $appointment->elder_id ?? null;
        $appointment_array = $appointment->toArray();
        $appointment_array['user'] = $user_collection;
        $appointment_array['elder'] = $elder_key ? $elder_collection->$elder_key : null;
        unset($appointment_array['elder_id']);

        return $appointment_array;
    }

    public function responseList($appointments)
    {
        //get elder data
        $elder_ids = array_filter(array_unique(Arr::pluck($appointments, 'elder_id')));
        $elder_collection = $this->wiringService->getEldersData($elder_ids);

        $appointments->getCollection()->map(function ($appointment) use ($elder_collection) {
            $appointment->elder = $appointment->elder_id ? (isset($elder_collection[$appointment->elder_id]) ? $elder_collection[$appointment->elder_id]->first() : null) : null;

            return $appointment;
        });

        return $appointments;
    }

    public function responseCalendar($appointments)
    {
        if (count($appointments) == 0) {
            return [];
        }
        //get elder data
        $elder_ids = array_filter(array_unique(Arr::pluck($appointments, 'elder_id')));
        $elder_collection = $this->wiringService->getEldersData($elder_ids);
        for ($i = 0; $i < count($appointments); $i++) {
            $appointment = $appointments[$i];
            $appointment->elder = null;
            $appointment->uid = null;
            $appointment->elder_name = null;
            if ($appointment->elder_id) {
                $elder_key = $appointment->elder_id;
                $elder_object = $elder_collection->$elder_key ?? null;
                if ($elder_object) {
                    $appointment->elder = $elder_object;
                    $appointment->uid = $elder_object->uid;
                    $appointment->elder_name = $elder_object->name;
                }
            }
            $user_ids = $appointment
                ->user()
                ->pluck('user_id')
                ->toArray() ?? [];
            $users_collection = $this->wiringService->getUsersData($user_ids)->pluck('nickname')->toArray();
            $appointment->users_name = implode(', ', $users_collection);
            unset($appointment->elder_id);
        }

        return $appointments;
    }
}
