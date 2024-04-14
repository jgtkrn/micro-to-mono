<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->string('health_scale_other')->nullable();
        });
    }

    public function down()
    {
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->dropColumn('health_scale_other');
        });
    }
};
