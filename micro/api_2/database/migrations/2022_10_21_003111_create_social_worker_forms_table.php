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
        Schema::create('social_worker_forms', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('assessment_case_id')->unsigned();
            $table->foreign('assessment_case_id')->references('id')->on('assessment_cases');

            $table->string('assessor_1')->nullable();
            $table->string('assessor_2')->nullable();

            // Qualtric Social Worker
            // Elderly Information
            $table->enum('elder_marital', [1, 2, 3, 4, 5])->nullable();
            $table->enum('elder_living', [1, 2, 3, 4, 5])->nullable();
            $table->enum('elder_carer', [1, 2, 3])->nullable();
            $table->enum('elder_is_carer', [1, 2, 3])->nullable();
            $table->enum('elder_edu', [1, 2, 3, 4, 5])->nullable();
            $table->enum('elder_religious', [1, 2, 3, 4, 5, 6, 7, 8, 9])->nullable();
            $table->enum('elder_housetype', [1, 2, 3, 4, 5, 6, 7, 8])->nullable();
            $table->enum('elder_bell', [1, 2, 3])->nullable();
            $table->enum('elder_home_fall', [1, 2, 3, 4, 5, 6])->nullable();
            $table->enum('elder_home_hygiene', [1, 2, 3, 4, 5, 6, 7])->nullable();
            $table->enum('elder_home_bug', [1, 2])->nullable();

            // Social Service
            $table->enum('elderly_center', [1, 2])->nullable();
            $table->enum('home_service', [1, 2, 3, 4, 5, 6, 7])->nullable();
            $table->enum('elderly_daycare', [1, 2])->nullable();
            $table->enum('longterm_service', [1, 2])->nullable();
            $table->enum('life_support', [1, 2, 3, 4, 5, 6, 7])->nullable();
            $table->enum('financial_support', [1, 2])->nullable();

            // Lifestyle
            $table->enum('spesific_program', [1, 2, 3, 4, 5])->nullable();
            $table->enum('high_cardio20', [1, 2, 3, 4, 5])->nullable();
            $table->enum('low_cardio40', [1, 2, 3, 4, 5])->nullable();
            $table->enum('recreation', [1, 2, 3, 4, 5])->nullable();
            $table->enum('streching3w', [1, 2, 3, 4, 5])->nullable();
            $table->enum('daily_workout', [1, 2, 3, 4, 5])->nullable();
            $table->enum('ate_fruit24', [1, 2, 3, 4, 5])->nullable();
            $table->enum('ate_veggie35', [1, 2, 3, 4, 5])->nullable();
            $table->enum('ate_dairy23', [1, 2, 3, 4, 5])->nullable();
            $table->enum('ate_protein23', [1, 2, 3, 4, 5])->nullable();
            $table->enum('have_breakfast', [1, 2, 3, 4, 5])->nullable();
            $table->enum('smoking_behavior', [1, 2, 3])->nullable();
            $table->enum('alcohol_frequent', [1, 2, 3, 4, 5])->nullable();

            // Functional
            $table->enum('diff_wearing', [1, 2, 3, 4, 5])->nullable();
            $table->enum('diff_bathing', [1, 2, 3, 4, 5])->nullable();
            $table->enum('diff_eating', [1, 2, 3, 4, 5])->nullable();
            $table->enum('diff_wakeup', [1, 2, 3, 4, 5])->nullable();
            $table->enum('diff_toilet', [1, 2, 3, 4, 5])->nullable();
            $table->enum('diff_urine', [1, 2, 3, 4, 5])->nullable();
            $table->smallInteger('can_use_phone')->unsigned()->nullable();
            $table->string('text_use_phone')->nullable();
            $table->smallInteger('can_take_ride')->unsigned()->nullable();
            $table->string('text_take_ride')->nullable();
            $table->smallInteger('can_buy_food')->unsigned()->nullable();
            $table->string('text_buy_food')->nullable();
            $table->smallInteger('can_cook')->unsigned()->nullable();
            $table->string('text_cook')->nullable();
            $table->smallInteger('can_do_housework')->unsigned()->nullable();
            $table->string('text_do_housework')->nullable();
            $table->smallInteger('can_do_repairment')->unsigned()->nullable();
            $table->string('text_do_repairment')->nullable();
            $table->smallInteger('can_do_laundry')->unsigned()->nullable();
            $table->string('text_do_laundry')->nullable();
            $table->smallInteger('can_take_medicine')->unsigned()->nullable();
            $table->string('text_take_medicine')->nullable();
            $table->smallInteger('can_handle_finances')->unsigned()->nullable();
            $table->string('text_handle_finances')->nullable();
            $table->integer('iadl_total_score')->nullable();

            // Cognitive
            $table->enum('forget_stuff', [1, 2, 3])->nullable();
            $table->enum('forget_friend', [1, 2, 3])->nullable();
            $table->enum('forget_word', [1, 2, 3])->nullable();
            $table->enum('correct_word', [1, 2, 3])->nullable();
            $table->enum('bad_memory', [1, 2, 3])->nullable();
            $table->enum('moca_edu', [1, 2, 3, 4, 5, 6])->nullable();

            // Psycho Social
            $table->smallInteger('relatives_sum')->unsigned()->nullable();
            $table->smallInteger('relatives_to_talk')->unsigned()->nullable();
            $table->smallInteger('relatives_to_help')->unsigned()->nullable();
            $table->smallInteger('friends_sum')->unsigned()->nullable();
            $table->smallInteger('friends_to_talk')->unsigned()->nullable();
            $table->smallInteger('friends_to_help')->unsigned()->nullable();
            $table->integer('lubben_total_score')->nullable();
            $table->boolean('genogram_done')->nullable();
            $table->enum('less_friend', [1, 2, 3, 4, 5])->nullable();
            $table->enum('feel_ignored', [1, 2, 3, 4, 5])->nullable();
            $table->enum('feel_lonely', [1, 2, 3, 4, 5])->nullable();
            $table->smallInteger('most_time_good_mood')->unsigned()->nullable();
            $table->smallInteger('irritable_and_fidgety')->unsigned()->nullable();
            $table->smallInteger('good_to_be_alive')->unsigned()->nullable();
            $table->smallInteger('feeling_down')->unsigned()->nullable();
            $table->integer('gds4_score')->nullable();

            // Stratification & Remark
            $table->enum('do_referral', [1, 2, 3, 4, 5, 6])->nullable();
            $table->enum('diagnosed_dementia', [0, 1])->nullable();
            $table->enum('suggest', [1, 2, 3])->nullable();
            $table->enum('not_suitable', [1, 2, 3, 4, 5, 6, 7])->nullable();
            $table->string('sw_remark')->nullable();

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
        Schema::dropIfExists('social_worker_forms');
    }
};
