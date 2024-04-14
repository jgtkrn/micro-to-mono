<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('care_plans', function (Blueprint $table) {
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->unsignedBigInteger('handler_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('care_plans', function (Blueprint $table) {
            $table->dropColumn(['manager_id', 'handler_id']);
        });
    }
};
