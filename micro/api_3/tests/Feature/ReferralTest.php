<?php

namespace Tests\Feature;

use App\Models\Referral;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReferralTest extends TestCase
{
    use WithFaker, WithoutMiddleware, RefreshDatabase;

    public function test_get_referral_list_success()
    {
        $count = $this->faker->numberBetween(5, 10);
        Referral::factory()->count($count)->create();
        $response = $this->getJson('/elderly-api/v1/referrals');

        $response->assertOk();
        $response->assertJsonCount($count, 'data');
    }

    public function test_create_referral_success()
    {
        $label = $this->faker->word;
        $data = [
            'label' => $label,
            'code' => Str::slug($label),
            'bzn_code' => $this->faker->lexify('B###'),
            'cga_code' => $this->faker->lexify('C###'),
        ];
        $response = $this->postJson('/elderly-api/v1/referrals', $data);

        $response->assertCreated();
        $response->assertJsonPath('data.label', $data['label']);
        $response->assertJsonPath('data.code', $data['code']);
        $response->assertJsonPath('data.bzn_code', $data['bzn_code']);
        $response->assertJsonPath('data.cga_code', $data['cga_code']);

        $this->assertDatabaseHas('referrals', $data);
    }

    public function test_create_referral_with_empty_data_failed()
    {
        $data = [];
        $response = $this->postJson('/elderly-api/v1/referrals', $data);

        $response->assertStatus(422);
    }

    public function test_get_referral_detail_success()
    {
        $count = $this->faker->numberBetween(5, 10);
        $referrals = Referral::factory()->count($count)->create();
        $referral = $referrals->random();

        $response = $this->getJson("/elderly-api/v1/referrals/$referral->id");

        $response->assertOk();
        $response->assertJsonPath('data.id', $referral->id);
        $response->assertJsonPath('data.label', $referral->label);
        $response->assertJsonPath('data.code', $referral->code);
        $response->assertJsonPath('data.bzn_code', $referral->bzn_code);
        $response->assertJsonPath('data.cga_code', $referral->cga_code);
    }

    public function test_get_nonexistence_referral_detail_failed()
    {
        $response = $this->getJson('/elderly-api/v1/referrals/101');

        $response->assertNotFound();
    }

    public function test_update_referral_success()
    {
        $count = $this->faker->numberBetween(5, 10);
        $referrals = Referral::factory()->count($count)->create();
        $referral = $referrals->random();
        $data = [
            'label' => 'Updated Label',
            'code' => 'updated_label',
            'bzn_code' => $this->faker->lexify('B###'),
            'cga_code' => $this->faker->lexify('C###'),
        ];

        $response = $this->putJson("/elderly-api/v1/referrals/$referral->id", $data);

        $response->assertOk();
        $response->assertJsonPath('data.id', $referral->id);
        $response->assertJsonPath('data.label', $data['label']);
        $response->assertJsonPath('data.code', $data['code']);
        $response->assertJsonPath('data.bzn_code', $data['bzn_code']);
        $response->assertJsonPath('data.cga_code', $data['cga_code']);

        $this->assertDatabaseHas('referrals', array_merge(['id' => $referral->id], $data));
    }

    public function test_update_referral_with_empty_data_failed()
    {
        $count = $this->faker->numberBetween(5, 10);
        $referrals = Referral::factory()->count($count)->create();
        $referral = $referrals->random();
        $data = [
            'label' => '',
            'code' => 'Invalid Code',
            'bzn_code' => '',
            'cga_code' => '',
        ];

        $response = $this->putJson("/elderly-api/v1/referrals/$referral->id", $data);

        $response->assertStatus(422);
    }

    public function test_update_nonexistence_referral_detail_failed()
    {
        $data = [
            'label' => 'Updated Label',
            'code' => 'updated_label',
            'bzn_code' => $this->faker->lexify('B###'),
            'cga_code' => $this->faker->lexify('C###'),
        ];
        $response = $this->putJson('/elderly-api/v1/referrals/101', $data);

        $response->assertNotFound();
    }

    public function test_delete_referral_success()
    {
        $count = $this->faker->numberBetween(5, 10);
        $referrals = Referral::factory()->count($count)->create();
        $referralId = $referrals->random()->id;

        $response = $this->deleteJson("/elderly-api/v1/referrals/$referralId");

        $response->assertNoContent();

        $this->assertDatabaseMissing('referrals', ['id' => $referralId]);
    }

    public function test_delete_nonexistence_referral_detail_failed()
    {
        $response = $this->deleteJson('/elderly-api/v1/referrals/101');

        $response->assertNotFound();
    }
}
