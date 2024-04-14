<?php

namespace App\Utils;

use App\Http\Services\ExternalService;
use App\Models\Event;
use App\Models\UserEvent;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class EventUtil
{
    private $externalService;

    public function __construct()
    {
        $this->externalService = new ExternalService();
    }

    public function FilterEvents($query)
    {
        $events = Event::query();

        if ((!empty($query['start'])) and (!empty($query['end']))) {
            $events->whereBetween("start", [
                Carbon::parse($query['start'])->startOfDay(),
                Carbon::parse($query['end'])->endOfDay(),
            ]);
        }

        if ((!empty($query['from'])) and (!empty($query['to']))) {
            $from = Carbon::parse($query['from'])->startOfDay();
            $to = Carbon::parse($query['to'])->endOfDay();
            $events->whereBetween('end', [$from, $to])->orderBy('end', 'asc')->get();
        }

        if (!empty($query['categories'])) {
            $events->whereIn("category_id", $query['categories']);
        }

        if (!empty($query['users'])) {
            $users = $query['users'];
            $events->whereHas("user", function ($q) use ($users) {
                $q->whereIn("user_id", $users);
            });
        }
        
        $events->with("user:user_id,event_id");

        if (!empty($query['search']) || !empty($query['elders'])) {
            $events->where("remark", "like", "%" . $query['search'] . "%")
                ->orWhere("title", "like", "%" . $query['search'] . "%")
                ->orWhereIn("elder_id", $query['elders']);
        }

        return $events;
    }

    public function ResponseDetails($token, $appointment)
    {
        //get elder data
        if ($appointment->elder_id) {
            $elder_collection = $this->externalService->getEldersData(array($appointment->elder_id));
        }

        //get user data
        $user_ids = $appointment
            ->user()
            ->pluck("user_id")
            ->toArray();
        $user_collection = $this->externalService->getUsersData($token, $user_ids);

        $appointment_array = $appointment->toArray();
        $appointment_array["user"] = $user_collection;
        $appointment_array["elder"] = $appointment->elder_id ? $elder_collection[$appointment->elder_id]->first() : null;
        unset($appointment_array["elder_id"]);
        return $appointment_array;
    }

    public function ResponseList($appointments)
    {
        //get elder data
        $elder_ids = array_filter(array_unique(Arr::pluck($appointments, 'elder_id')));
        $elder_collection = $this->externalService->getEldersData($elder_ids);

        $appointments->getCollection()->map(function ($appointment) use ($elder_collection) {
            $appointment->elder = $appointment->elder_id ? (isset($elder_collection[$appointment->elder_id]) ? $elder_collection[$appointment->elder_id]->first(): null) : null;
            return $appointment;
        });
        return $appointments;
    }

    public function ResponseCalendar($token, $appointments)
    {
        if (count($appointments) == 0) {
            return array();
        }

        //get user data
        $user_events = UserEvent::whereBelongsTo($appointments)->get();
        $user_ids = array_unique(Arr::pluck($user_events, 'user_id'));
        $users_collection = $this->externalService->getUsersData($token, $user_ids);

        //get elder data
        $elder_ids = array_filter(array_unique(Arr::pluck($appointments, 'elder_id')));
        $elder_collection = $this->externalService->getEldersData($elder_ids);

        $result_with_elder = $appointments->map(function ($appointment) use ($elder_collection, $users_collection) {
            $elder_object = $elder_collection[$appointment->elder_id] ?? null;
            if ($elder_object) {
                $appointment->elder = $elder_object->first();
                $appointment->uid = $elder_object->first()['uid'];
                $appointment->elder_name = $elder_object->first()['name'];
            }

            $user_ids_per_appointment = $appointment
                ->user()
                ->pluck("user_id")
                ->toArray();
            $user_name_array = $users_collection->whereIn('id', $user_ids_per_appointment)->pluck('name')->toArray();
            $appointment["users_name"] = implode(', ', $user_name_array);
            unset($appointment["elder_id"]);

            return $appointment;
        });

        return $result_with_elder;
    }
}
