<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\UserEvent;
use App\Models\File;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AppoinmentTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

    public function test_get_appointments_success()
    {
        $category = Category::factory()->create();
        $appointment = Event::factory()
            ->has(UserEvent::factory()->count(1), 'user')
            ->for($category)
            ->create();

        $response = $this->getJson("appointments-api/v1/appointments");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data', 1) //has 1 data
                ->has('meta') //has meta
                ->has('links') //has links
                ->has('data.0.elder') //has elder object in appointment object
                ->where('data.0.id', $appointment->id)
                ->where('data.0.title', $appointment->title)
                ->where('data.0.start', Carbon::parse($appointment->start)->format("Y-m-d H:i:s")) //example 2022-05-25 13:00:00
                ->where('data.0.end', Carbon::parse($appointment->end)->format("Y-m-d H:i:s")) //example 2022-05-25 14:00:00
                ->where('data.0.remark', $appointment->remark)
                ->where('data.0.category_id', $appointment->category_id)
                ->where('meta.current_page', 1)
                ->where('meta.last_page', 1)
                ->where('meta.total', 1)
        );
    }

    public function test_get_appointments_success_with_filter()
    {
        $category = Category::factory()->create();
        $appointment = Event::factory()
            ->has(UserEvent::factory()->count(1), 'user')
            ->for($category)
            ->create();

        $per_page = rand(1, 50);
        $user_event = $appointment->user()->first();

        $response = $this->getJson(
            "appointments-api/v1/appointments"
                . "?page=1&per_page={$per_page}&category_id={$appointment->category_id}"
                . "&search={$appointment->title}&user_ids={$user_event->user_id}"
        );

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data', 1) //has 1 data
                ->has('meta') //has meta
                ->has('links') //has links
                ->has('data.0.elder') //has elder object in appointment object
                ->where('data.0.id', $appointment->id)
                ->where('data.0.title', $appointment->title)
                ->where('data.0.start', Carbon::parse($appointment->start)->format("Y-m-d H:i:s")) //example 2022-05-25 13:00:00
                ->where('data.0.end', Carbon::parse($appointment->end)->format("Y-m-d H:i:s")) //example 2022-05-25 14:00:00
                ->where('data.0.remark', $appointment->remark)
                ->where('data.0.category_id', $appointment->category_id)
                ->where('meta.current_page', 1)
                ->where('meta.last_page', 1)
                ->where('meta.per_page', $per_page)
                ->where('meta.total', 1)
        );
    }

    public function test_get_appointments_success_empty()
    {
        $response = $this->getJson("appointments-api/v1/appointments");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data', 0) //has 1 data
                ->has('meta') //has meta
                ->has('links') //has links
                ->where('meta.current_page', 1)
                ->where('meta.last_page', 1)
                ->where('meta.total', 0)
        );
    }

    public function test_get_appointment_details_succeed()
    {
        $category = Category::factory()->create();
        $file = File::factory()->create();
        $appointment = Event::factory()
            ->has(UserEvent::factory()->count(1), 'user')
            ->has(File::factory()->count(1), 'file')
            ->for($category)
            ->create();
        $appointment->file()->save($file);

        $response = $this->getJson("appointments-api/v1/appointments/{$appointment->id}");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data.elder') //has elder object in appointment object
                ->has('data.user') //has user object in appointment object
                ->has('data.file') //has file object in appointment object
                ->where('data.id', $appointment->id)
                ->where('data.title', $appointment->title)
                ->where('data.start', Carbon::parse($appointment->start)->format("Y-m-d H:i:s")) //example 2022-05-25 13:00:00
                ->where('data.end', Carbon::parse($appointment->end)->format("Y-m-d H:i:s")) //example 2022-05-25 14:00:00
                ->where('data.remark', $appointment->remark)
                ->where('data.category_id', $appointment->category_id)
                ->where('data.file.0.id', $file->id)
                ->where('data.file.0.file_name', $file->file_name)
        );
    }

    public function test_get_appointment_failed_not_found()
    {
        $response = $this->getJson("appointments-api/v1/appointments/100");

        $response->assertNotFound();
    }

    // public function test_post_appointments_success()
    // {
    //     $category = Category::factory()->create();
    //     $file = File::factory()->create();
    //     $data = [
    //         'title' => $this->faker->title,
    //         'day_date' => '2022-05-25',
    //         'start_time' => '02:00 PM',
    //         'end_time' => '03:00 PM',
    //         'remark' => $this->faker->sentence,
    //         'category_id' => $category->id,
    //         'elder_id' => rand(1, 20),
    //         'case_id' => rand(1, 20),
    //         'user_ids' => [rand(1, 20)],
    //         'attachment_ids' => [$file->id]
    //     ];

    //     $response = $this->postJson("appointments-api/v1/appointments", $data);

    //     $response->assertCreated();
    //     $response->assertJson(
    //         fn (AssertableJson $json) => $json
    //             ->has('data')
    //             ->where('data.title', $data['title'])
    //             ->where('data.start', Carbon::parse($data['day_date'] . ' ' . $data['start_time'])->toISOString())
    //             ->where('data.end', Carbon::parse($data['day_date'] . ' ' . $data['end_time'])->toISOString())
    //             ->where('data.remark', $data['remark'])
    //             ->where('data.category_id', $data['category_id'])
    //             ->where('data.elder_id', $data['elder_id'])
    //             ->where('data.case_id', $data['case_id'])
    //     );
    // }

    public function test_post_appointments_failed_file_not_exist()
    {
        $category = Category::factory()->create();
        $data = [
            'title' => $this->faker->title,
            'day_date' => '2022-05-25',
            'start_time' => '02:00 PM',
            'end_time' => '03:00 PM',
            'remark' => $this->faker->sentence,
            'category_id' => $category->id,
            'elder_id' => rand(1, 20),
            'case_id' => rand(1, 20),
            'user_ids' => [rand(1, 20)],
            'attachment_ids' => [100] //file should not exist
        ];

        $response = $this->postJson("appointments-api/v1/appointments", $data);

        $response->assertStatus(422);
    }

    public function test_post_appointments_failed_invalid_date_format()
    {
        $category = Category::factory()->create();
        $file = File::factory()->create();
        $data = [
            'title' => $this->faker->title,
            'day_date' => '2022-05-25',
            'start_time' => '2022', //invalid date format
            'end_time' => '03:00 PM',
            'remark' => $this->faker->sentence,
            'category_id' => $category->id,
            'elder_id' => rand(1, 20),
            'case_id' => rand(1, 20),
            'user_ids' => [rand(1, 20)],
            'attachment_ids' => [$file->id]
        ];

        $response = $this->postJson("appointments-api/v1/appointments", $data);

        $response->assertStatus(422);
    }

    public function test_post_appointments_failed_title_not_exist()
    {
        $category = Category::factory()->create();
        $file = File::factory()->create();
        $data = [
            //no title
            'day_date' => '2022-05-25',
            'start_time' => '02:00 PM',
            'end_time' => '03:00 PM',
            'remark' => $this->faker->sentence,
            'category_id' => $category->id,
            'elder_id' => rand(1, 20),
            'case_id' => rand(1, 20),
            'user_ids' => [rand(1, 20)],
            'attachment_ids' => [$file->id]
        ];

        $response = $this->postJson("appointments-api/v1/appointments", $data);

        $response->assertStatus(422);
    }

    // public function test_put_appointments_success()
    // {
    //     //create initial appointment
    //     $category = Category::factory()->create();
    //     $appointment = Event::factory()
    //         ->has(UserEvent::factory()->count(1), 'user')
    //         ->for($category)
    //         ->create();

    //     //data for update appointment
    //     $file = File::factory()->create();
    //     $data = [
    //         'title' => $this->faker->title,
    //         'day_date' => '2022-05-25',
    //         'start_time' => '02:00 PM',
    //         'end_time' => '03:00 PM',
    //         'remark' => $this->faker->sentence,
    //         'category_id' => $category->id,
    //         'elder_id' => rand(1, 20),
    //         'case_id' => rand(1, 20),
    //         'user_ids' => [rand(1, 20)],
    //         'attachment_ids' => [$file->id]
    //     ];

    //     $response = $this->putJson("appointments-api/v1/appointments/{$appointment->id}", $data);

    //     $response->assertOk();
    //     $response->assertJson(
    //         fn (AssertableJson $json) => $json
    //             ->has('data')
    //             ->where('data.title', $data['title'])
    //             ->where('data.start', Carbon::parse($data['day_date'] . ' ' . $data['start_time'])->toISOString())
    //             ->where('data.end', Carbon::parse($data['day_date'] . ' ' . $data['end_time'])->toISOString())
    //             ->where('data.remark', $data['remark'])
    //             ->where('data.category_id', $data['category_id'])
    //             ->where('data.elder_id', $data['elder_id'])
    //             ->where('data.case_id', $data['case_id'])
    //     );
    // }

    // public function test_put_appointments_failed_not_found()
    // {
    //     //create initial appointment
    //     $category = Category::factory()->create();

    //     //data for update appointment
    //     $file = File::factory()->create();
    //     $data = [
    //         'title' => $this->faker->title,
    //         'day_date' => '2022-05-25',
    //         'start_time' => '02:00 PM',
    //         'end_time' => '03:00 PM',
    //         'remark' => $this->faker->sentence,
    //         'category_id' => $category->id,
    //         'elder_id' => rand(1, 20),
    //         'case_id' => rand(1, 20),
    //         'user_ids' => [rand(1, 20)],
    //         'attachment_ids' => [$file->id]
    //     ];

    //     $response = $this->putJson("appointments-api/v1/appointments/100", $data); //id not exist

    //     $response->assertNotFound();
    // }

    public function test_put_appointments_failed_file_not_exist()
    {
        //create initial appointment
        $category = Category::factory()->create();
        $appointment = Event::factory()
            ->has(UserEvent::factory()->count(1), 'user')
            ->for($category)
            ->create();

        //data for update appointment
        $data = [
            'title' => $this->faker->title,
            'day_date' => '2022-05-25',
            'start_time' => '02:00 PM',
            'end_time' => '03:00 PM',
            'remark' => $this->faker->sentence,
            'category_id' => $category->id,
            'elder_id' => rand(1, 20),
            'case_id' => rand(1, 20),
            'user_ids' => [rand(1, 20)],
            'attachment_ids' => [100] //file should not exist
        ];

        $response = $this->putJson("appointments-api/v1/appointments/{$appointment->id}", $data);

        $response->assertStatus(422);
    }

    public function test_put_appointments_failed_invalid_date_format()
    {
        //create initial appointment
        $category = Category::factory()->create();
        $appointment = Event::factory()
            ->has(UserEvent::factory()->count(1), 'user')
            ->for($category)
            ->create();

        //data for update appointment
        $file = File::factory()->create();
        $data = [
            'title' => $this->faker->title,
            'day_date' => '2022-05-25',
            'start_time' => '2022', //invalid date format
            'end_time' => '03:00 PM',
            'remark' => $this->faker->sentence,
            'category_id' => $category->id,
            'elder_id' => rand(1, 20),
            'case_id' => rand(1, 20),
            'user_ids' => [rand(1, 20)],
            'attachment_ids' => [$file->id]
        ];

        $response = $this->putJson("appointments-api/v1/appointments/{$appointment->id}", $data);

        $response->assertStatus(422);
    }

    public function test_put_appointments_failed_title_not_exist()
    {
        //create initial appointment
        $category = Category::factory()->create();
        $appointment = Event::factory()
            ->has(UserEvent::factory()->count(1), 'user')
            ->for($category)
            ->create();

        //data for update appointment
        $file = File::factory()->create();
        $data = [
            //no title
            'day_date' => '2022-05-25',
            'start_time' => '02:00 PM',
            'end_time' => '03:00 PM',
            'remark' => $this->faker->sentence,
            'category_id' => $category->id,
            'elder_id' => rand(1, 20),
            'case_id' => rand(1, 20),
            'user_ids' => [rand(1, 20)],
            'attachment_ids' => [$file->id]
        ];

        $response = $this->putJson("appointments-api/v1/appointments/{$appointment->id}", $data);

        $response->assertStatus(422);
    }

    public function test_delete_appointment_success()
    {
        $category = Category::factory()->create();
        $appointment = Event::factory()
            ->has(UserEvent::factory()->count(1), 'user')
            ->for($category)
            ->create();

        $this->assertDatabaseHas('events', [
            'id' => $appointment->id
        ]);

        $response = $this->delete("appointments-api/v1/appointments/{$appointment->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('events', [
            'id' => $appointment->id
        ]);
    }

    public function test_delete_appointment_not_found()
    {
        $response = $this->delete("appointments-api/v1/appointments/100");

        $response->assertNotFound();
    }

    public function test_bulk_delete_appointments_success()
    {
        $category = Category::factory()->create();
        $appointments = Event::factory()
            ->has(UserEvent::factory()->count(1), 'user')
            ->for($category)
            ->count(3) //number of appointment
            ->create();

        foreach ($appointments as $appointment) {
            $this->assertDatabaseHas('events', [
                'id' => $appointment->id
            ]);
        }

        $ids = implode(',', $appointments->pluck('id')->toArray());
        $response = $this->delete("appointments-api/v1/appointments?ids={$ids}");

        $response->assertNoContent();
        foreach ($appointments as $appointment) {
            $this->assertDatabaseMissing('events', [
                'id' => $appointment->id
            ]);
        }
    }

    public function test_bulk_delete_appointments_failed_not_found()
    {
        $ids = '1001,1002,1003';
        $response = $this->delete("appointments-api/v1/appointments?ids={$ids}");

        $response->assertNotFound();
    }

    public function test_get_events_success()
    {
        $category = Category::factory()->create();
        $appointment = Event::factory()
            ->has(UserEvent::factory()->count(1), 'user')
            ->for($category)
            ->create();

        $time = Carbon::parse($appointment->start)->format("Y-m-d");
        $response = $this->getJson("appointments-api/v1/appointments/events?start={$time}&end={$time}");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data', 1) //has 1 data
                ->has('data.0.elder_name')
                ->has('data.0.users_name')
                ->where('data.0.id', $appointment->id)
                ->where('data.0.title', $appointment->title)
                ->where('data.0.start', Carbon::parse($appointment->start)->format("Y-m-d H:i:s")) //example 2022-05-25 13:00:00
                ->where('data.0.end', Carbon::parse($appointment->end)->format("Y-m-d H:i:s")) //example 2022-05-25 14:00:00
                ->where('data.0.category_id', $appointment->category_id)
        );
    }

    public function test_get_events_success_empty()
    {
        $time = '2022-01-01';
        $response = $this->getJson("appointments-api/v1/appointments/events?start={$time}&end={$time}");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data', 0) //has 0 data
        );
    }

    public function test_get_events_failed_no_start_end_param()
    {
        $response = $this->getJson("appointments-api/v1/appointments/events");

        $response->assertStatus(422);
    }

    public function test_get_events_failed_incorrect_date_format()
    {
        $time = '2022'; //year without month and date
        $response = $this->getJson("appointments-api/v1/appointments/events?start={$time}&end={$time}");

        $response->assertStatus(422);
    }

    public function test_download_appointments_csv()
    {
        $category = Category::factory()->create();
        $appointment = Event::factory()
            ->has(UserEvent::factory()->count(1), 'user')
            ->for($category)
            ->create();

        $mock_role = '?access_role=admin';
        $response = $this->get("appointments-api/v1/appointments-csv" . $mock_role);

        $response->assertOk();
        $response->assertDownload('appointments.csv');
    }
}
