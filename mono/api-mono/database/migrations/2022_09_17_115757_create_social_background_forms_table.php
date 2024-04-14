<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('social_background_forms', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('assessment_case_id')->unsigned();
            $table->foreign('assessment_case_id')->references('id')->on('assessment_cases');

            $table->smallInteger('marital_status')->unsigned()->nullable();
            $table->smallInteger('living_status')->unsigned()->nullable();
            $table->boolean('safety_alarm')->nullable();
            $table->boolean('has_carer')->nullable();
            $table->smallInteger('carer_option')->unsigned()->nullable();
            $table->string('carer')->nullable();
            $table->smallInteger('employment_status')->unsigned()->nullable();
            $table->boolean('has_community_resource')->nullable();
            $table->string('community_resource')->nullable();
            $table->smallInteger('education_level')->unsigned()->nullable();
            $table->smallInteger('financial_state')->unsigned()->nullable();
            $table->smallInteger('smoking_option')->unsigned()->nullable();
            $table->integer('smoking')->nullable();
            $table->smallInteger('drinking_option')->unsigned()->nullable();
            $table->integer('drinking')->nullable();
            $table->boolean('has_religion')->nullable();
            $table->string('religion')->nullable();
            $table->boolean('has_social_activity')->nullable();
            $table->string('social_activity')->nullable();
            $table->integer('lubben_total_score')->nullable();

            $table->json('created_by')->nullable();
            $table->json('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('social_background_forms');
    }
};
