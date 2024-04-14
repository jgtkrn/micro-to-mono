<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->dropColumn([
                'temperature',
                'weight',
                'waistline',
                're_temperature',
                're_weight',
                're_waistline',
            ]);
        });
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->smallInteger('abnormality')->unsigned()->nullable();
            $table->string('other_abnormality')->nullable();
            $table->decimal('temperature')->nullable();
            $table->decimal('weight')->nullable();
            $table->decimal('waistline')->nullable();
            $table->smallInteger('blood_options')->unsigned()->nullable();
            $table->string('blood_text')->nullable();
            $table->string('meal_text')->nullable();
            $table->decimal('re_temperature')->nullable();
            $table->decimal('re_weight')->nullable();
            $table->decimal('re_waistline')->nullable();
            $table->smallInteger('re_blood_options')->unsigned()->nullable();
            $table->string('re_blood_text')->nullable();
            $table->string('re_meal_text')->nullable();
            $table->string('cancer_text')->nullable();
            $table->string('stroke_text')->nullable();
        });
        Schema::table('hospitalization_tables', function (Blueprint $table) {
            $table->dropColumn([
                'hosp_hosp',
            ]);
        });
        Schema::table('hospitalization_tables', function (Blueprint $table) {
            $table->smallInteger('hosp_hosp')->unsigned()->nullable();
        });

    }

    public function down()
    {
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->dropColumn([
                'abnormality',
                'other_abnormality',
                'temperature',
                'weight',
                'waistline',
                're_temperature',
                're_weight',
                're_waistline',
                'blood_options',
                'blood_text',
                'meal_text',
                're_blood_options',
                're_blood_text',
                're_meal_text',
                'cancer_text',
                'stroke_text',
            ]);
        });

        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->integer('temperature')->nullable();
            $table->integer('weight')->nullable();
            $table->integer('waistline')->nullable();
            $table->integer('re_temperature')->nullable();
            $table->integer('re_weight')->nullable();
            $table->integer('re_waistline')->nullable();
        });
        Schema::table('hospitalization_tables', function (Blueprint $table) {
            $table->dropColumn([
                'hosp_hosp',
            ]);
        });
        Schema::table('hospitalization_tables', function (Blueprint $table) {
            $table->enum('hosp_hosp', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->nullable();
        });

    }
};
