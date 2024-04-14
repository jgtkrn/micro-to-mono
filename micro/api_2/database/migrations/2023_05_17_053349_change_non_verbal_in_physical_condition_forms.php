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
            $table->dropColumn('non_verbal');
        });
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            $table->boolean('no_vision')->nullable();
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
            $table->dropColumn('no_vision');
        });
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            $table->boolean('non_verbal')->nullable();
        });
    }
};
