<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->string('fallrisk_fa')->nullable();
            $table->string('fallrisk_rs')->nullable();
            $table->string('hosp_fa')->nullable();
            $table->string('hosp_rs')->nullable();
            $table->string('remark_fa')->nullable();
            $table->string('remark_rs')->nullable();
        });
    }

    public function down()
    {
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->dropColumn([
                'fallrisk_fa',
                'fallrisk_rs',
                'hosp_fa',
                'hosp_rs',
                'remark_fa',
                'remark_rs',
            ]);
        });
    }
};
