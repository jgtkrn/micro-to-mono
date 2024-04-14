<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

    public function test_get_group_list_success()
    {
        $size = $this->faker->numberBetween(5, 10);
        Group::factory()
            ->count($size)
            ->sequence(fn ($sequence) => ['code' => "group-0000$sequence->index"])
            ->create();
        $response = $this->getJson('/user-api/v1/groups');

        $response->assertOk();
        $response->assertJsonCount($size, 'data');
    }

    public function test_filter_group_list_by_name()
    {
        $size = $this->faker->numberBetween(5, 10);
        $groups = Group::factory()
            ->count($size)
            ->sequence(fn ($sequence) => ['code' => "group-0000$sequence->index"])
            ->create();
        $nameFilter = $groups->random()->name;
        $response = $this->getJson("/user-api/v1/groups?name=$nameFilter");

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.name', $nameFilter);
    }

    public function test_filter_group_list_by_code()
    {
        $size = $this->faker->numberBetween(5, 10);
        $groups = Group::factory()
            ->count($size)
            ->sequence(fn ($sequence) => ['code' => "group-0000$sequence->index"])
            ->create();
        $codeFilter = $groups->random()->code;
        $response = $this->getJson("/user-api/v1/groups?code=$codeFilter");

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.code', $codeFilter);
    }

    public function test_create_group()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create([
            'access_role_id' => 1 //admin
        ]);
        $admin->roles()->attach($adminRole->id);
        $data = [
            'name' => 'new group',
            'code' => 'code-0001',
        ];
        $response = $this->actingAs($admin)->postJson('/user-api/v1/groups', $data);

        $response->assertCreated();
        $response->assertJsonPath('data.name', $data['name']);
        $response->assertJsonPath('data.code', $data['code']);
        $response->assertJsonCount(0, 'data.users');
    }

    public function test_manager_cannot_create_group()
    {
        $managerRole = Role::factory()->create([
            'name' => 'Manager',
            'code' => 'manager',
        ]);
        $manager = User::factory()->create([
            'access_role_id' => 2 //manager
        ]);
        $manager->roles()->attach($managerRole->id);
        $data = [
            'name' => 'new group',
            'code' => 'code-0001',
        ];
        $response = $this->actingAs($manager)->postJson('/user-api/v1/groups', $data);

        $response->assertForbidden();
    }

    public function test_normal_cannot_create_group()
    {
        $normalUser = User::factory()->create();
        $data = [
            'name' => 'new group',
            'code' => 'code-0001',
        ];
        $response = $this->actingAs($normalUser)->postJson('/user-api/v1/groups', $data);

        $response->assertForbidden();
    }

    public function test_create_group_with_empty_data()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);
        $response = $this->actingAs($admin)->postJson('/user-api/v1/groups', []);

        $response->assertStatus(422);
    }

    public function test_create_group_with_users()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create([
            'access_role_id' => 1 //admin
        ]);
        $admin->roles()->attach($adminRole->id);
        $size = $this->faker->numberBetween(2, 10);
        $users = User::factory()->count($size)->create();
        $user = $users->first();
        $data = [
            'name' => 'new group',
            'code' => 'code-0001',
            'users' => $users->map(fn ($usr) => $usr->id)->all(),
        ];
        $response = $this->actingAs($admin)->postJson('/user-api/v1/groups', $data);

        $response->assertCreated();
        $response->assertJsonPath('data.name', $data['name']);
        $response->assertJsonPath('data.code', $data['code']);
        $response->assertJsonCount($size, 'data.users');
        $response->assertJsonPath('data.users.0.id', $user->id);
        $response->assertJsonPath('data.users.0.name', $user->name);
        $response->assertJsonPath('data.users.0.email', $user->email);
    }

    public function test_create_group_with_non_existing_users()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);
        $data = [
            'name' => 'new group',
            'code' => 'code-0001',
            'users' => [
                101,
                102,
            ],
        ];
        $response = $this->actingAs($admin)->postJson('/user-api/v1/groups', $data);

        $response->assertStatus(422);
    }

    public function test_create_group_with_single_users()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);
        $user = User::factory()->create();
        $data = [
            'name' => 'new group',
            'code' => 'code-0001',
            'users' => [
                $user->id,
            ],
        ];
        $response = $this->actingAs($admin)->postJson('/user-api/v1/groups', $data);

        $response->assertStatus(422);
    }

    public function test_get_group_detail_with_no_user()
    {
        $group = Group::factory()->create();
        $response = $this->getJson("/user-api/v1/groups/$group->id");

        $response->assertJsonPath('data.id', $group->id);
        $response->assertJsonPath('data.name', $group->name);
        $response->assertJsonPath('data.code', $group->code);
        $response->assertJsonCount(0, 'data.users');
    }

    public function test_get_group_detail_with_users()
    {
        $group = Group::factory()->create();
        $users = User::factory()->count(5)->create();
        $userIds = $users->map(fn ($user) => $user->id)->all();
        $group->users()->sync($userIds);
        $this->assertCount($users->count(), $group->users);

        $response = $this->getJson("/user-api/v1/groups/$group->id");

        $response->assertJsonPath('data.id', $group->id);
        $response->assertJsonPath('data.name', $group->name);
        $response->assertJsonPath('data.code', $group->code);
        $response->assertJsonCount($users->count(), 'data.users');
    }

    public function test_get_non_existing_group_detail()
    {
        $response = $this->getJson('/user-api/v1/groups/101');

        $response->assertNotFound();
    }

    public function test_update_non_existing_group()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);
        $response = $this->actingAs($admin)->putJson('/user-api/v1/groups/101');

        $response->assertNotFound();
    }

    public function test_update_group_with_invalid_data()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);
        $group = Group::factory()->create();
        $data = [
            'name' => '',
            'users' => [],
        ];
        $response = $this->actingAs($admin)->putJson("/user-api/v1/groups/$group->id", $data);

        $response->assertStatus(422);
    }

    public function test_update_group_with_invalid_user()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);
        $group = Group::factory()->create();
        $data = [
            'name' => 'Updated name',
            'users' => [101, 102, 103],
        ];
        $response = $this->actingAs($admin)->putJson("/user-api/v1/groups/$group->id", $data);

        $response->assertStatus(422);
    }

    public function test_update_group_name()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create([
            'access_role_id' => 1 //admin
        ]);
        $admin->roles()->attach($adminRole->id);
        $group = Group::factory()->create();
        $data = [
            'name' => 'Updated name',
        ];
        $response = $this->actingAs($admin)->putJson("/user-api/v1/groups/$group->id", $data);

        $response->assertOk();
        $response->assertJsonPath('data.name', $data['name']);
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'code' => $group->code,
            'name' => $data['name'],
        ]);
    }

    public function test_manager_can_update_group_name()
    {
        $managerRole = Role::factory()->create([
            'name' => 'Manager',
            'code' => 'manager',
        ]);
        $manager = User::factory()->create([
            'access_role_id' => 2 //manager
        ]);
        $manager->roles()->attach($managerRole->id);
        $group = Group::factory()->create();
        $data = [
            'name' => 'Updated name',
        ];
        $response = $this->actingAs($manager)->putJson("/user-api/v1/groups/$group->id", $data);

        $response->assertOk();
        $response->assertJsonPath('data.name', $data['name']);
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'code' => $group->code,
            'name' => $data['name'],
        ]);
    }

    public function test_normal_user_cannot_update_group_name()
    {
        $normalUser = User::factory()->create();
        $group = Group::factory()->create();
        $data = [
            'name' => 'Updated name',
        ];
        $response = $this->actingAs($normalUser)->putJson("/user-api/v1/groups/$group->id", $data);

        $response->assertForbidden();
    }

    public function test_add_user_to_empty_group()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create([
            'access_role_id' => 1 //admin
        ]);
        $admin->roles()->attach($adminRole->id);
        $group = Group::factory()->create();
        $userSize = $this->faker->numberBetween(5, 10);
        $users = User::factory()->count($userSize)->create();
        $data = [
            'users' => $users->map(fn ($user) => $user->id)->all(),
        ];
        $response = $this->actingAs($admin)->putJson("/user-api/v1/groups/$group->id", $data);

        $response->assertOk();
        $response->assertJsonCount($userSize, 'data.users');

        $group->refresh();
        $this->assertCount($userSize, $group->users);
    }

    public function test_manager_can_add_user_to_empty_group()
    {
        $managerRole = Role::factory()->create([
            'name' => 'Manager',
            'code' => 'manager',
        ]);
        $manager = User::factory()->create([
            'access_role_id' => 2 //manager
        ]);
        $manager->roles()->attach($managerRole->id);
        $group = Group::factory()->create();
        $userSize = $this->faker->numberBetween(5, 10);
        $users = User::factory()->count($userSize)->create();
        $data = [
            'users' => $users->map(fn ($user) => $user->id)->all(),
        ];
        $response = $this->actingAs($manager)->putJson("/user-api/v1/groups/$group->id", $data);

        $response->assertOk();
        $response->assertJsonCount($userSize, 'data.users');

        $group->refresh();
        $this->assertCount($userSize, $group->users);
    }

    public function test_add_user_to_filled_group()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create([
            'access_role_id' => 1 //admin
        ]);
        $admin->roles()->attach($adminRole->id);
        $group = Group::factory()->create();
        $userSize = $this->faker->numberBetween(5, 10);
        $users = User::factory()->count($userSize)->create();

        $first = $users->first();
        $group->users()->attach($first->id);
        $this->assertCount(1, $group->users);

        $data = [
            'users' => $users->map(fn ($user) => $user->id)->all(),
        ];
        $response = $this->actingAs($admin)->putJson("/user-api/v1/groups/$group->id", $data);

        $response->assertOk();
        $response->assertJsonCount($userSize, 'data.users');

        $group->refresh();
        $this->assertCount($userSize, $group->users);
    }

    public function test_delete_non_existing_group()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);
        $response = $this->actingAs($admin)->deleteJson('/user-api/v1/groups/101');

        $response->assertNotFound();
    }

    public function test_delete_group_success()
    {
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);
        $admin = User::factory()->create([
            'access_role_id' => 1 //admin
        ]);
        $admin->roles()->attach($adminRole->id);
        $group = Group::factory()->create();
        $groupData = $group->toArray();
        $response = $this->actingAs($admin)->deleteJson("/user-api/v1/groups/$group->id");

        $response->assertNoContent();
        $this->assertDatabaseMissing('groups', $groupData);
    }

    public function test_manager_cannot_delete_group_success()
    {
        $managerRole = Role::factory()->create([
            'name' => 'Manager',
            'code' => 'manager',
        ]);
        $manager = User::factory()->create();
        $manager->roles()->attach($managerRole->id);
        $group = Group::factory()->create();
        $groupData = $group->toArray();
        $response = $this->actingAs($manager)->deleteJson("/user-api/v1/groups/$group->id");

        $response->assertForbidden();
    }
}
