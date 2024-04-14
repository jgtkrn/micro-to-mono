<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('follow_up_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id');
            $table->date('date');
            $table->dateTime('time');
            $table->foreignId('appointment_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('follow_up_histories');
    }
};
