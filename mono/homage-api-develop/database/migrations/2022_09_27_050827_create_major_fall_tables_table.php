<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('major_fall_tables', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('function_mobility_form_id')->unsigned();
            $table->foreign('function_mobility_form_id')->references('id')->on('function_mobility_forms');

            $table->smallInteger('location')->unsigned()->nullable();
            $table->smallInteger('injury_sustained')->unsigned()->nullable();

            $table->json('created_by')->nullable();
            $table->json('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('major_fall_tables');
    }
};
