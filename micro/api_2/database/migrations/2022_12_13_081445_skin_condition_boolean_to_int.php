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
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            $table->smallInteger('odor')->nullable()->unsigned()->change();
            $table->smallInteger('pain')->nullable()->unsigned()->change();
            $table->smallInteger('is_special_feeding')->nullable()->unsigned()->change();
        });
        Schema::table('pain_site_tables', function (Blueprint $table) {
            $table->smallInteger('affect_adl')->nullable()->unsigned()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            $table->boolean('odor')->nullable()->change();
            $table->boolean('pain')->nullable()->change();
            $table->boolean('is_special_feeding')->nullable()->change();
        });
        Schema::table('pain_site_tables', function (Blueprint $table) {
            $table->boolean('affect_adl')->nullable()->change();
        });
    }
};
