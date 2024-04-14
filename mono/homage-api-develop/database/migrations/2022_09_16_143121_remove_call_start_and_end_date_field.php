<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('elder_calls', function (Blueprint $table) {
            $table->dropColumn(['call_start', 'call_end']);
        });
    }

    public function down()
    {
        Schema::table('elder_calls', function (Blueprint $table) {
            $table->dateTime('call_start')->nullable();
            $table->dateTime('call_end')->nullable();
        });
    }
};
