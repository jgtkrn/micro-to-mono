<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use App\Models\Community;

class CommunityTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

    public function test_get_communities_success()
    {
        $community = Community::factory()->create();

        $response = $this->getJson('assessments-api/v1/communities');

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data', 1) //has 1 data
                ->has('meta') //has meta
                ->has('links') //has links
                ->where('data.0.id', $community->id)
                ->where('data.0.name', $community->name)
                ->where('data.0.url', $community->url)
                ->where('meta.current_page', 1)
                ->where('meta.last_page', 1)
                ->where('meta.per_page', 10)
                ->where('meta.total', 1)
        );
    }

    public function test_get_communities_success_with_filter()
    {
        $community = Community::factory()->create();
        $per_page = rand(1, 50);

        $response = $this->getJson("assessments-api/v1/communities"
            . "?page=1&per_page={$per_page}"
            . "&search={$community->name}");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data', 1) //has 1 data
                ->has('meta') //has meta
                ->has('links') //has links
                ->where('data.0.id', $community->id)
                ->where('data.0.name', $community->name)
                ->where('data.0.url', $community->url)
                ->where('meta.current_page', 1)
                ->where('meta.last_page', 1)
                ->where('meta.per_page', $per_page)
                ->where('meta.total', 1)
        );
    }

    public function test_get_communities_failed_invalid_format()
    {
        $community = Community::factory()->create();

        $response = $this->getJson("assessments-api/v1/communities"
            . "?page=1&per_page=a" //string
            . "&search={$community->name}");

        $response->assertStatus(422);
    }

    public function test_get_community_details_succeed()
    {
        $community = Community::factory()->create();

        $response = $this->getJson("assessments-api/v1/communities/{$community->id}");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data') //has data
                ->where('data.id', $community->id)
                ->where('data.name', $community->name)
                ->where('data.url', $community->url)
        );
    }

    public function test_get_community_details_failed_not_found()
    {
        $response = $this->getJson("assessments-api/v1/communities/100");

        $response->assertNotFound();
    }

    public function test_post_community_success()
    {
        $data = [
            'name' => $this->faker->colorName,
            'url' => $this->faker->url
        ];
        $response = $this->postJson('assessments-api/v1/communities', $data);

        $response->assertCreated();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data')
                ->has('data.id')
                ->where('data.name', $data['name'])
                ->where('data.url', $data['url'])
        );
    }

    public function test_post_community_failed_no_name()
    {
        $data = [
            //no name data
            'url' => $this->faker->url
        ];
        $response = $this->postJson('assessments-api/v1/communities', $data);

        $response->assertStatus(422);
    }

    public function test_put_community_success()
    {
        $community = Community::factory()->create();
        //data for update community
        $data = [
            'name' => $this->faker->colorName,
            'url' => $this->faker->url
        ];

        $response = $this->putJson("assessments-api/v1/communities/{$community->id}", $data);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data')
                ->has('data.id')
                ->where('data.name', $data['name'])
                ->where('data.url', $data['url'])
        );
    }

    public function test_put_community_failed_incomplete_request()
    {
        $community = Community::factory()->create();
        //data for update community
        $data = [
            //'name' => $this->faker->colorName, //incomplete data
            'url' => $this->faker->url
        ];

        $response = $this->putJson("assessments-api/v1/communities/{$community->id}", $data);

        $response->assertStatus(422);
    }

    public function test_delete_community_success()
    {
        $community = Community::factory()->create();
        $this->assertDatabaseHas('communities', [
            'id' => $community->id
        ]);

        $response = $this->deleteJson("assessments-api/v1/communities/{$community->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('communities', [
            'id' => $community->id
        ]);
    }

    public function test_delete_community_failed_not_found()
    {
        $response = $this->deleteJson("assessments-api/v1/communities/100");

        $response->assertNotFound();
    }
}
