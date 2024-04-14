<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('medication_histories', function (Blueprint $table) {
            $table->id();
            $table->string('medication_category');
            $table->string('medication_name');
            $table->string('dosage');
            $table->string('number_of_intake');
            $table->json('frequency');
            $table->string('route');
            $table->string('remarks')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medication_histories');
    }
};
