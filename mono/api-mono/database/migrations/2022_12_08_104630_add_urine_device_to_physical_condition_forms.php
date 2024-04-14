<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            $table->smallInteger('urine_device')->unsigned()->nullable();
        });
    }

    public function down()
    {
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            $table->dropColumn(['urine_device']);
        });
    }
};
