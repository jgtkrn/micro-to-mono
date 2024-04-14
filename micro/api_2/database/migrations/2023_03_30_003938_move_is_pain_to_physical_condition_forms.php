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
        Schema::table('pain_site_tables', function (Blueprint $table) {
            $table->dropColumn(['is_pain']);
        });
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            $table->smallInteger('is_pain')->unsigned()->nullable();
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
            $table->dropColumn(['is_pain']);
        });
        Schema::table('pain_site_tables', function (Blueprint $table) {
            $table->boolean('is_pain')->nullable();
        });
    }
};
