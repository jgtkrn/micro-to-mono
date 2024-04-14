<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->dropColumn(['health_scales']);
        });
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->string('health_scales')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->dropColumn(['health_scales']);
        });
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->enum('health_scales', [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 'other'])->nullable();
        });
    }
};
