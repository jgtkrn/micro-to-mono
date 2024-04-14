<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hospitalization_tables', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('qualtrics_form_id')->unsigned();
            $table->foreign('qualtrics_form_id')->references('id')->on('qualtrics_forms');
            $table->string('hosp_month')->nullable();
            $table->string('hosp_year')->nullable();
            $table->enum('hosp_hosp', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->nullable();
            $table->string('hosp_hosp_other')->nullable();
            $table->enum('hosp_way', [1, 2, 3, 4])->nullable();
            $table->enum('hosp_home', [1, 2, 3, 4, 5])->nullable();
            $table->string('hosp_home_else')->nullable();
            $table->enum('hosp_reason', [1, 2, 3, 4, 5])->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospitalization_tables');
    }
};
