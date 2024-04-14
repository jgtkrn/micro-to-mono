<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assessment_case_signatures', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('remarks')->nullable();
        });
    }

    public function down()
    {
        Schema::table('assessment_case_signatures', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('remarks');
        });
    }
};
