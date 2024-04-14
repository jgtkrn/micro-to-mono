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
        Schema::table('medical_condition_forms', function (Blueprint $table) {
            $table->string('other_complaint')->nullable();
            $table->string('other_medical_history')->nullable();
            $table->string('premorbid_condition')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medical_condition_forms', function (Blueprint $table) {
            $table->dropColumn([
                'other_complaint',
                'other_medical_history',
                'premorbid_condition'
            ]);
        });
    }
};
