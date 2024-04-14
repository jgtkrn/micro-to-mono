<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('medication_adherence_forms', function (Blueprint $table) {
            $table->string('elderly_central_ref_number')->nullable();
            $table->date('assessment_date')->nullable();
            $table->string('assessor_name')->nullable();
            $table->smallInteger('assessment_kind')->unsigned()->nullable();
        });
    }

    public function down()
    {
        Schema::table('medication_adherence_forms', function (Blueprint $table) {
            $table->dropColumn('elderly_central_ref_number');
            $table->dropColumn('assessment_date');
            $table->dropColumn('assessor_name');
            $table->dropColumn('assessment_kind');
        });
    }
};
