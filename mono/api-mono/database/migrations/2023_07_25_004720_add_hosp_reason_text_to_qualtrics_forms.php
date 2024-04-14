<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('hospitalization_tables', function (Blueprint $table) {
            $table->string('hosp_reason_other')->nullable();
        });
    }

    public function down()
    {
        Schema::table('hospitalization_tables', function (Blueprint $table) {
            $table->dropColumn('hosp_reason_other');
        });
    }
};
