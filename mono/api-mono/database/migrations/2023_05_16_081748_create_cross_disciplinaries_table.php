<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cross_disciplinaries', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('case_id')->unsigned()->nullable();
            $table->string('role')->nullable();
            $table->string('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cross_disciplinaries');
    }
};
