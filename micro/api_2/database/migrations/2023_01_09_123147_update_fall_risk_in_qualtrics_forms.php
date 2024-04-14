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
            $table->dropColumn(['timedup_remark']);
        });
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->string('timedup_sec_desc')->nullable();
            $table->boolean('tr_none')->nullable();
            $table->boolean('tr_stopped')->nullable();
            $table->boolean('tr_impaired')->nullable();
            $table->boolean('tr_others')->nullable();
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
            $table->dropColumn([
                'timedup_sec_desc',
                'tr_none',
                'tr_stopped',
                'tr_impaired',
                'tr_others'
            ]);
        });
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->smallInteger('timedup_remark')->unsigned()->nullable();
        });


    }
};
