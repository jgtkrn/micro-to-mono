<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('satisfaction_evaluation_forms', function (Blueprint $table) {
            $table->integer('case_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('satisfaction_evaluation_forms', function (Blueprint $table) {
            $table->dropColumn(['case_id']);
        });
    }
};
