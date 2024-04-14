<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assessment_case_statuses', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('assessment_case_id')->unsigned();
            $table->foreign('assessment_case_id')->references('id')->on('assessment_cases');

            $table->smallInteger('status')->unsigned()->nullable();
            $table->string('remarks')->nullable();

            $table->json('created_by')->nullable();
            $table->json('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assessment_case_statuses');
    }
};
