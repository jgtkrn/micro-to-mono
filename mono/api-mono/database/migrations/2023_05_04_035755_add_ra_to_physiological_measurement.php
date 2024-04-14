<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('physiological_measurement_forms', function (Blueprint $table) {
            $table->dropColumn([
                'temperature',
                'weight',
                'waistline',
            ]);
        });
        Schema::table('physiological_measurement_forms', function (Blueprint $table) {
            $table->decimal('temperature')->nullable();
            $table->decimal('weight')->nullable();
            $table->decimal('waistline')->nullable();
            $table->smallInteger('blood_options')->unsigned()->nullable();
            $table->string('blood_text')->nullable();
            $table->string('meal_text')->nullable();
        });
        Schema::table('re_physiological_measurement_forms', function (Blueprint $table) {
            $table->dropColumn([
                're_temperature',
                're_weight',
                're_waistline',
            ]);
        });
        Schema::table('re_physiological_measurement_forms', function (Blueprint $table) {
            $table->decimal('re_temperature')->nullable();
            $table->decimal('re_weight')->nullable();
            $table->decimal('re_waistline')->nullable();
            $table->smallInteger('re_blood_options')->unsigned()->nullable();
            $table->string('re_blood_text')->nullable();
            $table->string('re_meal_text')->nullable();
        });
    }

    public function down()
    {
        Schema::table('physiological_measurement_forms', function (Blueprint $table) {
            $table->dropColumn([
                'temperature',
                'weight',
                'waistline',
                'blood_options',
                'blood_text',
                'meal_text',
            ]);
        });
        Schema::table('physiological_measurement_forms', function (Blueprint $table) {
            $table->integer('temperature')->nullable();
            $table->integer('weight')->nullable();
            $table->integer('waistline')->nullable();
        });
        Schema::table('re_physiological_measurement_forms', function (Blueprint $table) {
            $table->dropColumn([
                're_temperature',
                're_weight',
                're_waistline',
                're_blood_options',
                're_blood_text',
                're_meal_text',
            ]);
        });
        Schema::table('re_physiological_measurement_forms', function (Blueprint $table) {
            $table->integer('re_temperature')->nullable();
            $table->integer('re_weight')->nullable();
            $table->integer('re_waistline')->nullable();
        });
    }
};
