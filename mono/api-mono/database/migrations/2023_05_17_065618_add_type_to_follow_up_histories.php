<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('follow_up_histories', function (Blueprint $table) {
            $table->string('type')->nullable();
        });
    }

    public function down()
    {
        Schema::table('follow_up_histories', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
