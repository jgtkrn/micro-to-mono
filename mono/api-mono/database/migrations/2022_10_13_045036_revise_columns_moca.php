<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('montreal_cognitive_assessment_forms', function (Blueprint $table) {
            $table->dropColumn([
                'recall_words_1',
                'category_hints_1',
                'multiple_choice_1',
                'recall_words_2',
                'category_hints_2',
                'multiple_choice_2',
                'recall_words_3',
                'category_hints_3',
                'multiple_choice_3',
                'recall_words_4',
                'category_hints_4',
                'multiple_choice_4',
                'recall_words_5',
                'category_hints_5',
                'multiple_choice_5',
            ]);
        });

        Schema::table('montreal_cognitive_assessment_forms', function (Blueprint $table) {
            $table->enum('face_word', [1, 2, 3])->nullable();
            $table->enum('velvet_word', [1, 2, 3])->nullable();
            $table->enum('church_word', [1, 2, 3])->nullable();
            $table->enum('daisy_word', [1, 2, 3])->nullable();
            $table->enum('red_word', [1, 2, 3])->nullable();
            $table->enum('category_percentile', [1, 2, 3, 4])->nullable();

        });

    }

    public function down()
    {
        Schema::table('montreal_cognitive_assessment_forms', function (Blueprint $table) {
            $table->dropColumn([
                'face_word',
                'velvet_word',
                'church_word',
                'daisy_word',
                'red_word',
                'category_percentile',
            ]);
        });
        Schema::table('montreal_cognitive_assessment_forms', function (Blueprint $table) {
            $table->boolean('recall_words_1')->nullable();
            $table->boolean('category_hints_1')->nullable();
            $table->boolean('multiple_choice_1')->nullable();
            $table->boolean('recall_words_2')->nullable();
            $table->boolean('category_hints_2')->nullable();
            $table->boolean('multiple_choice_2')->nullable();
            $table->boolean('recall_words_3')->nullable();
            $table->boolean('category_hints_3')->nullable();
            $table->boolean('multiple_choice_3')->nullable();
            $table->boolean('recall_words_4')->nullable();
            $table->boolean('category_hints_4')->nullable();
            $table->boolean('multiple_choice_4')->nullable();
            $table->boolean('recall_words_5')->nullable();
            $table->boolean('category_hints_5')->nullable();
            $table->boolean('multiple_choice_5')->nullable();
        });
    }
};
