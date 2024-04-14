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
                'forget_med_p',
                'missing_med_p',
                'reduce_med_p',
                'left_med_p',
                'take_all_med_p',
                'stop_med_p',
                'annoyed_by_med_p',
                'pain_level'
            ]);
        });
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->enum('pain_level', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 'NA'])->nullable();
            $table->string('pain_level_text')->nullable();
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
                'pain_level',
                'pain_level_text'
            ]);
        });

        Schema::table('qualtrics_forms', function (Blueprint $table) {
            // Pain
            $table->enum('forget_med_p', [0, 1, 2])->nullable();
            $table->enum('missing_med_p', [0, 1, 2])->nullable();
            $table->enum('reduce_med_p', [0, 1, 2])->nullable();
            $table->enum('left_med_p', [0, 1, 2])->nullable();
            $table->enum('take_all_med_p', [0, 1, 2])->nullable();
            $table->enum('stop_med_p', [0, 1, 2])->nullable();
            $table->enum('annoyed_by_med_p', [0, 1, 2])->nullable();
            $table->enum('pain_level', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->nullable();
        });
    }
};
