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
        Schema::table('elder_calls', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by']);
        });

        Schema::table('elder_calls', function (Blueprint $table) {
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->string('created_by_name')->nullable();
            $table->string('updated_by_name')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('elder_calls', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by', 'created_by_name', 'updated_by_name']);
        });

        Schema::table('elder_calls', function (Blueprint $table) {
            $table->longText('created_by')->nullable();
            $table->longText('updated_by')->nullable();
        });

    }
};
