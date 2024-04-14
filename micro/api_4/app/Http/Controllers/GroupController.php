<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupCollection;
use App\Models\Group;
use App\Traits\ResponseWithError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(name="Group")
 */
class GroupController extends Controller
{
    use ResponseWithError;

    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Get(
     *     path="/user-api/v1/groups",
     *     operationId="v1GetGroups",
     *     tags={"Group"},
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
     *          description="Group List",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/Group")
     *              ),
     *              @OA\Property(
     *                  property="links",
     *                  type="object",
     *                  @OA\Property(
     *                      property="first",
     *                      type="string",
     *                      example="/user-api/v1/groups?page=1"
     *                  ),
     *                  @OA\Property(
     *                      property="last",
     *                      type="string",
     *                      example="/user-api/v1/groups?page=10"
     *                  ),
     *                  @OA\Property(
     *                      property="prev",
     *                      type="string",
     *                      example=null
     *                  ),
     *                  @OA\Property(
     *                      property="next",
     *                      type="string",
     *                      example="/user-api/v1/groups?page=2"
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
     *                      example="/user-api/v1/groups"
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
        $perPage = $request->query('per_page', 10);
        $groups = Group::with('users:id,name,email')
            ->when($request->query('name'), function ($query, $name) {
                $query->where('name', 'like', "%$name%");
            })
            ->when($request->query('code'), function ($query, $code) {
                $query->where('code', $code);
            })
            ->paginate($perPage);

        return new GroupCollection($groups);
    }

    /**
     * @OA\Post(
     *     path="/user-api/v1/groups",
     *     operationId="v1CreateGroup",
     *     tags={"Group"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Nurse Group 007"),
     *              @OA\Property(property="code", type="string", example="unique-group-code-007"),
     *              @OA\Property(
     *                  property="users",
     *                  type="array",
     *                  @OA\Items(type="integer"),
     *                  example={1, 2, 3, 4},
     *              ),
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Group created",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Group")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Group data validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="422"),
     *                  @OA\Property(property="message", type="string", example=""),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to create Group",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to create group"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     )
     * )
     *
     * @param  \App\Http\Requests\CreateGroupRequest  $request
     * @return \Illuminate\Http\Response
     */
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

        if (!$group) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => 'Failed to create user group',
                ]
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

    /**
     * @OA\Get(
     *     path="/user-api/v1/groups/{groupId}",
     *     operationId="v1GetGroupDetail",
     *     tags={"Group"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="groupId",
     *          description="The id of the group",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Group detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Group")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Group not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find group with id {groupId}"),
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
        $group = Group::find($id);

        if (!$group) {
            return $this->responseWithError(404, "Cannot find group with id $id");
        }

        $group->load('users:id,name,email');

        return response()->json([
            'data' => $group,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/user-api/v1/groups/{groupId}",
     *     operationId="v1UpdateGroup",
     *     tags={"Group"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="groupId",
     *          description="The id of the group",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Nurse Group 007"),
     *              @OA\Property(
     *                  property="users",
     *                  type="array",
     *                  @OA\Items(type="integer"),
     *                  example={1, 2, 3, 4}
     *              ),
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Group updated",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Group")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Group not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find group with id {groupId}"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Group data validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="422"),
     *                  @OA\Property(property="message", type="string", example=""),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to update Group",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to update group"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     )
     * )
     *
     * @param  \App\Http\Requests\UpdateGroupRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGroupRequest $request, $id)
    {
        $group = Group::find($id);

        if (!$group) {
            return $this->responseWithError(404, "Cannot find group with id $id");
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

    /**
     * @OA\Delete(
     *     path="/user-api/v1/groups/{groupId}",
     *     operationId="v1DeleteGroup",
     *     tags={"Group"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="groupId",
     *          description="The id of the group",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=204,
     *          description="Delete Group"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Group not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find group with id {groupId}"),
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
        $group = Group::find($id);

        if (!$group) {
            return $this->responseWithError(404, "Cannot find group with id $id");
        }

        $this->authorize('delete', $group);

        $group->delete();

        return response()->json(null, 204);
    }
}
