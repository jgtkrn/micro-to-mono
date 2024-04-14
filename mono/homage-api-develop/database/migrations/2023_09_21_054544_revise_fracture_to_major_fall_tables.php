<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('function_mobility_forms', function (Blueprint $table) {
            $table->dropColumn(['fracture', 'fracture_text']);
        });
        Schema::table('major_fall_tables', function (Blueprint $table) {
            $table->boolean('fracture')->nullable();
            $table->text('fracture_text')->nullable();
        });
    }

    public function down()
    {
        Schema::table('major_fall_tables', function (Blueprint $table) {
            $table->dropColumn(['fracture', 'fracture_text']);
        });

        Schema::table('function_mobility_forms', function (Blueprint $table) {
            $table->boolean('fracture')->nullable();
            $table->text('fracture_text')->nullable();
        });
    }
};
