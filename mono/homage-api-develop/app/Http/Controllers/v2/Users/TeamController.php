<?php

namespace App\Http\Controllers\v2\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\v2\Users\CreateTeamRequest;
use App\Http\Requests\v2\Users\TeamIndexRequest;
use App\Http\Requests\v2\Users\UpdateTeamRequest;
use App\Http\Resources\v2\Users\TeamCollection;
use App\Models\v2\Users\Team;
use App\Traits\ResponseWithError;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    use ResponseWithError;

    public function index(TeamIndexRequest $request)
    {
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        $perPage = $request->query('per_page', 10);

        $teams = Team::orderBy($sortBy, $sortDir)
            ->paginate($perPage);

        return new TeamCollection($teams);
    }

    public function store(CreateTeamRequest $request)
    {
        $user = Auth::user();
        $userJson = json_encode([
            'id' => $user->id,
            'name' => $user->name,
        ]);
        $team = Team::create([
            'name' => $request->name,
            'code' => $request->code,
            'created_by' => $userJson,
            'updated_by' => $userJson,
        ]);

        if (! $team) {
            return $this->responseWithError(500, 'Failed to create Team');
        }

        return response()->json([
            'data' => $team,
        ], 201);
    }

    public function show($id)
    {
        $team = Team::find($id);

        if (! $team) {
            return $this->responseWithError(404, "Cannot find team with id {$id}");
        }

        return response()->json([
            'data' => $team,
        ]);
    }

    public function update(UpdateTeamRequest $request, $id)
    {
        $user = Auth::user();
        $userJson = json_encode([
            'id' => $user->id,
            'name' => $user->name,
        ]);
        $team = Team::find($id);

        if (! $team) {
            return $this->responseWithError(404, "Cannot find team with id {$id}");
        }

        $updated = $team->update([
            'name' => $request->name,
            'updated_by' => $userJson,
        ]);

        if (! $updated) {
            return $this->responseWithError(500, 'Failed to update Team');
        }

        return response()->json([
            'data' => $team,
        ]);
    }

    public function destroy($id)
    {
        $team = Team::find($id);

        if (! $team) {
            return $this->responseWithError(404, "Cannot find team with id {$id}");
        }

        $team->delete();

        return response(null, 204);
    }
}
