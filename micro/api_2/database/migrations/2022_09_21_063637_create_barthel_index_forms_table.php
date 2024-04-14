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
        Schema::create('barthel_index_forms', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('assessment_case_id')->unsigned();
            $table->foreign('assessment_case_id')->references('id')->on('assessment_cases');

            $table->smallInteger('bowels')->unsigned()->nullable();
            $table->smallInteger('bladder')->unsigned()->nullable();
            $table->smallInteger('grooming')->unsigned()->nullable();
            $table->smallInteger('toilet_use')->unsigned()->nullable();
            $table->smallInteger('feeding')->unsigned()->nullable();
            $table->smallInteger('transfer')->unsigned()->nullable();
            $table->smallInteger('mobility')->unsigned()->nullable();
            $table->smallInteger('dressing')->unsigned()->nullable();
            $table->smallInteger('stairs')->unsigned()->nullable();
            $table->smallInteger('bathing')->unsigned()->nullable();
            $table->integer('barthel_total_score')->nullable();

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
        Schema::dropIfExists('barthel_index_forms');
    }
};
