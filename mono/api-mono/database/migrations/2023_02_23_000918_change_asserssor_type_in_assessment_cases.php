<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assessment_cases', function (Blueprint $table) {
            $table->dropColumn(['first_assessor', 'second_assessor']);
        });
        Schema::table('assessment_cases', function (Blueprint $table) {
            $table->unsignedBigInteger('first_assessor')->nullable();
            $table->unsignedBigInteger('second_assessor')->nullable();
        });
    }

    public function down()
    {
        Schema::table('assessment_cases', function (Blueprint $table) {
            $table->dropColumn(['first_assessor', 'second_assessor']);
        });
        Schema::table('assessment_cases', function (Blueprint $table) {
            $table->string('first_assessor')->nullable();
            $table->string('second_assessor')->nullable();
        });
    }
};
