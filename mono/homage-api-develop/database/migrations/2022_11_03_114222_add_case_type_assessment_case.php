<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assessment_cases', function (Blueprint $table) {
            $table->enum('case_type', ['CGA', 'BZN'])->nullable();
        });
    }

    public function down()
    {
        Schema::table('assessment_cases', function (Blueprint $table) {
            $table->dropColumn('case_type');
        });
    }
};
