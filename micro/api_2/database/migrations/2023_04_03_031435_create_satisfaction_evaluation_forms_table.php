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
        Schema::create('satisfaction_evaluation_forms', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();

            $table->string('elder_reference_number')->nullable();
            $table->string('assessor_name')->nullable();
            $table->date('evaluation_date')->nullable();

            $table->smallInteger('clear_plan')->unsigned()->nullable();
            $table->smallInteger('enough_discuss_time')->unsigned()->nullable();
            $table->smallInteger('appropriate_plan')->unsigned()->nullable();
            $table->smallInteger('has_discussion_team')->unsigned()->nullable();
            $table->smallInteger('own_involved')->unsigned()->nullable();
            $table->smallInteger('enough_opportunities')->unsigned()->nullable();
            $table->smallInteger('enough_information')->unsigned()->nullable();
            $table->smallInteger('selfcare_improved')->unsigned()->nullable();
            $table->smallInteger('confidence_team')->unsigned()->nullable();
            $table->smallInteger('feel_respected')->unsigned()->nullable();
            $table->smallInteger('performance_rate')->unsigned()->nullable();
            $table->smallInteger('service_scale')->unsigned()->nullable();
            $table->smallInteger('recommend_service')->unsigned()->nullable();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('created_by_name')->nullable();
            $table->string('updated_by_name')->nullable();
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
        Schema::dropIfExists('satisfaction_evaluation_forms');
    }
};
