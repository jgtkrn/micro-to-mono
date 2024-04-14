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
        Schema::table('major_fall_tables', function (Blueprint $table) {
            $table->text('fall_mechanism_other')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('major_fall_tables', function (Blueprint $table) {
            $table->dropColumn('fall_mechanism_other');
        });
    }
};
