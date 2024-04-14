<?php

namespace App\Http\Controllers;

use App\Http\Resources\MeetingNotes\MeetingNotesResources;
use App\Models\MeetingNotes;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MeetingNotes\MeetingNotesExport;

class MeetingNotesController extends Controller
{
    /**
     * @OA\Get(
     *     path="/elderly-api/v1/meeting-notes",
     *     tags={"meeting-notes"},
     *     summary="get notes",
     *     operationId="getNotes",
     *     @OA\Parameter(
     *          in="query",
     *          name="cases_id",
     *          example="500"
     *     ),
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

    public function index(Request $request)
    {
        $case_id = $request->query('cases_id');
        $meeting_notes = MeetingNotes::where('cases_id', $case_id)->orderBy('updated_at', 'asc')->get();
        if (count($meeting_notes) == 0 || !$case_id) {
            return response()->json([
                'message' => 'Notes not found!',
                'data' => []
            ], 404);
        }
        return MeetingNotesResources::collection($meeting_notes);
    }

    /**
     * @OA\Post(
     *     path="/elderly-api/v1/meeting-notes",
     *     tags={"meeting-notes"},
     *     summary="store new notes",
     *     operationId="postNotes",
     *     @OA\Response(
     *         response=200,
     *         description="Successful"
     *     ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\RequestBody(
     *         description="Input required case id",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"cases_id"},
     *                 @OA\Property(property="cases_id", type="integer", example=500, description="exist case id"),
     *                 @OA\Property(property="notes", type="string", example="Completed", description="meeting notes")
     *             )
     *     )
     * )
     */

    public function store(Request $request)
    {
        if(!$request->cases_id){
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

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/meeting-notes/{id}",
     *     operationId="Get Meeting Notes Detail",
     *     summary="get meeting note by id",
     *     tags={"meeting-notes"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="id",
     *          description="The id of the meeting note",
     *          @OA\Schema(
     *              type="integer",
     *              example=1
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Note detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/MeetingNotes")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Note not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find case with id {id}")
     *              )
     *          )
     *     )
     * )
     */
    public function show($id)
    {
        $meeting_notes = MeetingNotes::find($id);
        if(!$meeting_notes){
            return response()->json([
                'message' => "Note with id: {$id}, not found!",
                'data' => null
            ], 404);
        }
        return response()->json([
            'message' => "Success get note data by id: {$id}",
            'data' => new MeetingNotesResources($meeting_notes),
        ]);
    }

    /**
     * @OA\Put(
     *     path="/elderly-api/v1/meeting-notes/{id}",
     *     operationId="Update Meeting Notes Detail",
     *     summary="put meeting note by id",
     *     tags={"meeting-notes"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="id",
     *          description="The id of the meeting note",
     *          @OA\Schema(
     *              type="integer",
     *              example=1
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/MeetingNotes")
     *         )
     *     ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\RequestBody(
     *         description="Input required case id",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"cases_id"},
     *                 @OA\Property(property="cases_id", type="integer", example=500, description="exist case id"),
     *                 @OA\Property(property="notes", type="string", example="Completed", description="meeting notes")
     *             )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $request->merge([
            'updated_by' => $request->user_id,
            'updated_by_name' => $request->user_name,
        ]);
        $meeting_notes = MeetingNotes::find($id);
        if(!$meeting_notes){
            return response()->json([
                'message' => "Note with id: {$id}, not found!",
                'data' => null
            ], 404);
        }
        $meeting_notes->update($request->toArray());
        return response()->json([
            'message' => 'Notes was updated',
            'data' => new MeetingNotesResources($meeting_notes),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/elderly-api/v1/meeting-notes/{id}",
     *     operationId="Delete Meeting Notes",
     *     summary="delete meeting note by id",
     *     tags={"meeting-notes"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="id",
     *          description="The id of the meeting note",
     *          @OA\Schema(
     *              type="integer",
     *              example=1
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful"
     *     ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy($id)
    {
        $meeting_notes = MeetingNotes::find($id);
        if(!$meeting_notes){
            return response()->json([
                'message' => "Note with id: {$id}, not found!",
                'data' => null
            ], 404);
        }
        $meeting_notes->delete();
        return response()->json(null, 204);
    }

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/meeting-notes-export",
     *     tags={"meeting-notes"},
     *     summary="export meeting notes to csv",
     *     operationId="exportMeetingNotes",
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
    public function exportMeetingNotes(Request $request)
    {
        return Excel::download(new MeetingNotesExport, 'meeting_notes.csv');
    }
}
