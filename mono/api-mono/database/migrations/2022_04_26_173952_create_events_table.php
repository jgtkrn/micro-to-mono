<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->timestamps();
            $table->integer('elder_id')->nullable();
            $table->integer('case_id')->nullable();
            $table->string('remark')->nullable();
            $table->string('address')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}
