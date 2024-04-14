<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('care_plans', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();

            $table->unsignedBigInteger('case_id');
            $table->enum('case_type', ['CGA', 'BZN'])->nullable();
            $table->string('case_manager')->nullable();
            $table->string('handler')->nullable();

            $table->json('created_by')->nullable();
            $table->json('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('care_plans');
    }
};
