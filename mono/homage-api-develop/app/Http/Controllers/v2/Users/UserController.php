<?php

namespace App\Http\Controllers\v2\Users;

use App\Exports\v2\Users\UserExport;
use App\Exports\v2\Users\UserListExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\v2\Users\CreateUserRequest;
use App\Http\Requests\v2\Users\GetUsersFromEvents;
use App\Http\Requests\v2\Users\UpdateUserRequest;
use App\Http\Requests\v2\Users\UserAutoCompleteNewRequest;
use App\Http\Requests\v2\Users\UserAutoCompleteRequest;
use App\Http\Requests\v2\Users\UserByEmailRequest;
use App\Http\Requests\v2\Users\UserIndexRequest;
use App\Http\Requests\v2\Users\UserReportsRequest;
use App\Http\Resources\v2\Users\UserAutocompleteCollection;
use App\Http\Resources\v2\Users\UserCollection;
use App\Http\Resources\v2\Users\UserResource;
use App\Http\Services\v2\Users\WiringServiceUser;
use App\Models\v2\Users\Role;
use App\Models\v2\Users\Team;
use App\Models\v2\Users\User;
use App\Traits\ResponseWithError;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel as MaatExcel;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class UserController extends Controller
{
    use ResponseWithError;
    private $wiringService;

    public function __construct()
    {
        $this->wiringService = new WiringServiceUser;
    }

    public function index(UserIndexRequest $request)
    {
        $request->validate([
            'sort_by' => 'nullable|string',
            'sort_dir' => 'nullable|string',
            'per_page' => 'nullable|integer',
            'status' => 'nullable|in:active,inactive',
        ]);

        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        $perPage = $request->query('per_page', 10);
        $status = $request->query('status');
        $users = User::with('teams', 'roles')
            ->when($status, function ($query) use ($status) {
                $query->where('user_status', $status === 'active' ? true : false);
            })
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
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('staff_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('email_cityu', 'like', "%{$search}%");
            })
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage);

        return new UserCollection($users);
    }

    public function store(CreateUserRequest $request)
    {
        $this->authorize('create', User::class);
        $userData = $request->except(['roles', 'password_confirmation', 'teams']);
        $userData['password'] = bcrypt($userData['password']);

        $user = User::create($userData);

        if (! $user) {
            return $this->responseWithError(500, 'Failed to create user');
        }

        $user->roles()->attach($request->roles);
        $user->teams()->attach($request->teams);
        $user->refresh();

        return response()->json([
            'data' => new UserResource($user),
        ], 201);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (! $user) {
            return $this->responseWithError(404, "Cannot find user with id {$id}");
        }

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::find($id);

        if (! $user) {
            return $this->responseWithError(404, "Cannot find user with id {$id}");
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

    public function destroy($id)
    {
        $user = User::find($id);

        if (! $user) {
            return $this->responseWithError(404, "Cannot find user with id {$id}");
        }

        $this->authorize('delete', $user);

        $user->delete();

        return response(null, 204);
    }

    // need appointment migration

    public function autocomplete(UserAutoCompleteRequest $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer',
            'name' => 'nullable|string',
            'email' => 'nullable|string',
            'team_id' => 'nullable|string',
            'team_ids' => 'nullable|string',
            'ids' => 'nullable|string',
            'date' => 'nullable|date',
        ]);
        $date = null;
        $rules = [
            'date' => 'nullable|date',
        ];
        $validated = Validator::make($request->all(), $rules);
        if (! $validated->fails()) {
            $date = $request->query('date');
        }
        $checkEvent = $this->wiringService->getTodayUserEvents($date);
        $userListToday = [];
        if ($checkEvent && count($checkEvent) > 0) {
            $userListToday = $checkEvent;
        }
        $perPage = $request->query('per_page', 25);
        $users = User::query()
            ->select('id', 'nickname', 'email')
            ->whereNotIn('id', json_decode($userListToday))
            ->when($request->query('name'), function ($query, $name) {
                $query->where('nickname', 'like', "%{$name}%");
            })
            ->when($request->query('email'), function ($query, $email) {
                $query->where('email', 'like', "%{$email}%");
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

    public function autocompletenew(UserAutoCompleteNewRequest $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer',
            'name' => 'nullable|string',
            'email' => 'nullable|string',
            'team_id' => 'nullable|string',
            'team_ids' => 'nullable|string',
            'ids' => 'nullable|string',
        ]);

        $perPage = $request->query('per_page', 25);
        $users = User::query()
            ->select('id', 'nickname', 'email')
            ->when($request->query('name'), function ($query, $name) {
                $query->where('nickname', 'like', "%{$name}%");
            })
            ->when($request->query('email'), function ($query, $email) {
                $query->where('email', 'like', "%{$email}%");
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

    public function userByEmail(UserByEmailRequest $request)
    {
        $email = $request->query('email');
        $user = User::where('email', $email)->first();
        if (! $user) {
            return response()->json([
                'data' => null,
            ], 404);
        }

        return response()->json([
            'data' => new UserResource($user),
        ], 201);
    }

    public function reports(UserReportsRequest $request)
    {
        $userCount = User::count();
        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        $size = $request->query('size') > 0 ? (int) $request->query('size') : $userCount;
        $page = $request->query('page') > 0 ? ((int) $request->query('page') - 1) * $size : 0;
        $from = $request->query('from') ? Carbon::parse($request->query('from'))->startOfDay() : null;
        $to = $request->query('to') ? Carbon::parse($request->query('to'))->endOfDay() : null;

        $users = User::query()->select(['id', 'name', 'access_role_id', 'employment_status'])
            ->with([
                'teams' => function ($query) {
                    $query->select(['teams.id', 'teams.name', 'teams.code'])->get();
                },
                'roles' => function ($query) {
                    $query->select(['roles.id'])->get();
                },
            ])
            ->when($request->query('ids'), function ($query, $ids) {
                $userIds = explode(',', $ids);
                $query->whereIn('id', $userIds);
            })
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
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
        $userNames = implode(',', $users->pluck('name')->toArray());
        $results = [];
        if (count($users) > 0) {
            $result = (array) new stdClass;
            $appointments = $this->wiringService->getStaffEvents($from, $to);
            $calls = $this->wiringService->getStaffCalls($userNames, $from, $to);
            $carePlans = $this->wiringService->getStaffCarePlans($userNames);
            $caseStatus = $this->wiringService->getUserCaseStatus();
            $caseHour = $this->wiringService->getCaseHour($from, $to);
            $user_ids = $appointments['user_ids'] ?? [];
            for ($i = 0; $i < count($users); $i++) {
                if (($from || $to) && ! in_array($users[$i]['id'], $user_ids)) {
                    continue;
                }
                $staffId = $users[$i]['id'];
                $staffName = $users[$i]['name'];
                $result['id'] = $staffId;
                $result['staff_name'] = $staffName ? $staffName : null;
                $result['teams'] = count($users[$i]['teams']) > 0 ? array_column($users[$i]['teams']->toArray(), 'name') : [];
                $result['team_ids'] = count($users[$i]['teams']) > 0 ? array_column($users[$i]['teams']->toArray(), 'id') : [];
                $result['role_ids'] = count($users[$i]['roles']) > 0 ? array_column($users[$i]['roles']->toArray(), 'id') : [];
                $result['access_role_id'] = $users[$i]['access_role_id'];
                $result['employment_status'] = $users[$i]['employment_status'];
                $result['appointment'] = 0;
                $result['followup'] = 0;
                $result['meeting'] = 0;
                $result['visits_assessment'] = 0;
                $result['case_contact_hour'] = $caseHour->$staffId ?? 0;
                $result['administrative_work'] = 0;
                $result['calls_log'] = 0;
                $result['patient_care'] = 0;
                $result['admin'] = 0;
                $result['on_going'] = 0;
                $result['pending'] = 0;
                $result['finished'] = 0;

                if ($caseStatus !== null && isset($caseStatus[$staffId])) {
                    $result['on_going'] = $caseStatus[$staffId]['on_going'];
                    $result['pending'] = $caseStatus[$staffId]['pending'];
                    $result['finished'] = $caseStatus[$staffId]['finished'];
                }
                if ($appointments !== null && isset($appointments[$staffId])) {
                    $result['appointment'] = $appointments[$staffId]['appointment'];
                    $result['followup'] = $appointments[$staffId]['followup'];
                    $result['meeting'] = $appointments[$staffId]['meeting'];
                    $result['visits_assessment'] = $appointments[$staffId]['appointment'] + $appointments[$staffId]['booking'];
                    $result['administrative_work'] = $appointments[$staffId]['administrative_work'];
                }
                if ($staffName !== null) {
                    $swipespace = preg_replace('/[^A-Za-z0-9]+/', '_', $staffName);
                    $snakeCaseStaffName = strtolower($swipespace);
                    $snakeCaseStaffName = trim($snakeCaseStaffName, '_');
                    if ($calls !== null && isset($calls[$snakeCaseStaffName])) {
                        $result['calls_log'] = $calls[$snakeCaseStaffName];
                    }
                    if ($carePlans !== null && isset($carePlans[$snakeCaseStaffName])) {
                        $result['patient_care'] = $carePlans[$snakeCaseStaffName];
                    }
                }
                array_push($results, $result);
            }
        }

        return response()->json(['data' => $results]);
    }

    public function getUsersFromEvent(GetUsersFromEvents $request)
    {
        $userIds = explode(',', $request->query('userIds'));
        $users = User::select(['id', 'name', 'email'])->whereIn('id', $userIds)->orderBy('id', 'asc')->get();
        if (count($users) === 0) {
            return response()->json([
                'data' => null,
            ], 404);
        }

        return response()->json([
            'data' => $users,
        ]);
    }

    public function getUsersSet(Request $request)
    {
        $users = User::select(['id', 'name'])->get();
        if (count($users) == 0) {
            return response()->json([
                'data' => null,
            ], 404);
        }
        $result = new stdClass;
        for ($i = 0; $i < count($users); $i++) {
            $userId = $users[$i]->id;
            if (! property_exists($result, $userId)) {
                $result->$userId = $users[$i];
            }
        }
    }

    public function exportUserReport(UserReportsRequest $request)
    {
        $request->all = true;
        $result = $this->reports($request);
        $result_collection = collect($result->getData()->data);

        return Excel::download(new UserExport($result_collection), 'staff-reports.csv', MaatExcel::CSV);
    }

    public function exportUserList(Request $request)
    {
        $users = User::with(['roles', 'teams'])->get();
        for ($i = 0; $i < count($users); $i++) {
            $users[$i]['team_ids'] = count($users[$i]['teams']) > 0 ? array_column($users[$i]['teams']->toArray(), 'id') : [];
            $users[$i]['role_ids'] = count($users[$i]['roles']) > 0 ? array_column($users[$i]['roles']->toArray(), 'id') : [];
        }
        $roles = Role::select('id')->get();
        $teams = Team::select('id')->get();

        return Excel::download(new UserListExport($users, $roles, $teams), 'staff-list.csv', MaatExcel::CSV);
    }
}
