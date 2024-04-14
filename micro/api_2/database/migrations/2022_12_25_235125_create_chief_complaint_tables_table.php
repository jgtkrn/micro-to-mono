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
        Schema::create('chief_complaint_tables', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('medical_condition_form_id')->unsigned();
            $table->foreign('medical_condition_form_id')->references('id')->on('medical_condition_forms');
            $table->string('complaint')->nullable();
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
        Schema::dropIfExists('chief_complaint_tables');
    }
};
