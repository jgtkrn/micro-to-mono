<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('function_mobility_forms', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('assessment_case_id')->unsigned();
            $table->foreign('assessment_case_id')->references('id')->on('assessment_cases');

            $table->smallInteger('iadl')->unsigned()->nullable();
            $table->integer('total_iadl_score')->nullable();
            $table->smallInteger('mobility')->unsigned()->nullable();
            $table->smallInteger('walk_with_assistance')->unsigned()->nullable();
            $table->string('mobility_tug')->nullable();
            $table->string('mobility_single_leg')->nullable();
            $table->smallInteger('range_of_motion')->unsigned()->nullable();
            $table->smallInteger('upper_limb_left')->unsigned()->nullable();
            $table->smallInteger('upper_limb_right')->unsigned()->nullable();
            $table->smallInteger('lower_limb_left')->unsigned()->nullable();
            $table->smallInteger('lower_limb_right')->unsigned()->nullable();
            $table->boolean('fall_history')->nullable();
            $table->string('fall_tug')->nullable();
            $table->string('fall_single_leg')->nullable();
            $table->smallInteger('number_of_major_fall')->unsigned()->nullable();

            $table->json('created_by')->nullable();
            $table->json('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('function_mobility_forms');
    }
};
