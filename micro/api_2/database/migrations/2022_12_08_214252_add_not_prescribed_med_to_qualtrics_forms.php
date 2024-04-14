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
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->dropColumn([
                'om_regular',
                'om_needed',
                'tm_regular',
                'tm_needed'
            ]);
        });

        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->string('not_prescribed_med')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->dropColumn([
                'not_prescribed_med'
            ]);
        });
        
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->boolean('om_regular')->nullable();
            $table->boolean('om_needed')->nullable();
            $table->boolean('tm_regular')->nullable();
            $table->boolean('tm_needed')->nullable();
        });
    }
};
