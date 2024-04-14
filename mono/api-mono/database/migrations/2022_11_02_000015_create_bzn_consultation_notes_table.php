<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bzn_consultation_notes', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('bzn_target_id')->unsigned();
            $table->foreign('bzn_target_id')->references('id')->on('bzn_care_targets');

            // Assessor Information
            $table->string('assessor')->nullable();
            $table->string('meeting')->nullable();
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

            // Intervention Target 1
            $table->smallInteger('domain')->unsigned()->nullable();
            $table->smallInteger('urgency')->unsigned()->nullable();
            $table->smallInteger('category')->unsigned()->nullable();
            $table->string('intervention_remark')->nullable();
            $table->string('consultation_remark')->nullable();
            $table->string('area')->nullable();
            $table->smallInteger('priority')->unsigned()->nullable();
            $table->string('target')->nullable();
            $table->smallInteger('modifier')->unsigned()->nullable();
            $table->string('ssa')->nullable();
            $table->smallInteger('knowledge')->unsigned()->nullable();
            $table->smallInteger('behaviour')->unsigned()->nullable();
            $table->smallInteger('status')->unsigned()->nullable();

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
        Schema::dropIfExists('bzn_consultation_notes');
    }
};
