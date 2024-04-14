<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('elder_calls', function (Blueprint $table) {
            $table->date('call_date')->nullable();
        });
    }

    public function down()
    {
        Schema::table('elder_calls', function (Blueprint $table) {
            $table->dropColumn('call_date');
        });
    }
};
