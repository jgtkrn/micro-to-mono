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
                'most_time_good_mood',
                'irritable_and_fidgety',
                'good_to_be_alive',
                'feeling_down',
                'gds4_score',
            ]);
        });
    }

    public function down()
    {
        Schema::table('geriatric_depression_scale_forms', function (Blueprint $table) {
            $table->smallInteger('most_time_good_mood')->unsigned()->nullable();
            $table->smallInteger('irritable_and_fidgety')->unsigned()->nullable();
            $table->smallInteger('good_to_be_alive')->unsigned()->nullable();
            $table->smallInteger('feeling_down')->unsigned()->nullable();
            $table->integer('gds4_score')->nullable();
        });
    }
};
