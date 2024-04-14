<?php

namespace App\Http\Controllers\v2\Elders;

use App\Exports\v2\Elders\MeetingNotesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\v2\Elders\MeetingNotesIndexRequest;
use App\Http\Requests\v2\Elders\MeetingNotesStoreRequest;
use App\Http\Resources\v2\Elders\MeetingNotesResources;
use App\Models\v2\Elders\MeetingNotes;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MeetingNotesController extends Controller
{
    public function index(MeetingNotesIndexRequest $request)
    {
        $case_id = $request->query('cases_id');
        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');

        $query = MeetingNotes::where('cases_id', $case_id);

        if ($start_date && $end_date) {
            $query->whereBetween('created_at', [$start_date, $end_date]);
        }

        $meeting_notes = $query->orderBy('updated_at', 'asc')->get();

        if (count($meeting_notes) == 0 || ! $case_id) {
            return response()->json([
                'message' => 'Notes not found!',
                'data' => [],
            ], 404);
        }

        return MeetingNotesResources::collection($meeting_notes);
    }

    public function store(MeetingNotesStoreRequest $request)
    {
        if (! $request->cases_id) {
            return response()->json([
                'message' => 'Case id required!',
                'data' => null,
            ], 409);
        }
        $request->merge([
            'created_by' => $request->user_id,
            'updated_by' => $request->user_id,
            'created_by_name' => $request->user_name,
            'updated_by_name' => $request->user_name,
        ]);

        $validated = $request->toArray();
        $meeting_notes = MeetingNotes::create($validated);

        return response()->json([
            'message' => 'Notes was created',
            'data' => new MeetingNotesResources($meeting_notes),
        ], 201);
    }

    public function show($id)
    {
        $meeting_notes = MeetingNotes::find($id);
        if (! $meeting_notes) {
            return response()->json([
                'message' => "Note with id: {$id}, not found!",
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => "Success get note data by id: {$id}",
            'data' => new MeetingNotesResources($meeting_notes),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->merge([
            'updated_by' => $request->user_id,
            'updated_by_name' => $request->user_name,
        ]);
        $meeting_notes = MeetingNotes::find($id);
        if (! $meeting_notes) {
            return response()->json([
                'message' => "Note with id: {$id}, not found!",
                'data' => null,
            ], 404);
        }
        $meeting_notes->update($request->toArray());

        return response()->json([
            'message' => 'Notes was updated',
            'data' => new MeetingNotesResources($meeting_notes),
        ]);
    }

    public function destroy($id)
    {
        $meeting_notes = MeetingNotes::find($id);
        if (! $meeting_notes) {
            return response()->json([
                'message' => "Note with id: {$id}, not found!",
                'data' => null,
            ], 404);
        }
        $meeting_notes->delete();

        return response()->json(null, 204);
    }

    public function exportMeetingNotes(Request $request)
    {
        return Excel::download(new MeetingNotesExport, 'meeting_notes.csv');
    }
}
