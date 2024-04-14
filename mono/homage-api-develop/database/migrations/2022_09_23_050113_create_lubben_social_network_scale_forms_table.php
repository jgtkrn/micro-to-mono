<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lubben_social_network_scale_forms', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('assessment_case_id')->unsigned();
            $table->foreign('assessment_case_id')->references('id')->on('assessment_cases');

            $table->string('elderly_central_ref_number')->nullable();
            $table->date('assessment_date')->nullable();
            $table->string('assessor_name')->nullable();
            $table->smallInteger('assessment_kind')->unsigned()->nullable();

            $table->smallInteger('relatives_sum')->unsigned()->nullable();
            $table->smallInteger('relatives_to_talk')->unsigned()->nullable();
            $table->smallInteger('relatives_to_help')->unsigned()->nullable();
            $table->smallInteger('friends_sum')->unsigned()->nullable();
            $table->smallInteger('friends_to_talk')->unsigned()->nullable();
            $table->smallInteger('friends_to_help')->unsigned()->nullable();
            $table->integer('lubben_total_score')->nullable();

            $table->json('created_by')->nullable();
            $table->json('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lubben_social_network_scale_forms');
    }
};
