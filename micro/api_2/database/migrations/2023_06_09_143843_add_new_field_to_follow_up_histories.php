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
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('appointment_other_text');
        });
        Schema::table('follow_up_histories', function (Blueprint $table) {
            $table->string('appointment_other_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('appointment_other_text')->nullable();
        });
        Schema::table('follow_up_histories', function (Blueprint $table) {
            $table->dropColumn('appointment_other_text');
        });
    }
};
