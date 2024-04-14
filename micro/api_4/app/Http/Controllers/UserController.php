<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Team;
use App\Models\Role;
use http\Env\Response;
use App\Exports\UserExport;
use Illuminate\Http\Request;
use App\Traits\ResponseWithError;
use App\Http\Resources\UserResource;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\UserCollection;
use App\Http\Services\ExternalService;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserAutocompleteCollection;

/**
 * @OA\Tag(name="User")
 */
class UserController extends Controller
{
    use ResponseWithError;
    private $externalService;

    public function __construct() {
        $this->middleware('auth:sanctum')->except(['userByEmail', 'getUsersFromEvent']);
        $this->externalService = new ExternalService();
    }

    /**
     * @OA\Get(
     *     path="/user-api/v1/users",
     *     operationId="v1GetUsers",
     *     tags={"User"},
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
     *          name="ids",
     *          description="user id separated by comma",
     *          example="1,2,3,4"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="team_id",
     *          description="id of a team",
     *          example="1"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="sort_by",
     *          description="available options: id, name, email, created_at, updated_at",
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
     *          description="User List",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/User")
     *              ),
     *              @OA\Property(
     *                  property="links",
     *                  type="object",
     *                  @OA\Property(
     *                      property="first",
     *                      type="string",
     *                      example="/user-api/v1/users?page=1"
     *                  ),
     *                  @OA\Property(
     *                      property="last",
     *                      type="string",
     *                      example="/user-api/v1/users?page=10"
     *                  ),
     *                  @OA\Property(
     *                      property="prev",
     *                      type="string",
     *                      example=null
     *                  ),
     *                  @OA\Property(
     *                      property="next",
     *                      type="string",
     *                      example="/user-api/v1/users?page=2"
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
     *                      example="/user-api/v1/users"
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        $perPage = $request->query('per_page', 10);

        $users = User::with('teams', 'roles')
            ->when($request->query('ids'), function ($query, $ids) {
                $userIds = explode(',', $ids);
                $query->whereIn('id', $userIds);
            })
            ->when($request->query('team_id'), function ($query, $teamId) {
                $query->whereHas('teams', function (Builder $team) use ($teamId) {
                    $team->where('teams.id', $teamId);
                });
            })
            ->when($request->query('search'), function ($query, $search) {
                $query
                    ->where('name', 'like', "%$search%")
                    ->orWhere('staff_number', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('email_cityu', 'like', "%$search%");
            })
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage);
        return new UserCollection($users);
    }

    /**
     * @OA\Post(
     *     path="/user-api/v1/users",
     *     operationId="v1CreateUser",
     *     tags={"User"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="John Snow"),
     *              @OA\Property(property="user_status", type="boolean", example=true),
     *              @OA\Property(property="email", type="string", format="email", example="john.snow@stark.com"),
     *              @OA\Property(property="email_cityu", type="string", format="email", example="john.snow@cityu.edu.hk"),
     *              @OA\Property(property="nickname", type="string", example="john"),
     *              @OA\Property(property="staff_number", type="string", example="staff0001"),
     *              @OA\Property(property="access_role_id", type="integer", example="1"),
     *              @OA\Property(
     *                  property="roles",
     *                  type="array",
     *                  example={1, 2, 3},
     *                  @OA\Items(type="integer", example=1)
     *              ),
     *              @OA\Property(
     *                  property="teams",
     *                  type="array",
     *                  example={1,2,3,4},
     *                  @OA\Items(type="integer", example=1)
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="User created",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/User")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="User data validation fail",
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
     *          description="Failed to create User",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to create user"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     )
     * )
     *
     * @param  \App\Http\Requests\CreateUserRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateUserRequest $request)
    {
        $this->authorize('create', User::class);
        $userData = $request->except(['roles', 'password_confirmation', 'teams']);
        $userData['password'] = bcrypt($userData['password']);

        $user = User::create($userData);

        if (!$user) {
            return $this->responseWithError(500, 'Failed to create user');
        }

        $user->roles()->attach($request->roles);
        $user->teams()->attach($request->teams);
        $user->refresh();

        return response()->json([
            'data' => new UserResource($user),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/user-api/v1/users/{userId}",
     *     operationId="v1GetUserDetail",
     *     tags={"User"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="userId",
     *          description="The id of the user",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="User detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/User")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find user with id {userId}"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     )
     * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->responseWithError(404, "Cannot find user with id $id");
        }

        return new UserResource($user);
    }

    /**
     * @OA\Put(
     *     path="/user-api/v1/users/{userId}",
     *     operationId="v1UpdateUser",
     *     tags={"User"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="userId",
     *          description="The id of the user",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="John Snow"),
     *              @OA\Property(property="user_status", type="boolean", example=true),
     *              @OA\Property(property="email", type="string", format="email", example="john.snow@stark.com"),
     *              @OA\Property(property="email_cityu", type="string", format="email", example="john.snow@cityu.edu.hk"),
     *              @OA\Property(property="nickname", type="string", example="john"),
     *              @OA\Property(property="staff_number", type="string", example="staff0001"),
     *              @OA\Property(property="access_role_id", type="integer", example="1"),
     *              @OA\Property(
     *                  property="roles",
     *                  type="array",
     *                  example={1, 2, 3},
     *                  @OA\Items(type="integer", example=1)
     *              ),
     *              @OA\Property(
     *                  property="teams",
     *                  type="array",
     *                  example={1,2,3,4},
     *                  @OA\Items(type="integer", example=1)
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Updated User",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/User")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="User data validation fail",
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
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find user with id {userId}"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer")),
     *              )
     *          )
     *     )
     * )
     *
     * @param  \App\Http\Requests\UpdateUserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->responseWithError(404, "Cannot find user with id $id");
        }

        $this->authorize('update', $user);

        $user->name = $request->has('name') ? $request->name : $user->name;
        $user->nickname = $request->has('nickname') ? $request->nickname : $user->nickname;
        $user->user_status = $request->has('user_status') ? $request->user_status : $user->user_status;
        $user->staff_number = $request->has('staff_number') ? $request->staff_number : $user->staff_number;
        $user->phone_number = $request->has('phone_number') ? $request->phone_number : $user->phone_number;
        $user->email = $request->has('email') ? $request->email : $user->email;
        $user->email_cityu = $request->has('email_cityu') ? $request->email_cityu : $user->email_cityu;
        $user->employment_status = $request->has('employment_status') ? $request->employment_status : $user->employment_status;
        $user->access_role_id = $request->has('access_role_id') ? $request->access_role_id : $user->access_role_id;
        $user->save();

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        if ($request->has('teams')) {
            $user->teams()->sync($request->teams);
        }

        return new UserResource($user);
    }

    /**
     * @OA\Delete(
     *     path="/user-api/v1/users/{userId}",
     *     operationId="v1DeleteUser",
     *     tags={"User"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="userId",
     *          description="The id of the user",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=204,
     *          description="Delete User"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find user with id {userId}"),
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
        $user = User::find($id);

        if (!$user) {
            return $this->responseWithError(404, "Cannot find user with id $id");
        }

        $this->authorize('delete', $user);

        $user->delete();

        return response(null, 204);
    }

    /**
     * @OA\Get(
     *     path="/user-api/v1/users/autocomplete",
     *     operationId="v1GetUserAutocomplete",
     *     tags={"User"},
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
     *          name="name",
     *          description="username filter",
     *          example="me"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="email",
     *          description="email address filter",
     *          example="john.snow@stark.com"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="ids",
     *          description="user id separated by comma",
     *          example="1,2,3,4,5"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="team_id",
     *          description="id of a team. old method",
     *          example="1"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="team_ids",
     *          description="team id separated by comma. new method",
     *          example="1,2,3,4,5"
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="User List",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object",
     *                      @OA\Property(
     *                          property="id",
     *                          type="integer",
     *                          example="1"
     *                      ),
     *                      @OA\Property(
     *                          property="name",
     *                          type="string",
     *                          example="John Snow"
     *                      ),
     *                      @OA\Property(
     *                          property="email",
     *                          type="string",
     *                          example="john.snow@stark.com"
     *                      ),
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="links",
     *                  type="object",
     *                  @OA\Property(
     *                      property="first",
     *                      type="string",
     *                      example="/user-api/v1/users/autocomplete?page=1"
     *                  ),
     *                  @OA\Property(
     *                      property="last",
     *                      type="string",
     *                      example="/user-api/v1/users/autocomplete?page=10"
     *                  ),
     *                  @OA\Property(
     *                      property="prev",
     *                      type="string",
     *                      example=null
     *                  ),
     *                  @OA\Property(
     *                      property="next",
     *                      type="string",
     *                      example="/user-api/v1/users/autocomplete?page=2"
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
     *                      example="/user-api/v1/users/autocomplete"
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function autocomplete(Request $request)
    {
        $date = null;
        $rules = [
            'date' => 'nullable|date',
        ];
        $validated = Validator::make($request->all(), $rules);
        if(!$validated->fails()){
            $date = $request->query('date');
        }
        $checkEvent = $this->externalService->getTodayUserEvents($date);
        $userListToday = json_encode([]);
        if($checkEvent !== null){
            $userListToday = $checkEvent->getBody()->getContents();
        }
        $perPage = $request->query('per_page', 25);
        $users = User::query()
            ->select('id', 'nickname', 'email')
            ->whereNotIn('id', json_decode($userListToday))
            ->when($request->query('name'), function ($query, $name) {
                $query->where('nickname', 'like', "%$name%");
            })
            ->when($request->query('email'), function ($query, $email) {
                $query->where('email', 'like', "%$email%");
            })
            ->when($request->query('team_id'), function ($query, $teamId) {
                $query->whereHas('teams', function (Builder $team) use ($teamId) {
                    $team->where('teams.id', $teamId);
                });
            })
            ->when($request->query('team_ids'), function ($query, $teamIds) {
                $team_id_list = explode(',', $teamIds);
                $query->whereHas('teams', function (Builder $team) use ($team_id_list) {
                    $team->whereIn('teams.id', $team_id_list);
                });
            })
            ->when($request->query('ids'), function ($query, $ids) {
                $userIds = explode(',', $ids);
                $query->whereIn('id', $userIds);
            })
            ->orderBy('nickname')
            ->paginate($perPage);

        return new UserAutocompleteCollection($users);
    }

    public function autocompletenew(Request $request)
    {
        $perPage = $request->query('per_page', 25);
        $users = User::query()
            ->select('id', 'nickname', 'email')
            ->when($request->query('name'), function ($query, $name) {
                $query->where('nickname', 'like', "%$name%");
            })
            ->when($request->query('email'), function ($query, $email) {
                $query->where('email', 'like', "%$email%");
            })
            ->when($request->query('team_id'), function ($query, $teamId) {
                $query->whereHas('teams', function (Builder $team) use ($teamId) {
                    $team->where('teams.id', $teamId);
                });
            })
            ->when($request->query('team_ids'), function ($query, $teamIds) {
                $team_id_list = explode(',', $teamIds);
                $query->whereHas('teams', function (Builder $team) use ($team_id_list) {
                    $team->whereIn('teams.id', $team_id_list);
                });
            })
            ->when($request->query('ids'), function ($query, $ids) {
                $userIds = explode(',', $ids);
                $query->whereIn('id', $userIds);
            })
            ->orderBy('nickname')
            ->paginate($perPage);

        return new UserAutocompleteCollection($users);
    }

    public function userByEmail(Request $request) {
        $email = $request->query('email');
        $user = User::where('email', $email)->first();
        if(!$user){
            return response()->json([
                'data' => null,
            ], 404);
        }
        return response()->json([
            'data' => new UserResource($user),
        ], 201);
    }

    public function reports(Request $request) {
        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        $size = $request->query('size') > 0 ? (int)$request->query('size') : 10;
        $page = $request->query('page') > 0 ? ((int)$request->query('page') - 1) * $size : 0;

        $users = User::query()->select(['id', 'name', 'access_role_id', 'employment_status'])
            ->with([
                'teams' => function($query){
                    $query->select(['teams.id', 'teams.name', 'teams.code'])->get();
                },
                'roles' => function($query){
                    $query->select(['roles.id'])->get();
                }
            ])
            ->when($request->query('ids'), function ($query, $ids) {
                $userIds = explode(',', $ids);
                $query->whereIn('id', $userIds);
            })
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%$search%");
            })
            ->skip($page)
            ->take($size)
            ->when($request->query('team_id'), function ($query, $teamId) {
                $query->whereHas('teams', function (Builder $team) use ($teamId) {
                    $team->where('teams.id', $teamId);
                });
            })
            ->orderBy($sortBy, $sortDir)
            ->get();
        $userIds = implode(',', $users->pluck('id')->toArray());
        $userNames = implode(',', $users->pluck('name')->toArray());
        $results = array();
        if(count($users) > 0){
            $appointments = $this->externalService->getStaffEvents($userIds);
            // return $appointments;
            $calls = $this->externalService->getStaffCalls($userNames);
            $carePlans = $this->externalService->getStaffCarePlans($userNames);
            $caseStatus = $this->externalService->getCasesStatus();
            
            for($i = 0; $i<count($users); $i++){
                $staffId = $users[$i]['id'];
                $staffName = $users[$i]['name'];
                $results[$i]['id'] = $staffId;
                $results[$i]['staff_name'] = $staffName ? $staffName : null;
                $results[$i]['teams'] = count($users[$i]['teams']) > 0 ? array_column($users[$i]['teams']->toArray(), 'name') : [];
                $results[$i]['team_ids'] = count($users[$i]['teams']) > 0 ? array_column($users[$i]['teams']->toArray(), 'id') : [];
                $results[$i]['role_ids'] = count($users[$i]['roles']) > 0 ? array_column($users[$i]['roles']->toArray(), 'id') : [];
                $results[$i]['access_role_id'] = $users[$i]['access_role_id'];
                $results[$i]['employment_status'] = $users[$i]['employment_status'];
                $results[$i]['appointment'] = 0;
                $results[$i]['followup'] = 0;
                $results[$i]['meeting'] = 0;
                $results[$i]['reservations'] = 0;
                $results[$i]['case_contact_hour'] = 0;
                $results[$i]['administrative_work'] = 0;
                $results[$i]['calls_log'] = 0;
                $results[$i]['patient_care'] = 0;
                $results[$i]['admin'] = 0;
                $results[$i]['on_going'] = 0;
                $results[$i]['pending'] = 0;
                $results[$i]['finished'] = 0;

                if($caseStatus !== null && isset($caseStatus[$staffId])){
                    $results[$i]['on_going'] = $caseStatus[$staffId]['on_going'];
                    $results[$i]['pending'] = $caseStatus[$staffId]['pending'];
                    $results[$i]['finished'] = $caseStatus[$staffId]['finished'];
                    $results[$i]['case_contact_hour'] = $caseStatus[$staffId]['total_visit'];
                }
                if($appointments !== null && isset($appointments[$staffId])){
                    $results[$i]['appointment'] = $appointments[$staffId]['appointment'];
                    $results[$i]['followup'] = $appointments[$staffId]['followup'];
                    $results[$i]['meeting'] = $appointments[$staffId]['meeting'];
                    $results[$i]['reservations'] = $appointments[$staffId]['appointment'] + $appointments[$staffId]['booking'];
                    $results[$i]['administrative_work'] = $appointments[$staffId]['administrative_work'];                   
                }
                if($staffName !== null){
                    $swipespace = preg_replace('/[^A-Za-z0-9]+/', '_', $staffName);
                    $snakeCaseStaffName = strtolower($swipespace);
                    $snakeCaseStaffName = trim($snakeCaseStaffName, '_');
                    if($calls !== null && isset($calls[$snakeCaseStaffName])){
                        $results[$i]['calls_log'] = $calls[$snakeCaseStaffName];
                    }
                    if($carePlans !== null && isset($carePlans[$snakeCaseStaffName])){
                        $results[$i]['patient_care'] = $carePlans[$snakeCaseStaffName];
                    }
                }
                // $results[$i]['reservation'] = ;
            }
        }
        return response()->json(['data' => $results]);
    }

    public function getUsersFromEvent(Request $request)
    {
        $userIds = explode(',', $request->query('userIds'));
        $users = User::select(['id', 'name', 'email'])->whereIn('id', $userIds)->orderBy('id', 'asc')->get();
        if(count($users) === 0){
            return response()->json([
                'data' => null
            ], 404);
        }
        return response()->json([
            'data' => $users
        ]);
    }

    public function getUsersSet(Request $request)
    {
        $users = User::select(['id', 'name'])->get();
        $result = new \stdClass();
        if(count($users) > 0) {
            for($i = 0; $i < count($users); $i++){
                $userId = $users[$i]->id;
                if(!property_exists($result, $userId)){
                    $result->$userId = $users[$i];
                }
            }
        } else {
            return response()->json([
                'data' => null
            ], 404);
        }
        return response()->json([
            'data' => $result
        ]);
    }

    public function exportUserReport(Request $request)
    {
        $result = $this->reports($request);
        $teams = Team::select('id')->get();
        $roles = Role::select('id')->get();
        $result_collection = collect($result->getData()->data);
        return Excel::download(new UserExport($result_collection, $roles, $teams), 'staff-reports.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
