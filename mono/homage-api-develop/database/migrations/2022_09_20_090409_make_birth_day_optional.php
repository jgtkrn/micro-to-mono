<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('elders', function (Blueprint $table) {
            $table->integer('birth_day')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('elders', function (Blueprint $table) {
            $table->integer('birth_day')->change();
        });
    }
};
