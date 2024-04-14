<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('medical_condition_forms', function (Blueprint $table) {
            $table->dropColumn(['has_allergy', 'allergy_description']);
        });
        Schema::table('medical_condition_forms', function (Blueprint $table) {
            $table->boolean('has_food_allergy')->nullable();
            $table->string('food_allergy_description')->nullable();
            $table->boolean('has_drug_allergy')->nullable();
            $table->string('drug_allergy_description')->nullable();
        });
    }

    public function down()
    {
        Schema::table('medical_condition_forms', function (Blueprint $table) {
            $table->dropColumn(['has_food_allergy', 'food_allergy_description', 'has_drug_allergy', 'drug_allergy_description']);
        });
        Schema::table('medical_condition_forms', function (Blueprint $table) {
            $table->boolean('has_allergy')->nullable();
            $table->string('allergy_description')->nullable();
        });
    }
};
