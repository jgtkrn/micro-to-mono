<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('route_loggers', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->string('method')->nullable();
            $table->string('url')->nullable();
            $table->unsignedBigInteger('time')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('route_loggers');
    }
};
