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
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->dropColumn([
                'hosp_month',
                'hosp_year',
                'hosp_hosp',
                'hosp_hosp_other',
                'hosp_way',
                'hosp_home',
                'hosp_home_else',
                'hosp_reason'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->string('hosp_month')->nullable();
            $table->string('hosp_year')->nullable();
            $table->enum('hosp_hosp', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->nullable();
            $table->string('hosp_hosp_other')->nullable();
            $table->enum('hosp_way', [1, 2, 3, 4])->nullable();
            $table->enum('hosp_home', [1, 2, 3, 4, 5])->nullable();
            $table->string('hosp_home_else')->nullable();
            $table->enum('hosp_reason', [1, 2, 3, 4, 5])->nullable();
        });
    }
};
