<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('physiological_measurement_forms', function (Blueprint $table) {
            $table->integer('respiratory_rate')->nullable();
        });
    }

    public function down()
    {
        Schema::table('physiological_measurement_forms', function (Blueprint $table) {
            $table->dropColumn('respiratory_rate');
        });
    }
};
