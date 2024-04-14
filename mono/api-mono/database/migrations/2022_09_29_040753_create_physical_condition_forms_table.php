<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('physical_condition_forms', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('assessment_case_id')->unsigned();
            $table->foreign('assessment_case_id')->references('id')->on('assessment_cases');

            // General Condition
            $table->smallInteger('general_condition')->unsigned()->nullable();
            $table->smallInteger('eye_opening_response')->unsigned()->nullable();
            $table->smallInteger('verbal_response')->unsigned()->nullable();
            $table->smallInteger('motor_response')->unsigned()->nullable();
            $table->integer('glasgow_score')->nullable();

            // Mental State
            $table->smallInteger('mental_state')->unsigned()->nullable();
            $table->smallInteger('edu_percentile')->unsigned()->nullable();
            $table->integer('moca_score')->nullable();

            // Emotional State
            $table->smallInteger('emotional_state')->unsigned()->nullable();
            $table->integer('geriatric_score')->nullable();

            // Sensory
            $table->boolean('is_good')->nullable();
            $table->boolean('is_deaf')->nullable();
            $table->boolean('dumb_left')->nullable();
            $table->boolean('dumb_right')->nullable();
            $table->boolean('is_visual_impaired')->nullable();
            $table->boolean('blind_left')->nullable();
            $table->boolean('blind_right')->nullable();
            $table->boolean('is_assistive_devices')->nullable();
            $table->boolean('denture')->nullable();
            $table->boolean('hearing_aid')->nullable();
            $table->boolean('glasses')->nullable();

            // Pain
            $table->boolean('is_pain')->nullable();
            $table->string('provoking_factor')->nullable();
            $table->string('pain_location1')->nullable();
            $table->boolean('is_dull')->nullable();
            $table->boolean('is_achy')->nullable();
            $table->boolean('is_sharp')->nullable();
            $table->boolean('is_stabbing')->nullable();
            $table->enum('stabbing_option', ['constant', 'intermittent'])->nullable();
            $table->string('pain_location2')->nullable();
            $table->boolean('is_relief')->nullable();
            $table->string('what_relief')->nullable();
            $table->boolean('have_relief_method')->nullable();
            $table->smallInteger('relief_method')->unsigned()->nullable();
            $table->string('other_relief_method')->nullable();
            $table->smallInteger('pain_scale')->unsigned()->nullable();
            $table->string('when_pain')->nullable();
            $table->boolean('affect_adl')->nullable();
            $table->string('adl_info')->nullable();

            // Nutrition
            $table->boolean('dat')->nullable();
            $table->boolean('is_special_diet')->nullable();
            $table->smallInteger('special_diet')->unsigned()->nullable();
            $table->boolean('is_special_feeding')->nullable();
            $table->smallInteger('special_feeding')->unsigned()->nullable();
            $table->string('thickener_formula')->nullable();
            $table->string('fluid_restriction')->nullable();
            $table->string('tube_next_change')->nullable();
            $table->string('milk_formula')->nullable();
            $table->string('milk_regime')->nullable();
            $table->smallInteger('feeding_person')->unsigned()->nullable();
            $table->smallInteger('feeding_technique')->unsigned()->nullable();

            // Skin Condition
            $table->boolean('is_intact')->nullable();
            $table->boolean('is_abnormal_skin')->nullable();
            $table->boolean('is_napkin_associated')->nullable();
            $table->boolean('is_dry')->nullable();
            $table->boolean('is_cellulitis')->nullable();
            $table->string('cellulitis_desc')->nullable();
            $table->boolean('is_eczema')->nullable();
            $table->string('eczema_desc')->nullable();
            $table->boolean('is_scalp')->nullable();
            $table->string('scalp_desc')->nullable();
            $table->boolean('is_itchy')->nullable();
            $table->string('itchy_desc')->nullable();
            $table->boolean('is_wound')->nullable();
            $table->string('wound_desc')->nullable();
            $table->decimal('wound_size')->nullable();
            $table->string('tunneling_time')->nullable();
            $table->decimal('wound_bed')->nullable();
            $table->decimal('granulating_tissue')->nullable();
            $table->decimal('necrotic_tissue')->nullable();
            $table->decimal('sloughy_tissue')->nullable();
            $table->decimal('other_tissue')->nullable();
            $table->smallInteger('exudate_amount')->unsigned()->nullable();
            $table->smallInteger('exudate_type')->unsigned()->nullable();
            $table->string('other_exudate')->nullable();
            $table->smallInteger('surrounding_skin')->unsigned()->nullable();
            $table->string('other_surrounding')->nullable();
            $table->boolean('odor')->nullable();
            $table->boolean('pain')->nullable();

            // Elimination
            $table->boolean('bowel_normal')->nullable();
            $table->boolean('bowel_abnormal')->nullable();
            $table->smallInteger('abnormal_option')->unsigned()->nullable();
            $table->smallInteger('fi_bowel')->unsigned()->nullable();
            $table->boolean('urine_normal')->nullable();
            $table->boolean('urine_abnormal')->nullable();
            $table->boolean('urine_incontinence')->nullable();
            $table->smallInteger('fi_urine')->unsigned()->nullable();
            $table->smallInteger('catheter_type')->unsigned()->nullable();
            $table->string('catheter_next_change')->nullable();
            $table->integer('catheter_size_fr')->nullable();

            $table->json('created_by')->nullable();
            $table->json('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('physical_condition_forms');
    }
};
