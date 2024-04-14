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
            $table->dropColumn('circumference');
        });
        Schema::table('bzn_consultation_notes', function (Blueprint $table) {
            $table->dropColumn('circumference');
        });
        Schema::table('cga_consultation_notes', function (Blueprint $table) {
            $table->string('circumference')->nullable();
        });
        Schema::table('bzn_consultation_notes', function (Blueprint $table) {
            $table->string('circumference')->nullable();
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
            $table->dropColumn('circumference');
        });
        Schema::table('bzn_consultation_notes', function (Blueprint $table) {
            $table->dropColumn('circumference');
        });
        Schema::table('cga_consultation_notes', function (Blueprint $table) {
            $table->smallInteger('circumference')->unsigned()->nullable();
        });
        Schema::table('bzn_consultation_notes', function (Blueprint $table) {
            $table->smallInteger('circumference')->unsigned()->nullable();
        });
    }
};
