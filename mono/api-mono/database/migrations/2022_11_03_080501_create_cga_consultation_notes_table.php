<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cga_consultation_notes', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('cga_target_id')->unsigned();
            $table->foreign('cga_target_id')->references('id')->on('cga_care_targets');

            // Assessor Information
            $table->string('assessor_1')->nullable();
            $table->string('assessor_2')->nullable();
            $table->string('visit_type')->nullable();
            $table->date('assessment_date')->nullable();
            $table->time('assessment_time')->nullable();

            // Vital Sign
            $table->smallInteger('sbp')->unsigned()->nullable();
            $table->smallInteger('dbp')->unsigned()->nullable();
            $table->smallInteger('pulse')->unsigned()->nullable();
            $table->smallInteger('pao')->unsigned()->nullable();
            $table->smallInteger('hstix')->unsigned()->nullable();
            $table->smallInteger('body_weight')->unsigned()->nullable();
            $table->smallInteger('waist')->unsigned()->nullable();
            $table->smallInteger('circumference')->unsigned()->nullable();

            // Log
            $table->smallInteger('purpose')->unsigned()->nullable();
            $table->string('content')->nullable();
            $table->string('progress')->nullable();
            $table->string('case_summary')->nullable();
            $table->smallInteger('followup_options')->unsigned()->nullable();
            $table->string('followup')->nullable();
            $table->string('personal_insight')->nullable();

            // Case Status
            $table->smallInteger('case_status')->unsigned()->nullable();
            $table->string('case_remark')->nullable();

            $table->json('created_by')->nullable();
            $table->json('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cga_consultation_notes');
    }
};
