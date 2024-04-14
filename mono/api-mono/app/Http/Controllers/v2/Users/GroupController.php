<?php

namespace App\Http\Controllers\v2\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\v2\Users\CreateGroupRequest;
use App\Http\Requests\v2\Users\GroupIndexRequest;
use App\Http\Requests\v2\Users\UpdateGroupRequest;
use App\Http\Resources\v2\Users\GroupCollection;
use App\Models\v2\Users\Group;
use App\Traits\ResponseWithError;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    use ResponseWithError;

    public function index(GroupIndexRequest $request)
    {
        $perPage = $request->query('per_page', 10);
        $groups = Group::with('users:id,name,email')
            ->when($request->query('name'), function ($query, $name) {
                $query->where('name', 'like', "%{$name}%");
            })
            ->when($request->query('code'), function ($query, $code) {
                $query->where('code', $code);
            })
            ->paginate($perPage);

        return new GroupCollection($groups);
    }

    public function store(CreateGroupRequest $request)
    {
        $this->authorize('create', Group::class);

        $user = Auth::user();
        $userJson = json_encode([
            'id' => $user->id,
            'name' => $user->name,
        ]);
        $group = Group::create([
            'name' => $request->name,
            'code' => $request->code,
            'created_by' => $userJson,
            'updated_by' => $userJson,
        ]);

        if (! $group) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => 'Failed to create user group',
                ],
            ], 500);
        }

        if ($request->has('users')) {
            $group->users()->sync($request->users);
        }

        $group->load('users:id,name,email');

        return response()->json([
            'data' => $group,
        ], 201);
    }

    public function show($id)
    {
        $group = Group::find($id);

        if (! $group) {
            return $this->responseWithError(404, "Cannot find group with id {$id}");
        }

        $group->load('users:id,name,email');

        return response()->json([
            'data' => $group,
        ]);
    }

    public function update(UpdateGroupRequest $request, $id)
    {
        $group = Group::find($id);

        if (! $group) {
            return $this->responseWithError(404, "Cannot find group with id {$id}");
        }

        $this->authorize('update', $group);

        if ($request->has('name')) {
            $user = Auth::user();
            $userJson = json_encode([
                'id' => $user->id,
                'name' => $user->name,
            ]);
            $group->update([
                'name' => $request->name,
                'updated_by' => $userJson,
            ]);
        }

        if ($request->has('users')) {
            $group->users()->sync($request->users);
        }

        $group->load('users:id,name,email');

        return response()->json([
            'data' => $group,
        ]);
    }

    public function destroy($id)
    {
        $group = Group::find($id);

        if (! $group) {
            return $this->responseWithError(404, "Cannot find group with id {$id}");
        }

        $this->authorize('delete', $group);

        $group->delete();

        return response()->json(null, 204);
    }
}
