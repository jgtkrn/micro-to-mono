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
        Schema::create('medication_adherence_forms', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('assessment_case_id')->unsigned();
            $table->foreign('assessment_case_id')->references('id')->on('assessment_cases');

            $table->boolean('is_forget_sometimes')->nullable();
            $table->boolean('is_missed_meds')->nullable();
            $table->boolean('is_reduce_meds')->nullable();
            $table->boolean('is_forget_when_travel')->nullable();
            $table->boolean('is_meds_yesterday')->nullable();
            $table->boolean('is_stop_when_better')->nullable();
            $table->boolean('is_annoyed')->nullable();
            $table->string('forget_sometimes')->nullable();
            $table->string('missed_meds')->nullable();
            $table->string('reduce_meds')->nullable();
            $table->string('forget_when_travel')->nullable();
            $table->string('meds_yesterday')->nullable();
            $table->string('stop_when_better')->nullable();
            $table->string('annoyed')->nullable();
            $table->smallInteger('forget_frequency')->unsigned()->nullable();

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
        Schema::dropIfExists('medication_adherence_forms');
    }
};
