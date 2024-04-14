<?php

namespace Tests\Feature;

use App\Models\Cases;
use App\Models\District;
use App\Models\Elder;
use App\Models\ElderCalls;
use App\Models\Referral;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class CallTest extends TestCase
{
    use WithFaker, WithoutMiddleware, RefreshDatabase;

    public function test_get_calls_list()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        Elder::factory()->create();
        Cases::factory()->create();
        $callsCount = $this->faker->numberBetween(5, 10);
        ElderCalls::factory()->count($callsCount)->create();

        $response = $this->getJson('/elderly-api/v1/calls');
        $response->assertOk();
        $response->assertJsonCount($callsCount, 'data');
    }

    public function test_get_call_detail_success()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        Elder::factory()->create();
        Cases::factory()->create();
        $callsCount = $this->faker->numberBetween(5, 10);
        $calls = ElderCalls::factory()->count($callsCount)->create();
        $randomCall = $calls->random();

        $response = $this->getJson("/elderly-api/v1/calls/$randomCall->id");
        $response->assertOk();
        $response->assertJsonPath('data.id', $randomCall->id);
        $response->assertJsonPath('data.call_date', $randomCall->call_date->format('Y-m-d'));
        $response->assertJsonPath('data.caller_id', $randomCall->caller_id);
        $response->assertJsonPath('data.cases_id', $randomCall->cases_id);
        $response->assertJsonPath('data.call_status', $randomCall->call_status);
    }

    public function test_get_call_detail_failed()
    {
        $response = $this->getJson('/elderly-api/v1/calls/101');
        $response->assertNotFound();
    }

    public function test_create_calls_success()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        $elder = Elder::factory()->create();
        $elderCase = Cases::factory()->create();
        $data = [
            'caller_id' => $this->faker->numberBetween(1, 100),
            'cases_id' => $elderCase->id,
            'call_date' => Carbon::now()->format('Y-m-d'),
            'call_status' => 'status',
        ];

        $response = $this->postJson('/elderly-api/v1/calls', $data);
        $response->assertCreated();

        $response->assertJsonPath('data.call_date', $data['call_date']);
        $response->assertJsonPath('data.caller_id', $data['caller_id']);
        $response->assertJsonPath('data.cases_id', $data['cases_id']);
        $response->assertJsonPath('data.call_status', $data['call_status']);
        $response->assertJsonPath('data.elder_uid', $elder->uid);
        $response->assertJsonPath('data.elder_name', $elder->name);
    }

    public function test_create_calls_with_empty_data_failed()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        Elder::factory()->create();
        Cases::factory()->create();
        $data = [];

        $response = $this->postJson('/elderly-api/v1/calls', $data);
        $response->assertStatus(422);
    }

    public function test_update_call_success()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        Elder::factory()->create();
        $elderCase = Cases::factory()->create();
        $callsCount = $this->faker->numberBetween(5, 10);
        $calls = ElderCalls::factory()->count($callsCount)->create();
        $randomCall = $calls->random();
        $data = [
            'caller_id' => $this->faker->numberBetween(1, 100),
            'cases_id' => $elderCase->id,
            'call_date' => Carbon::now()->format('Y-m-d'),
            'call_status' => 'updated status',
        ];

        $response = $this->putJson("/elderly-api/v1/calls/$randomCall->id", $data);
        $response->assertOk();

        $randomCall->refresh();
        $response->assertJsonPath('data.id', $randomCall->id);
        $response->assertJsonPath('data.call_date', $randomCall->call_date);
        $response->assertJsonPath('data.caller_id', $randomCall->caller_id);
        $response->assertJsonPath('data.cases_id', $randomCall->cases_id);
        $response->assertJsonPath('data.call_status', $randomCall->call_status);
    }

    public function test_update_calls_with_empty_data_failed()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        Elder::factory()->create();
        Cases::factory()->create();
        $callsCount = $this->faker->numberBetween(5, 10);
        $calls = ElderCalls::factory()->count($callsCount)->create();
        $randomCall = $calls->random();
        $data = [];

        $response = $this->putJson("/elderly-api/v1/calls/$randomCall->id", $data);
        $response->assertStatus(422);
    }

    public function test_delete_call_success()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        Elder::factory()->create();
        Cases::factory()->create();
        $callsCount = $this->faker->numberBetween(5, 10);
        $calls = ElderCalls::factory()->count($callsCount)->create();
        $callId = $calls->random()->id;

        $response = $this->deleteJson("/elderly-api/v1/calls/$callId");
        $response->assertNoContent();

        $this->assertDatabaseMissing('elder_calls', ['id' => $callId]);
    }

    public function test_delete_nonexistence_call_failed()
    {
        $response = $this->deleteJson('/elderly-api/v1/calls/101');
        $response->assertNotFound();
    }
}
