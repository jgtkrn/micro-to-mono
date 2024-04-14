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
        Schema::create('physiological_measurement_forms', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('assessment_case_id')->unsigned();
            $table->foreign('assessment_case_id')->references('id')->on('assessment_cases');

            $table->integer('temperature')->nullable();
            $table->integer('sitting_sbp')->nullable();
            $table->integer('sitting_dbp')->nullable();
            $table->integer('standing_sbp')->nullable();
            $table->integer('standing_dbp')->nullable();
            $table->integer('blood_oxygen')->nullable();
            $table->integer('heart_rate')->nullable();
            $table->smallInteger('heart_rythm')->unsigned()->nullable();
            $table->smallInteger('kardia')->unsigned()->nullable();
            $table->decimal('blood_sugar')->nullable();
            $table->smallInteger('blood_sugar_time')->unsigned()->nullable();
            $table->integer('waistline')->nullable();
            $table->integer('weight')->nullable();
            $table->decimal('height')->nullable();

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
        Schema::dropIfExists('physiological_measurement_forms');
    }
};
