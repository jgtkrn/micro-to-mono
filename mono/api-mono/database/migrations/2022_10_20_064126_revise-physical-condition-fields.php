<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            $table->string('pain_remark')->nullable();
            $table->string('special_diet')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            $table->dropColumn([
                'pain_remark',
            ]);
        });
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            $table->smallInteger('special_diet')->unsigned()->nullable()->change();
        });
    }
};
