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
        Schema::create('cga_care_targets', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('care_plan_id')->unsigned();
            $table->foreign('care_plan_id')->references('id')->on('care_plans');

            $table->string('target')->nullable();
            $table->string('health_vision')->nullable();
            $table->string('long_term_goal')->nullable();
            $table->string('short_term_goal')->nullable();
            $table->smallInteger('motivation')->unsigned()->nullable();
            $table->smallInteger('early_change_stage')->unsigned()->nullable();
            $table->smallInteger('later_change_stage')->unsigned()->nullable();

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
        Schema::dropIfExists('cga_care_targets');
    }
};
