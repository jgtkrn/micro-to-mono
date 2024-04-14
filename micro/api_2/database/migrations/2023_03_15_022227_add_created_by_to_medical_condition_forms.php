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
        Schema::table('medical_condition_forms', function (Blueprint $table) {
           $table->dropColumn(['created_by', 'updated_by']); 
        });
        Schema::table('medical_condition_forms', function (Blueprint $table) {
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
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
        Schema::table('medical_condition_forms', function (Blueprint $table) {
           $table->dropColumn(['created_by', 'updated_by', 'created_by_name', 'updated_by_name']); 
        });
        Schema::table('medical_condition_forms', function (Blueprint $table) {
            $table->json('created_by')->nullable();
            $table->json('updated_by')->nullable();
        });
    }
};
