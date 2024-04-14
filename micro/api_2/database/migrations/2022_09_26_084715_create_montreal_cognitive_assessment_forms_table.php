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
        Schema::create('montreal_cognitive_assessment_forms', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('assessment_case_id')->unsigned();
            $table->foreign('assessment_case_id')->references('id')->on('assessment_cases');

            $table->string('elderly_central_ref_number')->nullable();
            $table->date('assessment_date')->nullable();
            $table->string('assessor_name')->nullable();
            $table->smallInteger('assessment_kind')->unsigned()->nullable();

            $table->boolean('memory_c11')->nullable();
            $table->boolean('memory_c12')->nullable();
            $table->boolean('memory_c13')->nullable();
            $table->boolean('memory_c14')->nullable();
            $table->boolean('memory_c15')->nullable();
            $table->boolean('memory_c21')->nullable();
            $table->boolean('memory_c22')->nullable();
            $table->boolean('memory_c23')->nullable();
            $table->boolean('memory_c24')->nullable();
            $table->boolean('memory_c25')->nullable();
            $table->decimal('memory_score')->nullable();
            $table->string('language_fluency1')->nullable();
            $table->string('language_fluency2')->nullable();
            $table->string('language_fluency3')->nullable();
            $table->string('language_fluency4')->nullable();
            $table->string('language_fluency5')->nullable();
            $table->string('language_fluency6')->nullable();
            $table->string('language_fluency7')->nullable();
            $table->string('language_fluency8')->nullable();
            $table->string('language_fluency9')->nullable();
            $table->string('language_fluency10')->nullable();
            $table->string('language_fluency11')->nullable();
            $table->string('language_fluency12')->nullable();
            $table->string('language_fluency13')->nullable();
            $table->string('language_fluency14')->nullable();
            $table->string('language_fluency15')->nullable();
            $table->string('language_fluency16')->nullable();
            $table->string('language_fluency17')->nullable();
            $table->string('language_fluency18')->nullable();
            $table->string('language_fluency19')->nullable();
            $table->string('language_fluency20')->nullable();
            $table->decimal('all_words')->nullable();
            $table->decimal('repeat_words')->nullable();
            $table->decimal('non_animal_words')->nullable();
            $table->decimal('language_fluency_score')->nullable();
            $table->smallInteger('orientation_day')->unsigned()->nullable();
            $table->smallInteger('orientation_month')->unsigned()->nullable();
            $table->smallInteger('orientation_year')->unsigned()->nullable();
            $table->smallInteger('orientation_week')->unsigned()->nullable();
            $table->string('orientation_place')->nullable();
            $table->string('orientation_area')->nullable();
            $table->decimal('orientation_score')->nullable();
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
            $table->decimal('delayed_memory_score')->nullable();
            $table->decimal('total_moca_score')->nullable();
            
            $table->json('created_by')->nullable();
            $table->json('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('montreal_cognitive_assessment_forms');
    }
};
