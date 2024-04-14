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
        Schema::table('qualtrics_forms', function (Blueprint $table){
            $table->dropColumn([
                'rest15', 
                'eathour',
                'body_temperature1',
                'body_temperature2',
                'sit_upward1',
                'sit_upward2',
                'sit_depression1',
                'sit_depression2',
                'sta_upward1',
                'sta_upward2',
                'sta_depression1',
                'sta_depression2',
                'blood_ox1',
                'blood_ox2',
                'heartbeat1',
                'heartbeat2',
                'blood_glucose1',
                'blood_glucose2',
                'phy_kardia',
                'phy_waist',
                'phy_weight',
                'phy_height',
            ]);

        });

        Schema::table('qualtrics_forms', function (Blueprint $table){
            $table->smallInteger('rest15')->unsigned()->nullable();
            $table->smallInteger('eathour')->unsigned()->nullable();

            // Physiological Measurement
            $table->integer('temperature')->nullable();
            $table->integer('sitting_sbp')->nullable();
            $table->integer('sitting_dbp')->nullable();
            $table->integer('standing_sbp')->nullable();
            $table->integer('standing_dbp')->nullable();
            $table->integer('blood_oxygen')->nullable();
            $table->integer('heart_rate')->nullable();
            $table->smallInteger('heart_rythm')->unsigned()->nullable();
            $table->smallInteger('kardia')->unsigned()->nullable();
            $table->decimal('blood_sugar')->nullable();
            $table->smallInteger('blood_sugar_time')->unsigned()->nullable();
            $table->integer('waistline')->nullable();
            $table->integer('weight')->nullable();
            $table->decimal('height')->nullable();
            $table->integer('respiratory_rate')->nullable();

            // Re-Physiological Measurement
            $table->integer('re_temperature')->nullable();
            $table->integer('re_sitting_sbp')->nullable();
            $table->integer('re_sitting_dbp')->nullable();
            $table->integer('re_standing_sbp')->nullable();
            $table->integer('re_standing_dbp')->nullable();
            $table->integer('re_blood_oxygen')->nullable();
            $table->integer('re_heart_rate')->nullable();
            $table->smallInteger('re_heart_rythm')->unsigned()->nullable();
            $table->smallInteger('re_kardia')->unsigned()->nullable();
            $table->decimal('re_blood_sugar')->nullable();
            $table->smallInteger('re_blood_sugar_time')->unsigned()->nullable();
            $table->integer('re_waistline')->nullable();
            $table->integer('re_weight')->nullable();
            $table->decimal('re_height')->nullable();
            $table->integer('re_respiratory_rate')->nullable();

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
                'rest15',
                'eathour',

                // Physiological Measurement
                'temperature',
                'sitting_sbp',
                'sitting_dbp',
                'standing_sbp',
                'standing_dbp',
                'blood_oxygen',
                'heart_rate',
                'heart_rythm',
                'kardia',
                'blood_sugar',
                'blood_sugar_time',
                'waistline',
                'weight',
                'height',
                'respiratory_rate',
                
                // Re-Physiological Measurement
                're_temperature',
                're_sitting_sbp',
                're_sitting_dbp',
                're_standing_sbp',
                're_standing_dbp',
                're_blood_oxygen',
                're_heart_rate',
                're_heart_rythm',
                're_kardia',
                're_blood_sugar',
                're_blood_sugar_time',
                're_waistline',
                're_weight',
                're_height',
                're_respiratory_rate',

            ]);
        });

        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->enum('rest15', [0, 1])->nullable();
            $table->enum('eathour', [1, 2, 3])->nullable();
            $table->decimal('body_temperature1')->nullable();
            $table->decimal('body_temperature2')->nullable();
            $table->decimal('sit_upward1')->nullable(); 
            $table->decimal('sit_upward2')->nullable();
            $table->decimal('sit_depression1')->nullable();
            $table->decimal('sit_depression2')->nullable();
            $table->decimal('sta_upward1')->nullable();
            $table->decimal('sta_upward2')->nullable();
            $table->decimal('sta_depression1')->nullable();
            $table->decimal('sta_depression2')->nullable();
            $table->decimal('blood_ox1')->nullable();
            $table->decimal('blood_ox2')->nullable();
            $table->decimal('heartbeat1')->nullable();
            $table->decimal('heartbeat2')->nullable();
            $table->decimal('blood_glucose1')->nullable();
            $table->decimal('blood_glucose2')->nullable();
            $table->enum('phy_kardia', [1, 2, 3, 4, 5])->nullable();
            $table->decimal('phy_waist')->nullable();
            $table->decimal('phy_weight')->nullable();
            $table->decimal('phy_height')->nullable();
        });

    }
};
