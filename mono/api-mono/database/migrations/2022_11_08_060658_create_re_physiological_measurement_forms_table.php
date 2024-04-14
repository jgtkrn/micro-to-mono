<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('re_physiological_measurement_forms', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('assessment_case_id')->unsigned();
            $table->foreign('assessment_case_id')->references('id')->on('assessment_cases');

            $table->integer('re_temperature')->nullable();
            $table->integer('re_sitting_sbp')->nullable();
            $table->integer('re_sitting_dbp')->nullable();
            $table->integer('re_standing_sbp')->nullable();
            $table->integer('re_standing_dbp')->nullable();
            $table->integer('re_blood_oxygen')->nullable();
            $table->integer('re_heart_rate')->nullable();
            $table->smallInteger('re_heart_rythm')->unsigned()->nullable();
            $table->smallInteger('re_kardia')->unsigned()->nullable();
            $table->decimal('re_blood_sugar')->nullable();
            $table->smallInteger('re_blood_sugar_time')->unsigned()->nullable();
            $table->integer('re_waistline')->nullable();
            $table->integer('re_weight')->nullable();
            $table->decimal('re_height')->nullable();
            $table->integer('re_respiratory_rate')->nullable();

            $table->json('created_by')->nullable();
            $table->json('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('re_physiological_measurement_forms');
    }
};
