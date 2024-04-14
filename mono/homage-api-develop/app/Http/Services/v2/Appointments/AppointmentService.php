<?php

namespace App\Http\Services\v2\Appointments;

use App\Models\v2\Appointments\Event;
use App\Models\v2\Appointments\File;
use App\Models\v2\Appointments\UserEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppointmentService
{
    public function store(Request $request)
    {
        //Create event
        $event = $this->createOrUpdateEvent($request);

        //Attach user to event
        $user_ids = $this->getUsersId($request->user_ids);
        $user_events = [];
        foreach ($user_ids as $user_id) {
            $user_event = new UserEvent([
                'user_id' => $user_id,
            ]);
            array_push($user_events, $user_event);
        }
        $event->user()->saveMany($user_events);

        //attach files to appointment
        if ($request->attachment_ids != null) {
            $attachment_ids = $request->attachment_ids;
            $files = File::findMany($attachment_ids);
            $event->file()->saveMany($files);
        }

        $this->deleteOrphanedFiles($request->user_id);

        return $event;
    }

    public function update(Request $request, $appointment)
    {
        $event = $this->createOrUpdateEvent($request, $appointment);

        //sync user to event
        $user_ids = $this->getUsersId($request->user_ids);
        $user_events = [];
        foreach ($user_ids as $user_id) {
            $user_event = new UserEvent([
                'user_id' => $user_id,
            ]);
            array_push($user_events, $user_event);
        }
        $event->user()->delete();
        $event->user()->saveMany($user_events);

        $current_file_ids = $event
            ->file()
            ->get()
            ->pluck('id')
            ->toArray();
        $attachment_ids = $request->attachment_ids;
        if ($attachment_ids == null) {
            $attachment_ids = [];
        }

        //delete old file not exist in new array
        foreach ($current_file_ids as $file_id) {
            if (! in_array($file_id, $attachment_ids)) {
                $file = File::findOrFail($file_id);
                Storage::delete($file->disk_name);
                $file->delete();
            }
        }

        //add new file not exist in old array
        foreach ($attachment_ids as $file_id) {
            if (! in_array($file_id, $current_file_ids)) {
                $file = File::findOrFail($file_id);
                $event->file()->save($file);
            }
        }

        $this->deleteOrphanedFiles($request->user_id);

        return $event;
    }

    public function destroy($appointment)
    {
        $files = $appointment->file()->get();
        foreach ($files as $file) {
            Storage::delete($file->disk_name);
            $file->delete();
        }
        $appointment->delete();
    }

    public function massDestroy($appointments)
    {
        foreach ($appointments as $appointment) {
            $this->destroy($appointment);
        }
    }

    private function deleteOrphanedFiles($user_id)
    {
        //delete appointment from the user without event (file uploaded but canceled)
        $orphanedFiles = File::where('user_id', $user_id)->where('event_id', null)->get();
        foreach ($orphanedFiles as $orphanedFile) {
            Storage::delete($orphanedFile->disk_name);
            $orphanedFile->delete();
        }
    }

    private function calculateTime($day, $hour_minute)
    {
        $date = new Carbon($day);
        $date = $date->startOfDay();
        $day_date = clone $date;

        $time = Carbon::parse($hour_minute)->format('H:i');
        $hour = intval(substr($time, 0, 2));
        $minute = intval(substr($time, 3, 2));

        $result = $day_date->addHours($hour)->addMinutes($minute);

        return $result;
    }

    private function getUsersId($request_user_ids)
    {
        $user_ids = [];
        $external_email = [];

        foreach ($request_user_ids as $user_id) {
            if (is_numeric($user_id)) {
                array_push($user_ids, $user_id);
            } else {
                array_push($external_email, $user_id);
            }
        }

        // if (count($external_email) > 0) {
        //     foreach ($external_email as $email) {
        //         $new_id = $this->createExternalUser($email);
        //         array_push($user_ids, $new_id);
        //     }
        // }

        return $user_ids;
    }

    // private function createExternalUser($email)
    // {
    //     $user = new User();
    //     $user->name = $email;
    //     $user->email = $email;
    //     $user->role_id = 4; //external users
    //     $user->save();
    //     return $user->id;
    // }

    private function createOrUpdateEvent(Request $request, $event = null)
    {
        if ($event == null) {
            $event = new Event;
        }
        $event->title = $request->title;
        $event->remark = $request->remark;
        $event->category_id = $request->category_id;
        $event->case_id = $request->case_id;
        $event->elder_id = $request->elder_id;
        $event->start = $this->calculateTime(
            $request->day_date,
            $request->start_time
        );
        $event->end = $this->calculateTime(
            $request->day_date,
            $request->end_time
        );
        $event->created_by = ($event->created_by == null) ? $request->created_by : $event->created_by;
        $event->updated_by = $request->updated_by;
        $event->created_by_name = ($event->created_by_name == null) ? $request->created_by_name : $event->created_by_name;
        $event->updated_by_name = $request->updated_by_name;
        $event->save();

        return $event;
    }
}
