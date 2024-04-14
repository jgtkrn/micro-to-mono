<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cga_consultation_notes', function (Blueprint $table) {
            $table->string('visiting_duration')->nullable();
        });
        Schema::table('bzn_consultation_notes', function (Blueprint $table) {
            $table->string('visiting_duration')->nullable();
        });
    }

    public function down()
    {
        Schema::table('cga_consultation_notes', function (Blueprint $table) {
            $table->dropColumn('visiting_duration');
        });
        Schema::table('bzn_consultation_notes', function (Blueprint $table) {
            $table->dropColumn('visiting_duration');
        });
    }
};
