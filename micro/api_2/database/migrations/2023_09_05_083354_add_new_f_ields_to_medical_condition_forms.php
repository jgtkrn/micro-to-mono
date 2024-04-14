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
            $table->text('ra_part')->nullable();
            $table->text('fracture_part')->nullable();
            $table->text('arthritis_part')->nullable();
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
            $table->dropColumn([
                'ra_part',
                'fracture_part',
                'arthritis_part'
            ]);
        });
    }
};
