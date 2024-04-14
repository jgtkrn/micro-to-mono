<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('elders', function (Blueprint $table) {
            $table->string('zone_other')->nullable();
        });
    }

    public function down()
    {
        Schema::table('elders', function (Blueprint $table) {
            $table->dropColumn('zone_other');
        });
    }
};
