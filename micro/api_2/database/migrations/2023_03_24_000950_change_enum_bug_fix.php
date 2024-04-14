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
                'left_ear', 
                'right_ear', 
                'left_eye', 
                'right_eye', 
                'hearing_aid', 
                'pain_level',
                'forget_med',
                'missing_med',
                'reduce_med',
                'left_med',
                'take_all_med',
                'stop_med',
                'annoyed_by_med',
                'diff_rem_med',
                'pain_semester',
                'health_scales',
                'rest15'
            ]); 
        });
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->smallInteger('left_ear')->unsigned()->nullable();
            $table->smallInteger('right_ear')->unsigned()->nullable();
            $table->smallInteger('left_eye')->unsigned()->nullable();
            $table->smallInteger('right_eye')->unsigned()->nullable();
            $table->smallInteger('hearing_aid')->unsigned()->nullable();
            $table->string('pain_level')->nullable();
            $table->smallInteger('forget_med')->unsigned()->nullable();
            $table->smallInteger('missing_med')->unsigned()->nullable();
            $table->smallInteger('reduce_med')->unsigned()->nullable();
            $table->smallInteger('left_med')->unsigned()->nullable();
            $table->smallInteger('take_all_med')->unsigned()->nullable();
            $table->smallInteger('stop_med')->unsigned()->nullable();
            $table->smallInteger('annoyed_by_med')->unsigned()->nullable();
            $table->string('diff_rem_med')->nullable();
            $table->smallInteger('pain_semester')->unsigned()->nullable();
            $table->string('health_scales')->nullable();
            $table->smallInteger('rest15')->unsigned()->nullable();
        });
        Schema::table('social_worker_forms', function (Blueprint $table) {
           $table->dropColumn([
                'diagnosed_dementia'
            ]); 
        });
        Schema::table('social_worker_forms', function (Blueprint $table) {
           $table->smallInteger('diagnosed_dementia')->unsigned()->nullable(); 
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
                'left_ear', 
                'right_ear', 
                'left_eye', 
                'right_eye', 
                'hearing_aid', 
                'pain_level',
                'forget_med',
                'missing_med',
                'reduce_med',
                'left_med',
                'take_all_med',
                'stop_med',
                'annoyed_by_med',
                'diff_rem_med',
                'pain_semester',
                'health_scales',
                'rest15'
            ]); 
        });
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->enum('left_ear', [0, 1, 2])->nullable();
            $table->enum('right_ear', [0, 1, 2])->nullable();
            $table->enum('left_eye', [0, 1, 2])->nullable();
            $table->enum('right_eye', [0, 1, 2])->nullable();
            $table->enum('hearing_aid', [0, 1])->nullable();
            $table->enum('pain_level', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 'NA'])->nullable(); // string
            $table->enum('forget_med', [0, 1, 2])->nullable();
            $table->enum('missing_med', [0, 1, 2])->nullable();
            $table->enum('reduce_med', [0, 1, 2])->nullable();
            $table->enum('left_med', [0, 1, 2])->nullable();
            $table->enum('take_all_med', [0, 1, 2])->nullable();
            $table->enum('stop_med', [0, 1, 2])->nullable();
            $table->enum('annoyed_by_med', [0, 1, 2])->nullable();
            $table->enum('diff_rem_med', [1.00, 0.75, 0.50, 0.25, 0.00, -1.00])->nullable(); // float
            $table->enum('pain_semester', [0, 1, 2, 3, 4, 5])->nullable();
            $table->enum('health_scales', [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 'other'])->nullable(); // string
            $table->enum('rest15', [0, 1])->nullable();
        });
        Schema::table('social_worker_forms', function (Blueprint $table) {
           $table->dropColumn([
                'diagnosed_dementia'
            ]); 
        });
        Schema::table('social_worker_forms', function (Blueprint $table) {
           $table->enum('diagnosed_dementia', [0, 1])->nullable(); 
        });
    }
};
