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
        Schema::table('cross_disciplinaries', function (Blueprint $table) {
            $table->dropColumn('comments');
        });
        Schema::table('cross_disciplinaries', function (Blueprint $table) {
            $table->text('comments')->nullable();
        });
        Schema::table('bzn_consultation_notes', function (Blueprint $table) {
            $table->dropColumn(['case_remark', 'consultation_remark', 'intervention_remark']);
        });
        Schema::table('bzn_consultation_notes', function (Blueprint $table) {
            $table->text('case_remark')->nullable();
            $table->text('consultation_remark')->nullable();
            $table->text('intervention_remark')->nullable();
        });
        Schema::table('cga_consultation_notes', function (Blueprint $table) {
            $table->dropColumn('case_remark');
        });
        Schema::table('cga_consultation_notes', function (Blueprint $table) {
            $table->text('case_remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cross_disciplinaries', function (Blueprint $table) {
            $table->dropColumn('comments');
        });
        Schema::table('cross_disciplinaries', function (Blueprint $table) {
            $table->string('comments')->nullable();
        });
        Schema::table('bzn_consultation_notes', function (Blueprint $table) {
            $table->dropColumn(['case_remark', 'consultation_remark', 'intervention_remark']);
        });
        Schema::table('bzn_consultation_notes', function (Blueprint $table) {
            $table->string('case_remark')->nullable();
            $table->string('consultation_remark')->nullable();
            $table->string('intervention_remark')->nullable();
        });
        Schema::table('cga_consultation_notes', function (Blueprint $table) {
            $table->dropColumn('case_remark');
        });
        Schema::table('cga_consultation_notes', function (Blueprint $table) {
            $table->string('case_remark')->nullable();
        });
    }
};
