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
        Schema::table('bzn_care_targets', function (Blueprint $table) {
            $table->text('omaha_s')->nullable();
        });
        Schema::table('bzn_consultation_notes', function (Blueprint $table) {
            $table->text('omaha_s')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bzn_care_targets', function (Blueprint $table) {
            $table->dropColumn('omaha_s');
        });
        Schema::table('bzn_consultation_notes', function (Blueprint $table) {
            $table->dropColumn('omaha_s');
        });
    }
};
