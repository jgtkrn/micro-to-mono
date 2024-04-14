<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

    public function test_get_user_list()
    {
        $users = User::factory()->count(10)->create();
        $response = $this->get('/user-api/v1/users');

        $response->assertOk();
        $response->assertJsonCount($users->count(), 'data');
    }

    public function test_create_user_success()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create([
            'access_role_id' => 1 //admin
        ]);
        $admin->roles()->attach($adminRole->id);
        $team = Team::factory()->create();
        $role = Role::factory()->create();
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'nickname' => $this->faker->userName,
            'staff_number' => $this->faker->numerify('staff#####'),
            'password' => 'P@ssW0rd!',
            'password_confirmation' => 'P@ssW0rd!',
            'roles' => [
                $role->id,
            ],
            'teams' => [
                $team->id
            ],
        ];

        $response = $this->actingAs($admin)->postJson('/user-api/v1/users', $data);
        $response->assertCreated();
        $response->assertJsonPath('data.name', $data['name']);
        $response->assertJsonPath('data.nickname', $data['nickname']);
        $response->assertJsonPath('data.staff_number', $data['staff_number']);
        $response->assertJsonPath('data.email', $data['email']);
        $response->assertJsonPath('data.roles.0.id', $data['roles'][0]);
        $response->assertJsonPath('data.teams.0.id', $data['teams'][0]);

        $user = User::find($response->json()['data']['id']);
        $isPasswordHashMatch = Hash::check($data['password'], $user->password);
        $this->assertTrue($isPasswordHashMatch);

        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_manager_can_create_user()
    {
        $managerRole = Role::factory()->create([
            'name' => 'Manager',
            'code' => 'manager',
        ]);
        $manager = User::factory()->create([
            'access_role_id' => 2 //manager
        ]);
        $manager->roles()->attach($managerRole->id);
        $team = Team::factory()->create();
        $role = Role::factory()->create();
        $data = [
            'name' => $this->faker->name,
            'nickname' => $this->faker->userName,
            'staff_number' => $this->faker->numerify('staff#####'),
            'email' => $this->faker->safeEmail,
            'password' => 'P@ssW0rd!',
            'password_confirmation' => 'P@ssW0rd!',
            'roles' => [
                $role->id,
            ],
            'teams' => [
                $team->id
            ],
        ];

        $response = $this->actingAs($manager)->postJson('/user-api/v1/users', $data);
        $response->assertCreated();
        $response->assertJsonPath('data.name', $data['name']);
        $response->assertJsonPath('data.nickname', $data['nickname']);
        $response->assertJsonPath('data.staff_number', $data['staff_number']);
        $response->assertJsonPath('data.email', $data['email']);
        $response->assertJsonPath('data.roles.0.id', $data['roles'][0]);
        $response->assertJsonPath('data.teams.0.id', $data['teams'][0]);

        $user = User::find($response->json()['data']['id']);
        $isPasswordHashMatch = Hash::check($data['password'], $user->password);
        $this->assertTrue($isPasswordHashMatch);

        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_normal_user_cannot_create_user()
    {
        $normalUser = User::factory()->create();
        $team = Team::factory()->create();
        $role = Role::factory()->create();
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'nickname' => $this->faker->userName,
            'staff_number' => $this->faker->numerify('staff#####'),
            'password' => 'P@ssW0rd!',
            'password_confirmation' => 'P@ssW0rd!',
            'roles' => [
                $role->id,
            ],
            'teams' => [
                $team->id
            ],
        ];

        $response = $this->actingAs($normalUser)->postJson('/user-api/v1/users', $data);
        $response->assertForbidden();
    }

    public function test_create_user_duplicate_email()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);
        $team = Team::factory()->create();
        $role = Role::factory()->create();
        $user = User::factory()->create();
        $data = [
            'name' => $this->faker->name,
            'nickname' => $this->faker->userName,
            'staff_number' => $this->faker->numerify('staff#####'),
            'email' => $user->email,
            'password' => 'P@ssW0rd!',
            'password_confirmation' => 'P@ssW0rd!',
            'roles' => [
                $role->id,
            ],
            'team_id' => $team->id,
        ];

        $response = $this->actingAs($admin)->postJson('/user-api/v1/users', $data);
        $response->assertStatus(422);
    }

    public function test_create_user_empty_roles()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);
        $team = Team::factory()->create();
        $data = [
            'name' => $this->faker->name,
            'nickname' => $this->faker->userName,
            'staff_number' => $this->faker->numerify('staff#####'),
            'email' => $this->faker->safeEmail,
            'password' => 'P@ssW0rd!',
            'password_confirmation' => 'P@ssW0rd!',
            'roles' => [],
            'teams' => [
                $team->id
            ],
        ];

        $response = $this->actingAs($admin)->postJson('/user-api/v1/users', $data);
        $response->assertStatus(422);
    }

    public function test_create_user_invalid_roles()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);
        $team = Team::factory()->create();
        $data = [
            'name' => $this->faker->name,
            'nickname' => $this->faker->userName,
            'staff_number' => $this->faker->numerify('staff#####'),
            'email' => $this->faker->safeEmail,
            'password' => 'P@ssW0rd!',
            'password_confirmation' => 'P@ssW0rd!',
            'roles' => [101],
            'team_id' => $team->id,
        ];

        $response = $this->actingAs($admin)->postJson('/user-api/v1/users', $data);
        $response->assertStatus(422);
    }

    public function test_create_user_empty_teams()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);
        $role = Role::factory()->create();
        $data = [
            'name' => $this->faker->name,
            'nickname' => $this->faker->userName,
            'staff_number' => $this->faker->numerify('staff#####'),
            'email' => $this->faker->safeEmail,
            'password' => 'P@ssW0rd!',
            'password_confirmation' => 'P@ssW0rd!',
            'roles' => [
                $role->id,
            ],
            'teams' => [],
        ];

        $response = $this->actingAs($admin)->postJson('/user-api/v1/users', $data);
        $response->assertStatus(422);
    }

    public function test_create_user_unconfirm_password()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);
        $team = Team::factory()->create();
        $role = Role::factory()->create();
        $data = [
            'name' => $this->faker->name,
            'nickname' => $this->faker->userName,
            'staff_number' => $this->faker->numerify('staff#####'),
            'email' => $this->faker->safeEmail,
            'password' => 'unconfirm',
            'password_confirmation' => 'P@ssW0rd!',
            'roles' => [
                $role->id,
            ],
            'team_id' => $team->id,
        ];

        $response = $this->actingAs($admin)->postJson('/user-api/v1/users', $data);
        $response->assertStatus(422);
    }

    public function test_create_user_invalid_team()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);
        $role = Role::factory()->create();
        $data = [
            'name' => $this->faker->name,
            'nickname' => $this->faker->userName,
            'staff_number' => $this->faker->numerify('staff#####'),
            'email' => $this->faker->safeEmail,
            'password' => 'P@ssW0rd!',
            'password_confirmation' => 'P@ssW0rd!',
            'roles' => [
                $role->id,
            ],
            'teams' => [101],
        ];

        $response = $this->actingAs($admin)->postJson('/user-api/v1/users', $data);
        $response->assertStatus(422);
    }

    public function test_create_user_with_multiple_role()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create([
            'access_role_id' => 1 //admin
        ]);
        $admin->roles()->attach($adminRole->id);
        $team = Team::factory()->create();
        $roles = Role::factory()
            ->count(5)
            ->sequence(fn ($sequence) => ['code' => "role000$sequence->index"])
            ->create()
            ->map(fn ($role) => $role->id)
            ->all();
        $data = [
            'name' => $this->faker->name,
            'nickname' => $this->faker->userName,
            'staff_number' => $this->faker->numerify('staff#####'),
            'email' => $this->faker->safeEmail,
            'password' => 'P@ssW0rd!',
            'password_confirmation' => 'P@ssW0rd!',
            'roles' => $roles,
            'teams' => [
                $team->id
            ],
        ];

        $response = $this->actingAs($admin)->postJson('/user-api/v1/users', $data);
        $response->assertCreated();
        $user = User::find($response->json()['data']['id']);
        $this->assertCount(count($roles), $user->roles);
    }

    public function test_create_user_with_multiple_team()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create([
            'access_role_id' => 1 //admin
        ]);
        $admin->roles()->attach($adminRole->id);
        $role = Role::factory()->create();
        $teams = Team::factory()
            ->count(5)
            ->sequence(fn ($sequence) => ['code' => "team000$sequence->index"])
            ->create()
            ->map(fn ($team) => $team->id)
            ->all();
        $data = [
            'name' => $this->faker->name,
            'nickname' => $this->faker->userName,
            'staff_number' => $this->faker->numerify('staff#####'),
            'email' => $this->faker->safeEmail,
            'password' => 'P@ssW0rd!',
            'password_confirmation' => 'P@ssW0rd!',
            'roles' => [
                $role->id,
            ],
            'teams' => $teams,
        ];

        $response = $this->actingAs($admin)->postJson('/user-api/v1/users', $data);
        $response->assertCreated();
        $user = User::find($response->json()['data']['id']);
        $this->assertCount(count($teams), $user->teams);
    }

    public function test_create_user_with_employment_status()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create([
            'access_role_id' => 1 //admin
        ]);
        $admin->roles()->attach($adminRole->id);
        $team = Team::factory()->create();
        $role = Role::factory()->create();
        $data = [
            'name' => $this->faker->name,
            'nickname' => $this->faker->userName,
            'staff_number' => $this->faker->numerify('staff#####'),
            'email' => $this->faker->safeEmail,
            'password' => 'P@ssW0rd!',
            'password_confirmation' => 'P@ssW0rd!',
            'roles' => [
                $role->id,
            ],
            'teams' => [
                $team->id,
            ],
            'employment_status' => $this->faker->randomElement(['PT', 'FT']),
        ];

        $response = $this->actingAs($admin)->postJson('/user-api/v1/users', $data);
        $response->assertCreated();
        $response->assertJsonPath('data.employment_status', $data['employment_status']);
    }

    public function test_create_user_with_invalid_employment_status()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);
        $team = Team::factory()->create();
        $role = Role::factory()->create();
        $data = [
            'name' => $this->faker->name,
            'nickname' => $this->faker->userName,
            'staff_number' => $this->faker->numerify('staff#####'),
            'email' => $this->faker->safeEmail,
            'password' => 'P@ssW0rd!',
            'password_confirmation' => 'P@ssW0rd!',
            'roles' => [
                $role->id,
            ],
            'teams' => [
                $team->id,
            ],
            'employment_status' => 'none',
        ];

        $response = $this->actingAs($admin)->postJson('/user-api/v1/users', $data);
        $response->assertStatus(422);
    }

    public function test_get_user_detail()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/user-api/v1/users/{$user->id}");
        $response->assertOk();
        $response->assertJsonPath('data.id', $user->id);
        $response->assertJsonPath('data.name', $user->name);
        $response->assertJsonPath('data.email', $user->email);
    }

    public function test_get_nonexistence_user_detail()
    {
        $response = $this->getJson('/user-api/v1/users/101');
        $response->assertNotFound();
    }

    public function test_update_user_success()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create([
            'access_role_id' => 1 //admin
        ]);
        $admin->roles()->attach($adminRole->id);
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $nurseRole = Role::factory()->create([
            'name' => 'Nurse',
        ]);
        $user->roles()->attach($nurseRole->id);
        $this->assertCount(1, $user->roles);
        $partTimeRole = Role::factory()->create([
            'name' => 'Part-time',
        ]);
        $data = [
            'name' => 'new name',
            'nickname' => 'newnickname',
            'staff_number' => 'new00007',
            'email' => 'new.address@mail.com',
            'teams' => [
                $team->id
            ],
            'roles' => [
                $nurseRole->id,
                $partTimeRole->id,
            ],
        ];

        $response = $this->actingAs($admin)->putJson("/user-api/v1/users/$user->id", $data);
        $response->assertOk();
        $response->assertJsonPath('data.name', $data['name']);
        $response->assertJsonPath('data.email', $data['email']);
        $response->assertJsonPath('data.nickname', $data['nickname']);
        $response->assertJsonPath('data.staff_number', $data['staff_number']);
        $response->assertJsonPath('data.teams.0.id', $data['teams'][0]);

        $user->refresh();
        $this->assertCount(2, $user->roles);
    }

    public function test_manager_can_update_user()
    {
        $managerRole = Role::factory()->create([
            'name' => 'Manager',
            'code' => 'manager',
        ]);
        $manager = User::factory()->create([
            'access_role_id' => 2 //manager
        ]);
        $manager->roles()->attach($managerRole->id);
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $nurseRole = Role::factory()->create([
            'name' => 'Nurse',
        ]);
        $user->roles()->attach($nurseRole->id);
        $this->assertCount(1, $user->roles);
        $partTimeRole = Role::factory()->create([
            'name' => 'Part-time',
        ]);
        $data = [
            'name' => 'new name',
            'nickname' => 'newnickname',
            'staff_number' => 'new00007',
            'email' => 'new.address@mail.com',
            'teams' => [
                $team->id
            ],
            'roles' => [
                $nurseRole->id,
                $partTimeRole->id,
            ],
        ];

        $response = $this->actingAs($manager)->putJson("/user-api/v1/users/$user->id", $data);
        $response->assertOk();
        $response->assertJsonPath('data.name', $data['name']);
        $response->assertJsonPath('data.nickname', $data['nickname']);
        $response->assertJsonPath('data.staff_number', $data['staff_number']);
        $response->assertJsonPath('data.email', $data['email']);
        $response->assertJsonPath('data.teams.0.id', $data['teams'][0]);

        $user->refresh();
        $this->assertCount(2, $user->roles);
    }

    public function test_normal_user_cannot_update_user()
    {
        $manager = User::factory()->create();
        Team::factory()->create();
        $user = User::factory()->create();
        $data = [
            'name' => 'new name',
        ];

        $response = $this->actingAs($manager)->putJson("/user-api/v1/users/$user->id", $data);
        $response->assertForbidden();
    }

    public function test_update_user_employment_status()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create([
            'access_role_id' => 1 //admin
        ]);
        $admin->roles()->attach($adminRole->id);
        $team = Team::factory()->create();
        $user = User::factory()->create([
            'employment_status' => 'FT',
        ]);
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
        ]);
        $user->roles()->attach($adminRole->id);
        $this->assertCount(1, $user->roles);
        $data = [
            'name' => 'new name',
            'email' => 'new.address@mail.com',
            'teams' => [
                $team->id
            ],
            'employment_status' => 'PT',
        ];

        $response = $this->actingAs($admin)->putJson("/user-api/v1/users/$user->id", $data);
        $response->assertOk();
        $response->assertJsonPath('data.name', $data['name']);
        $response->assertJsonPath('data.email', $data['email']);
        $response->assertJsonPath('data.teams.0.id', $data['teams'][0]);
        $response->assertJsonPath('data.employment_status', $data['employment_status']);
    }

    public function test_update_user_with_invalid_employment_status()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);
        $team = Team::factory()->create();
        $user = User::factory()->create([
            'employment_status' => 'FT',
        ]);
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
        ]);
        $user->roles()->attach($adminRole->id);
        $this->assertCount(1, $user->roles);
        $data = [
            'name' => 'new name',
            'email' => 'new.address@mail.com',
            'teams' => [
                $team->id,
            ],
            'employment_status' => 'none',
        ];

        $response = $this->actingAs($admin)->putJson("/user-api/v1/users/$user->id", $data);
        $response->status(422);
    }

    public function test_update_nonexistence_user()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);
        $data = [
            'name' => 'new name',
        ];

        $response = $this->actingAs($admin)->putJson('/user-api/v1/users/101', $data);
        $response->assertNotFound();
    }

    public function test_update_user_with_empty_roles()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);
        $user = User::factory()->create();
        $data = [
            'roles' => [],
        ];

        $response = $this->actingAs($admin)->putJson("/user-api/v1/users/$user->id", $data);
        $response->assertStatus(422);
    }

    public function test_update_user_with_empty_teams()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);
        $user = User::factory()->create();
        $data = [
            'teams' => [],
        ];

        $response = $this->actingAs($admin)->putJson("/user-api/v1/users/$user->id", $data);
        $response->assertStatus(422);
    }

    public function test_update_user_add_role()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create([
            'access_role_id' => 1 //admin
        ]);
        $admin->roles()->attach($adminRole->id);
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $roles = Role::factory()
            ->count(5)
            ->sequence(fn ($sequence) => ['code' => "role000$sequence->index"])
            ->create()
            ->map(fn ($role) => $role->id)
            ->all();
        $user->roles()->attach($roles[0]);
        $this->assertCount(1, $user->roles);
        $data = [
            'roles' => $roles,
        ];

        $response = $this->actingAs($admin)->putJson("/user-api/v1/users/$user->id", $data);
        $response->assertOk();

        $user->refresh();
        $this->assertCount(count($roles), $user->roles);
    }

    public function test_update_user_remove_role()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create([
            'access_role_id' => 1 //admin
        ]);
        $admin->roles()->attach($adminRole->id);
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $roles = Role::factory()
            ->count(5)
            ->sequence(fn ($sequence) => ['code' => "role000$sequence->index"])
            ->create()
            ->map(fn ($role) => $role->id)
            ->all();
        $user->roles()->attach($roles);
        $this->assertCount(count($roles), $user->roles);
        $data = [
            'roles' => collect($roles)->take(2)->all(),
        ];

        $response = $this->actingAs($admin)->putJson("/user-api/v1/users/$user->id", $data);
        $response->assertOk();

        $user->refresh();
        $this->assertCount(2, $user->roles);
    }

    public function test_update_user_add_team()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create([
            'access_role_id' => 1 //admin
        ]);
        $admin->roles()->attach($adminRole->id);
        $user = User::factory()->create();
        $teams = Team::factory()
            ->count(5)
            ->sequence(fn ($sequence) => ['code' => "team000$sequence->index"])
            ->create()
            ->map(fn ($role) => $role->id)
            ->all();
        $user->teams()->attach($teams[0]);
        $this->assertCount(1, $user->teams);
        $data = [
            'teams' => $teams,
        ];

        $response = $this->actingAs($admin)->putJson("/user-api/v1/users/$user->id", $data);
        $response->assertOk();

        $user->refresh();
        $this->assertCount(count($teams), $user->teams);
    }

    public function test_update_user_remove_team()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create([
            'access_role_id' => 1 //admin
        ]);
        $admin->roles()->attach($adminRole->id);
        $user = User::factory()->create();
        $teams = Team::factory()
            ->count(5)
            ->sequence(fn ($sequence) => ['code' => "team000$sequence->index"])
            ->create()
            ->map(fn ($role) => $role->id)
            ->all();
        $user->teams()->attach($teams);
        $this->assertCount(count($teams), $user->teams);
        $data = [
            'teams' => collect($teams)->take(2)->all(),
        ];

        $response = $this->actingAs($admin)->putJson("/user-api/v1/users/$user->id", $data);
        $response->assertOk();

        $user->refresh();
        $this->assertCount(2, $user->teams);
    }

    public function test_delete_user_success()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create([
            'access_role_id' => 1 //admin
        ]);
        $admin->roles()->attach($adminRole->id);
        $user = User::factory()->create();
        $userId = $user->id;
        $response = $this->actingAs($admin)->deleteJson("/user-api/v1/users/$userId");
        $response->assertNoContent();
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    public function test_manager_cannot_delete_user()
    {
        $managerRole = Role::factory()->create([
            'name' => 'Manager',
            'code' => 'manager',
        ]);
        $manager = User::factory()->create();
        $manager->roles()->attach($managerRole->id);
        $user = User::factory()->create();
        $userId = $user->id;
        $response = $this->actingAs($manager)->deleteJson("/user-api/v1/users/$userId");
        $response->assertForbidden();
    }

    public function test_normal_user_cannot_delete_user()
    {
        $normalUser = User::factory()->create();
        $user = User::factory()->create();
        $userId = $user->id;
        $response = $this->actingAs($normalUser)->deleteJson("/user-api/v1/users/$userId");
        $response->assertForbidden();
    }

    public function test_delete_nonexistence_user()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);

        $response = $this->actingAs($admin)->deleteJson('/user-api/v1/users/101');
        $response->assertNotFound();
    }

    public function test_get_user_autocomplete()
    {
        $users = User::factory()->count(10)->create();
        $response = $this->get('/user-api/v1/users/autocomplete');

        $response->assertOk();
        $response->assertJsonCount($users->count(), 'data');
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'name',
                    'email',
                ],
            ],
            'links',
            'meta',
        ]);
    }

    public function test_filter_user_autocomplete_by_name()
    {
        $users = User::factory()->count(10)->create();
        $user = $users->random();
        $nameFilter = $user->nickname;
        $response = $this->get("/user-api/v1/users/autocomplete?name=$nameFilter");

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $user->id);
        $response->assertJsonPath('data.0.name', $user->nickname);
        $response->assertJsonPath('data.0.email', $user->email);
    }

    public function test_filter_user_autocomplete_by_email()
    {
        $users = User::factory()->count(10)->create();
        $user = $users->random();
        $emailFilter = $user->email;
        $response = $this->get("/user-api/v1/users/autocomplete?email=$emailFilter");

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $user->id);
        $response->assertJsonPath('data.0.name', $user->nickname);
        $response->assertJsonPath('data.0.email', $user->email);
    }

    public function test_filter_user_autocomplete_by_ids()
    {
        $userSize = $this->faker->numberBetween(1, 10);
        $users = User::factory()->count(10)->create();
        $userIds = $users->random($userSize)
            ->map(fn ($user) => $user->id)
            ->join(',');
        $response = $this->get("/user-api/v1/users/autocomplete?ids=$userIds");

        $response->assertOk();
        $response->assertJsonCount($userSize, 'data');
    }

    public function test_filter_user_list_by_ids()
    {
        $userSize = $this->faker->numberBetween(1, 10);
        $users = User::factory()->count(10)->create();
        $userIds = $users->random($userSize)
            ->map(fn ($user) => $user->id)
            ->join(',');
        $response = $this->get("/user-api/v1/users?ids=$userIds");

        $response->assertOk();
        $response->assertJsonCount($userSize, 'data');
    }

    public function test_filter_user_list_by_team_id()
    {
        $teams = Team::factory()->count(2)->create();
        $firstTeam = $teams->first()->id;
        $secondTeam = $teams->last()->id;
        $firstTeamSize = $this->faker->numberBetween(1, 10);
        $secondTeamSize = $this->faker->numberBetween(1, 10);
        User::factory()
            ->count($firstTeamSize)
            ->create()
            ->each(function ($user) use ($firstTeam) {
                $user->teams()->attach($firstTeam);
            });
        User::factory()
            ->count($secondTeamSize)
            ->create()
            ->each(function ($user) use ($secondTeam) {
                $user->teams()->attach($secondTeam);
            });
        $teamId = $teams->random()->id;
        $response = $this->get("/user-api/v1/users?team_id=$teamId");

        $response->assertOk();
        $teamSize = $teamId === $firstTeam ? $firstTeamSize : $secondTeamSize;
        $response->assertJsonCount($teamSize, 'data');
    }

    public function test_filter_user_autocomplete_by_team_id()
    {
        $teams = Team::factory()->count(2)->create();
        $firstTeam = $teams->first()->id;
        $secondTeam = $teams->last()->id;
        $firstTeamSize = $this->faker->numberBetween(1, 10);
        $secondTeamSize = $this->faker->numberBetween(1, 10);
        User::factory()
            ->count($firstTeamSize)
            ->create()
            ->each(function ($user) use ($firstTeam) {
                $user->teams()->attach($firstTeam);
            });
        User::factory()
            ->count($secondTeamSize)
            ->create()
            ->each(function ($user) use ($secondTeam) {
                $user->teams()->attach($secondTeam);
            });
        $teamId = $teams->random()->id;
        $response = $this->get("/user-api/v1/users/autocomplete?team_id=$teamId");

        $response->assertOk();
        $teamSize = $teamId === $firstTeam ? $firstTeamSize : $secondTeamSize;
        $response->assertJsonCount($teamSize, 'data');
    }
}
