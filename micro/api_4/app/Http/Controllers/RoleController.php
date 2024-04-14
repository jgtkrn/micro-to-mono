<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleCollection;
use App\Models\Role;
use App\Traits\ResponseWithError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(name="Role")
 */
class RoleController extends Controller
{
    use ResponseWithError;

    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Get(
     *     path="/user-api/v1/roles",
     *     operationId="v1GetRoles",
     *     tags={"Role"},
     *     @OA\Parameter(
     *          in="query",
     *          name="page",
     *          example="1"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="per_page",
     *          example="10"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="sort_by",
     *          description="available options: id, name, code, created_at, updated_at",
     *          example="created_at"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="sort_dir",
     *          description="available options: asc, desc",
     *          example="desc"
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Role List",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/Role")
     *              ),
     *              @OA\Property(
     *                  property="links",
     *                  type="object",
     *                  @OA\Property(
     *                      property="first",
     *                      type="string",
     *                      example="/user-api/v1/roles?page=1"
     *                  ),
     *                  @OA\Property(
     *                      property="last",
     *                      type="string",
     *                      example="/user-api/v1/roles?page=10"
     *                  ),
     *                  @OA\Property(
     *                      property="prev",
     *                      type="string",
     *                      example=null
     *                  ),
     *                  @OA\Property(
     *                      property="next",
     *                      type="string",
     *                      example="/user-api/v1/roles?page=2"
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="meta",
     *                  type="object",
     *                  @OA\Property(
     *                      property="current_page",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="from",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="last_page",
     *                      type="integer",
     *                      example="10"
     *                  ),
     *                  @OA\Property(
     *                      property="links",
     *                      type="array",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(
     *                              property="url",
     *                              type="string",
     *                              nullable=true,
     *                              example=null
     *                          ),
     *                          @OA\Property(
     *                              property="label",
     *                              type="string",
     *                              example="&laqou; Previous"
     *                          ),
     *                          @OA\Property(
     *                              property="active",
     *                              type="boolean",
     *                              example="false"
     *                          )
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="path",
     *                      type="string",
     *                      example="/user-api/v1/roles"
     *                  ),
     *                  @OA\Property(
     *                      property="per_page",
     *                      type="integer",
     *                      example="10"
     *                  ),
     *                  @OA\Property(
     *                      property="to",
     *                      type="integer",
     *                      example="10"
     *                  ),
     *                  @OA\Property(
     *                      property="total",
     *                      type="integer",
     *                      example="100"
     *                  )
     *              )
     *          )
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        $perPage = $request->query('per_page', 10);

        $roles = Role::orderBy($sortBy, $sortDir)
            ->paginate($perPage);

        return new RoleCollection($roles);
    }

    /**
     * @OA\Post(
     *     path="/user-api/v1/roles",
     *     operationId="v1CreateRole",
     *     tags={"Role"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Admin"),
     *              @OA\Property(property="code", type="string", example="admin"),
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Role created",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Role")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Role data validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="422"),
     *                  @OA\Property(property="message", type="string", example=""),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to create Role",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to create role"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     )
     * )
     *
     * @param  \App\Http\Requests\CreateRoleRequest  $request
     * @return \Illuminate\Http\Response
     */
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

        if (!$role) {
            return $this->responseWithError(500, 'Failed to create role');
        }

        return response()->json([
            'data' => $role,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/user-api/v1/roles/{roleId}",
     *     operationId="v1GetRoleDetail",
     *     tags={"Role"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="roleId",
     *          description="The id of the role",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Role detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Role")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Role not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find role with id {roleId}"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     )
     * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->responseWithError(404, "Cannot find role with id $id");
        }

        return response()->json([
            'data' => $role,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/user-api/v1/roles/{roleId}",
     *     operationId="v1UpdateRole",
     *     tags={"Role"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="roleId",
     *          description="The id of the role",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Admin")
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Role updated",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Role")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Role not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find role with id {roleId}"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Role data validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="422"),
     *                  @OA\Property(property="message", type="string", example=""),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to update Role",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to update role"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     )
     * )
     *
     * @param  \App\Http\Requests\UpdateRoleRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoleRequest $request, $id)
    {
        $user = Auth::user();
        $userJson = json_encode([
            'id' => $user->id,
            'name' => $user->name,
        ]);
        $role = Role::find($id);
        if (!$role) {
            return $this->responseWithError(404, "Cannot find role with id $id");
        }

        $updated = $role->update([
            'name' => $request->name,
            'updated_by' => $userJson,
        ]);

        if (!$updated) {
            return $this->responseWithError(500, 'Failed to update role');
        }

        return response()->json([
            'data' => $role,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/user-api/v1/roles/{roleId}",
     *     operationId="v1DeleteRole",
     *     tags={"Role"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="roleId",
     *          description="The id of the role",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=204,
     *          description="Delete Role"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Role not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find role with id {roleId}"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     )
     * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->responseWithError(404, "Cannot find role with id $id");
        }

        $role->delete();

        return response(null, 204);
    }
}
