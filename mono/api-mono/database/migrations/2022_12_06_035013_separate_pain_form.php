<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            $table->dropColumn([
                'is_pain',
                'provoking_factor',
                'pain_location1',
                'is_dull',
                'is_achy',
                'is_sharp',
                'is_stabbing',
                'stabbing_option',
                'pain_location2',
                'is_relief',
                'what_relief',
                'have_relief_method',
                'relief_method',
                'other_relief_method',
                'pain_scale',
                'when_pain',
                'affect_adl',
                'adl_info',
                'pain_remark',
            ]);
        });
    }

    public function down()
    {
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            $table->boolean('is_pain')->nullable();
            $table->string('provoking_factor')->nullable();
            $table->string('pain_location1')->nullable();
            $table->boolean('is_dull')->nullable();
            $table->boolean('is_achy')->nullable();
            $table->boolean('is_sharp')->nullable();
            $table->boolean('is_stabbing')->nullable();
            $table->enum('stabbing_option', ['constant', 'intermittent'])->nullable();
            $table->string('pain_location2')->nullable();
            $table->boolean('is_relief')->nullable();
            $table->string('what_relief')->nullable();
            $table->boolean('have_relief_method')->nullable();
            $table->smallInteger('relief_method')->unsigned()->nullable();
            $table->string('other_relief_method')->nullable();
            $table->smallInteger('pain_scale')->unsigned()->nullable();
            $table->string('when_pain')->nullable();
            $table->boolean('affect_adl')->nullable();
            $table->string('adl_info')->nullable();
            $table->string('pain_remark')->nullable();
        });
    }
};
