<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('medication_histories', function (Blueprint $table) {
            $table->text('routes_other')->nullable();
        });
    }

    public function down()
    {
        Schema::table('medication_histories', function (Blueprint $table) {
            $table->dropColumn('routes_other');
        });
    }
};
