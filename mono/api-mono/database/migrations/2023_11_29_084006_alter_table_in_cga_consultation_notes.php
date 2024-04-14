<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cga_consultation_notes', function (Blueprint $table) {
            $table->text('progress')->nullable()->change();
            $table->text('case_summary')->nullable()->change();
            $table->text('followup')->nullable()->change();
            $table->text('personal_insight')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('cga_consultation_notes', function (Blueprint $table) {
            $table->string('progress')->nullable()->change();
            $table->string('case_summary')->nullable()->change();
            $table->string('followup')->nullable()->change();
            $table->string('personal_insight')->nullable()->change();
        });
    }
};
