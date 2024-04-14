<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bzn_care_targets', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('care_plan_id')->unsigned();
            $table->foreign('care_plan_id')->references('id')->on('care_plans');

            $table->string('intervention')->nullable();
            $table->unsignedSmallInteger('target_type')->nullable();
            $table->string('plan')->nullable();

            $table->json('created_by')->nullable();
            $table->json('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bzn_care_targets');
    }
};
