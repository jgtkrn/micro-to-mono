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
        Schema::create('pre_coaching_pams', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('care_plan_id')->unsigned();
            $table->foreign('care_plan_id')->references('id')->on('care_plans');
            $table->smallInteger('section')->unsigned()->nullable();
            $table->smallInteger('intervention_group')->unsigned()->nullable();
            $table->smallInteger('gender')->unsigned()->nullable();
            $table->smallInteger('health_manage')->unsigned()->nullable();
            $table->smallInteger('active_role')->unsigned()->nullable();
            $table->smallInteger('self_confidence')->unsigned()->nullable();
            $table->smallInteger('drug_knowledge')->unsigned()->nullable();
            $table->smallInteger('self_understanding')->unsigned()->nullable();
            $table->smallInteger('self_health')->unsigned()->nullable();
            $table->smallInteger('self_discipline')->unsigned()->nullable();
            $table->smallInteger('issue_knowledge')->unsigned()->nullable();
            $table->smallInteger('other_treatment')->unsigned()->nullable();
            $table->smallInteger('change_treatment')->unsigned()->nullable();
            $table->smallInteger('issue_prevention')->unsigned()->nullable();
            $table->smallInteger('find_solutions')->unsigned()->nullable();
            $table->smallInteger('able_maintain')->unsigned()->nullable();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('pre_coaching_pams');
    }
};
