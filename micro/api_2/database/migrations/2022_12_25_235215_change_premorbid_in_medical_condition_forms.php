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
            $table->dropColumn(['premorbid']);
        });
        Schema::table('medical_condition_forms', function (Blueprint $table) {
            $table->smallInteger('premorbid')->unsigned()->nullable();
            $table->smallInteger('premorbid_start_month')->unsigned()->nullable();
            $table->smallInteger('premorbid_start_year')->unsigned()->nullable();
            $table->smallInteger('premorbid_end_month')->unsigned()->nullable();
            $table->smallInteger('premorbid_end_year')->unsigned()->nullable();
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
                'premorbid',
                'premorbid_start_month',
                'premorbid_start_year',
                'premorbid_end_month',
                'premorbid_end_year'
            ]);
        });
        Schema::table('medical_condition_forms', function (Blueprint $table) {
            $table->string('premorbid')->nullable();
        });

    }
};
