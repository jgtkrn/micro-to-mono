<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geriatric_depression_scale_forms', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('assessment_case_id')->unsigned();
            $table->foreign('assessment_case_id')->references('id')->on('assessment_cases');

            $table->string('elderly_central_ref_number')->nullable();
            $table->date('assessment_date')->nullable();
            $table->string('assessor_name')->nullable();
            $table->smallInteger('assessment_kind')->unsigned()->nullable();

            $table->smallInteger('is_satisfied')->unsigned()->nullable();
            $table->smallInteger('is_given_up')->unsigned()->nullable();
            $table->smallInteger('is_feel_empty')->unsigned()->nullable();
            $table->smallInteger('is_often_bored')->unsigned()->nullable();
            $table->smallInteger('is_happy_a_lot')->unsigned()->nullable();
            $table->smallInteger('is_affraid')->unsigned()->nullable();
            $table->smallInteger('is_happy_all_day')->unsigned()->nullable();
            $table->smallInteger('is_feel_helpless')->unsigned()->nullable();
            $table->smallInteger('is_prefer_stay')->unsigned()->nullable();
            $table->smallInteger('is_memory_problem')->unsigned()->nullable();
            $table->smallInteger('is_good_to_alive')->unsigned()->nullable();
            $table->smallInteger('is_feel_useless')->unsigned()->nullable();
            $table->smallInteger('is_feel_energic')->unsigned()->nullable();
            $table->smallInteger('is_hopeless')->unsigned()->nullable();
            $table->smallInteger('is_people_better')->unsigned()->nullable();
            $table->integer('gds15_score')->nullable();
            $table->smallInteger('most_time_good_mood')->unsigned()->nullable();
            $table->smallInteger('irritable_and_fidgety')->unsigned()->nullable();
            $table->smallInteger('good_to_be_alive')->unsigned()->nullable();
            $table->smallInteger('feeling_down')->unsigned()->nullable();
            $table->integer('gds4_score')->nullable();

            $table->json('created_by')->nullable();
            $table->json('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geriatric_depression_scale_forms');
    }
};
