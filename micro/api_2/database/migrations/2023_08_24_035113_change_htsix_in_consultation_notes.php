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
        Schema::table('cga_consultation_notes', function (Blueprint $table) {
            $table->dropColumn('hstix');
        });
        Schema::table('bzn_consultation_notes', function (Blueprint $table) {
            $table->dropColumn('hstix');
        });
        Schema::table('cga_consultation_notes', function (Blueprint $table) {
            $table->decimal('hstix')->nullable();
        });
        Schema::table('bzn_consultation_notes', function (Blueprint $table) {
            $table->decimal('hstix')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cga_consultation_notes', function (Blueprint $table) {
            $table->dropColumn('hstix');
        });
        Schema::table('bzn_consultation_notes', function (Blueprint $table) {
            $table->dropColumn('hstix');
        });
        Schema::table('cga_consultation_notes', function (Blueprint $table) {
            $table->smallInteger('hstix')->unsigned()->nullable();
        });
        Schema::table('bzn_consultation_notes', function (Blueprint $table) {
            $table->smallInteger('hstix')->unsigned()->nullable();
        });
    }
};
