<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('districts', function (Blueprint $table) {
            $table->string('cga_code')->nullable();
        });
    }

    public function down()
    {
        Schema::table('districts', function (Blueprint $table) {
            $table->dropColumn('cga_code');
        });
    }
};
