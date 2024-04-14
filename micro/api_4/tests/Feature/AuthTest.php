<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_user_unauthenticated()
    {
        $response = $this->getJson('/user-api/v1/auth/user');

        $response->assertStatus(401);
    }

    public function test_get_user_authenticated()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token')->plainTextToken;
        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/user-api/v1/auth/user');

        $response->assertStatus(200);
    }

    public function test_user_logged_in_success()
    {
        $user = User::factory()->create();
        $data = [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'unit_test',
        ];

        $response = $this->postJson('/user-api/v1/auth/login', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'token',
                'user',
            ]
        ]);
        $response->assertJsonPath('data.user.name', $user->name);
        $response->assertJsonPath('data.user.email', $user->email);
    }

    public function test_user_logged_in_failed()
    {
        $user = User::factory()->create();
        $data = [
            'email' => $user->email,
            'password' => 'invalid_password',
            'device_name' => 'unit_test',
        ];

        $response = $this->postJson('/user-api/v1/auth/login', $data);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => [
                'code' => 400,
                'message' => __('The provided credentials are incorrect'),
            ]
        ]);
    }

    public function test_user_logged_out()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/user-api/v1/auth/logout');

        $response->assertStatus(204);
        $this->assertCount(0, $user->tokens);
    }

    // TODO: add test for forgot and reset password
}
