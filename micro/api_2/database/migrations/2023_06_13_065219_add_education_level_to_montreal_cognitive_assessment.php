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
        Schema::table('montreal_cognitive_assessment_forms', function (Blueprint $table) {
            $table->string('education_level')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('montreal_cognitive_assessment_forms', function (Blueprint $table) {
            $table->dropColumn('education_level');
        });
    }
};
