<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('function_mobility_forms', function (Blueprint $table) {
            $table->dropColumn([
                'mobility_single_leg',
                'fall_tug',
                'fall_single_leg',
            ]);
        });

        Schema::table('function_mobility_forms', function (Blueprint $table) {
            $table->boolean('left_single_leg')->nullable();
            $table->boolean('right_single_leg')->nullable();
        });
    }

    public function down()
    {
        Schema::table('function_mobility_forms', function (Blueprint $table) {
            $table->dropColumn([
                'left_single_leg',
                'right_single_leg',
            ]);
        });
        Schema::table('function_mobility_forms', function (Blueprint $table) {
            $table->string('mobility_single_leg')->nullable();
            $table->string('fall_tug')->nullable();
            $table->string('fall_single_leg')->nullable();
        });
    }
};
