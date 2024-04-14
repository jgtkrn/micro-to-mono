<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('qualtrics_forms', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('assessment_case_id')->unsigned();
            $table->foreign('assessment_case_id')->references('id')->on('assessment_cases');

            $table->string('assessor_1')->nullable();
            $table->string('assessor_2')->nullable();

            // Health Professional
            // Chronic Disease History
            $table->boolean('no_chronic')->nullable();
            $table->boolean('is_hypertension')->nullable();
            $table->boolean('is_heart_disease')->nullable();
            $table->boolean('is_diabetes')->nullable();
            $table->boolean('is_high_cholesterol')->nullable();
            $table->boolean('is_copd')->nullable();
            $table->boolean('is_stroke')->nullable();
            $table->boolean('is_dementia')->nullable();
            $table->boolean('is_cancer')->nullable();
            $table->boolean('is_rheumatoid')->nullable();
            $table->boolean('is_osteoporosis')->nullable();
            $table->boolean('is_gout')->nullable();
            $table->boolean('is_depression')->nullable();
            $table->boolean('is_schizophrenia')->nullable();
            $table->boolean('is_enlarged_prostate')->nullable();
            $table->boolean('is_parkinson')->nullable();
            $table->boolean('is_other_disease')->nullable();
            $table->boolean('no_followup')->nullable();
            $table->boolean('is_general_clinic')->nullable();
            $table->boolean('is_internal_medicine')->nullable();
            $table->boolean('is_cardiology')->nullable();
            $table->boolean('is_geriatric')->nullable();
            $table->boolean('is_endocrinology')->nullable();
            $table->boolean('is_gastroenterology')->nullable();
            $table->boolean('is_nephrology')->nullable();
            $table->boolean('is_dep_respiratory')->nullable();
            $table->boolean('is_surgical')->nullable();
            $table->boolean('is_psychiatry')->nullable();
            $table->boolean('is_private_doctor')->nullable();
            $table->boolean('is_oncology')->nullable();
            $table->boolean('is_orthopedics')->nullable();
            $table->boolean('is_urology')->nullable();
            $table->boolean('is_opthalmology')->nullable();
            $table->boolean('is_ent')->nullable();
            $table->boolean('is_other_followup')->nullable();
            $table->boolean('never_surgery')->nullable();
            $table->boolean('is_aj_replace')->nullable();
            $table->boolean('is_cataract')->nullable();
            $table->boolean('is_cholecystectomy')->nullable();
            $table->boolean('is_malignant')->nullable();
            $table->boolean('is_colectomy')->nullable();
            $table->boolean('is_thyroidectomy')->nullable();
            $table->boolean('is_hysterectomy')->nullable();
            $table->boolean('is_thongbo')->nullable();
            $table->boolean('is_pacemaker')->nullable();
            $table->boolean('is_prostatectomy')->nullable();
            $table->boolean('is_other_surgery')->nullable();
            $table->enum('left_ear', [0, 1, 2])->nullable();
            $table->enum('right_ear', [0, 1, 2])->nullable();
            $table->enum('left_eye', [0, 1, 2])->nullable();
            $table->enum('right_eye', [0, 1, 2])->nullable();
            $table->enum('hearing_aid', [0, 1])->nullable();
            $table->enum('walk_aid', [1, 2, 3, 4, 5, 6])->nullable();
            $table->string('other_walk_aid')->nullable();
            $table->enum('amsler_grid', [1, 2, 3, 4, 5])->nullable();

            // Medication
            $table->boolean('om_regular')->nullable();
            $table->string('om_regular_desc')->nullable();
            $table->boolean('om_needed')->nullable();
            $table->string('om_needed_desc')->nullable();
            $table->boolean('tm_regular')->nullable();
            $table->string('tm_regular_desc')->nullable();
            $table->boolean('tm_needed')->nullable();
            $table->string('tm_needed_desc')->nullable();
            $table->enum('forget_med', [0, 1, 2])->nullable();
            $table->enum('missing_med', [0, 1, 2])->nullable();
            $table->enum('reduce_med', [0, 1, 2])->nullable();
            $table->enum('left_med', [0, 1, 2])->nullable();
            $table->enum('take_all_med', [0, 1, 2])->nullable();
            $table->enum('stop_med', [0, 1, 2])->nullable();
            $table->enum('annoyed_by_med', [0, 1, 2])->nullable();
            $table->enum('diff_rem_med', [1.00, 0.75, 0.50, 0.25, 0.00, -1.00])->nullable();

            // Pain
            $table->enum('pain_semester', [0, 1, 2, 3, 4, 5])->nullable();
            $table->string('other_pain_area')->nullable();
            $table->enum('forget_med_p', [0, 1, 2])->nullable();
            $table->enum('missing_med_p', [0, 1, 2])->nullable();
            $table->enum('reduce_med_p', [0, 1, 2])->nullable();
            $table->enum('left_med_p', [0, 1, 2])->nullable();
            $table->enum('take_all_med_p', [0, 1, 2])->nullable();
            $table->enum('stop_med_p', [0, 1, 2])->nullable();
            $table->enum('annoyed_by_med_p', [0, 1, 2])->nullable();
            $table->enum('pain_level', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->nullable();

            // Fall History and Hospitalization
            $table->string('have_fallen')->nullable();
            $table->string('adm_admitted')->nullable();
            $table->string('hosp_month')->nullable();
            $table->string('hosp_year')->nullable();
            $table->enum('hosp_hosp', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->nullable();
            $table->string('hosp_hosp_other')->nullable();
            $table->enum('hosp_way', [1, 2, 3, 4])->nullable();
            $table->enum('hosp_home', [1, 2, 3, 4, 5])->nullable();
            $table->string('hosp_home_else')->nullable();
            $table->enum('hosp_reason', [1, 2, 3, 4, 5])->nullable();

            // Intervention Effectiveness Evaluation
            $table->enum('ife_action', [1, 2, 3, 4, 5, 6])->nullable();
            $table->enum('ife_self_care', [1, 2, 3, 4, 5, 6])->nullable();
            $table->enum('ife_usual_act', [1, 2, 3, 4, 5, 6])->nullable();
            $table->enum('ife_discomfort', [1, 2, 3, 4, 5, 6])->nullable();
            $table->enum('ife_anxiety', [1, 2, 3, 4, 5, 6])->nullable();
            $table->enum('health_scale', [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100])->nullable();

            // Qualtrics Form Physiological Measurement
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

            // Fall Risk
            $table->enum('timedup_test', [1, 2])->nullable();
            $table->string('timedup_test_skip')->nullable();
            $table->enum('timeup_device', [1, 2, 3, 4])->nullable();
            $table->string('timedup_other')->nullable();
            $table->smallInteger('timedup_sec')->unsigned()->nullable();
            $table->enum('timedup_remark', [1, 2, 3, 4])->nullable();
            $table->string('timeup_remark_others')->nullable();
            $table->enum('singlestart_sts', [1, 2])->nullable();
            $table->string('singlestart_skip')->nullable();
            $table->enum('left_sts', [1, 2, 3])->nullable();
            $table->enum('right_sts', [1, 2, 3])->nullable();

            // Qualtrics Remarks
            $table->string('qualtrics_remarks')->nullable();

            $table->json('created_by')->nullable();
            $table->json('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('qualtrics_forms');
    }
};
