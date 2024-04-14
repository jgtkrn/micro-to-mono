<?php

namespace App\Http\Controllers\v2\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\v2\Users\CreateRoleRequest;
use App\Http\Requests\v2\Users\RoleIndexRequest;
use App\Http\Requests\v2\Users\UpdateRoleRequest;
use App\Http\Resources\v2\Users\RoleCollection;
use App\Models\v2\Users\Role;
use App\Traits\ResponseWithError;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    use ResponseWithError;

    public function index(RoleIndexRequest $request)
    {
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        $perPage = $request->query('per_page', 10);

        $roles = Role::orderBy($sortBy, $sortDir)
            ->paginate($perPage);

        return new RoleCollection($roles);
    }

    public function store(CreateRoleRequest $request)
    {
        $user = Auth::user();
        $userJson = json_encode([
            'id' => $user->id,
            'name' => $user->name,
        ]);
        $role = Role::create([
            'name' => $request->name,
            'code' => $request->code,
            'created_by' => $userJson,
            'updated_by' => $userJson,
        ]);

        if (! $role) {
            return $this->responseWithError(500, 'Failed to create role');
        }

        return response()->json([
            'data' => $role,
        ], 201);
    }

    public function show($id)
    {
        $role = Role::find($id);

        if (! $role) {
            return $this->responseWithError(404, "Cannot find role with id {$id}");
        }

        return response()->json([
            'data' => $role,
        ]);
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $user = Auth::user();
        $userJson = json_encode([
            'id' => $user->id,
            'name' => $user->name,
        ]);
        $role = Role::find($id);
        if (! $role) {
            return $this->responseWithError(404, "Cannot find role with id {$id}");
        }

        $updated = $role->update([
            'name' => $request->name,
            'updated_by' => $userJson,
        ]);

        if (! $updated) {
            return $this->responseWithError(500, 'Failed to update role');
        }

        return response()->json([
            'data' => $role,
        ]);
    }

    public function destroy($id)
    {
        $role = Role::find($id);

        if (! $role) {
            return $this->responseWithError(404, "Cannot find role with id {$id}");
        }

        $role->delete();

        return response(null, 204);
    }
}
