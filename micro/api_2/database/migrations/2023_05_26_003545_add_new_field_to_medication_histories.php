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
        Schema::table('medication_histories', function (Blueprint $table) {
            $table->string('qi_data')->nullable();
            $table->string('frequency_other')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medication_histories', function (Blueprint $table) {
            $table->dropColumn(['qi_data', 'frequency_other']);
        });
    }
};
