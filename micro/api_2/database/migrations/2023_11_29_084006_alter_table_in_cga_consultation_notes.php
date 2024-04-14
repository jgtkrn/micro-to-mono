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
            $table->text('progress')->nullable()->change();
            $table->text('case_summary')->nullable()->change();
            $table->text('followup')->nullable()->change();
            $table->text('personal_insight')->nullable()->change();
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
            $table->string('progress')->nullable()->change();
            $table->string('case_summary')->nullable()->change();
            $table->string('followup')->nullable()->change();
            $table->string('personal_insight')->nullable()->change();
        });
    }
};
