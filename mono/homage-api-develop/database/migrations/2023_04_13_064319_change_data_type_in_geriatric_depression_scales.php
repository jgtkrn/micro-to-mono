<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('geriatric_depression_scale_forms', function (Blueprint $table) {
            $table->dropColumn([
                'assessment_kind',
                'is_satisfied',
                'is_given_up',
                'is_feel_empty',
                'is_often_bored',
                'is_happy_a_lot',
                'is_affraid',
                'is_happy_all_day',
                'is_feel_helpless',
                'is_prefer_stay',
                'is_memory_problem',
                'is_good_to_alive',
                'is_feel_useless',
                'is_feel_energic',
                'is_hopeless',
                'is_people_better',
                'gds15_score',
            ]);
        });

        Schema::table('geriatric_depression_scale_forms', function (Blueprint $table) {
            $table->smallInteger('assessment_kind')->nullable();
            $table->smallInteger('is_satisfied')->nullable();
            $table->smallInteger('is_given_up')->nullable();
            $table->smallInteger('is_feel_empty')->nullable();
            $table->smallInteger('is_often_bored')->nullable();
            $table->smallInteger('is_happy_a_lot')->nullable();
            $table->smallInteger('is_affraid')->nullable();
            $table->smallInteger('is_happy_all_day')->nullable();
            $table->smallInteger('is_feel_helpless')->nullable();
            $table->smallInteger('is_prefer_stay')->nullable();
            $table->smallInteger('is_memory_problem')->nullable();
            $table->smallInteger('is_good_to_alive')->nullable();
            $table->smallInteger('is_feel_useless')->nullable();
            $table->smallInteger('is_feel_energic')->nullable();
            $table->smallInteger('is_hopeless')->nullable();
            $table->smallInteger('is_people_better')->nullable();
            $table->integer('gds15_score')->nullable();
        });
    }

    public function down()
    {
        Schema::table('geriatric_depression_scale_forms', function (Blueprint $table) {
            $table->dropColumn([
                'assessment_kind',
                'is_satisfied',
                'is_given_up',
                'is_feel_empty',
                'is_often_bored',
                'is_happy_a_lot',
                'is_affraid',
                'is_happy_all_day',
                'is_feel_helpless',
                'is_prefer_stay',
                'is_memory_problem',
                'is_good_to_alive',
                'is_feel_useless',
                'is_feel_energic',
                'is_hopeless',
                'is_people_better',
                'gds15_score',
            ]);
        });

        Schema::table('geriatric_depression_scale_forms', function (Blueprint $table) {
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
        });
    }
};
