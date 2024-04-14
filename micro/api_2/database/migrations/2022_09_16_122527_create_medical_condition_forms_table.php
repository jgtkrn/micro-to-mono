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
        Schema::create('medical_condition_forms', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('assessment_case_id')->unsigned();
            $table->foreign('assessment_case_id')->references('id')->on('assessment_cases');

            $table->string('complaint')->nullable();
            $table->boolean('has_medical_history')->nullable();
            $table->string('medical_history')->nullable();
            $table->string('premorbid')->nullable();
            $table->string('followup_appointment')->nullable();
            $table->boolean('has_allergy')->nullable();
            $table->string('allergy_description')->nullable();
            $table->boolean('has_medication')->nullable();
            $table->string('medication_description')->nullable();

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
        Schema::dropIfExists('medical_condition_forms');
    }
};
