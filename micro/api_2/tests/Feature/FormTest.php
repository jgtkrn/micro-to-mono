<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\AssessmentCase;
use App\Models\MedicalConditionForm;
use App\Models\MedicationAdherenceForm;
use App\Models\PhysiologicalMeasurementForm;
use App\Models\RePhysiologicalMeasurementForm;
use App\Models\LubbenSocialNetworkScaleForm;
use App\Models\SocialBackgroundForm;
use App\Models\FunctionMobilityForm;
use App\Models\MajorFallTable;
use App\Models\BarthelIndexForm;
use App\Models\AssessmentCaseStatus;
use App\Models\GeriatricDepressionScaleForm;
use App\Models\IadlForm;
use App\Models\MontrealCognitiveAssessmentForm;
use App\Models\GenogramForm;
use App\Models\PhysicalConditionForm;
use App\Models\QualtricsForm;
use App\Models\SocialWorkerForm;
use App\Models\LivingStatusTable;
use App\Models\PainSiteTable;
use App\Models\HospitalizationTables;
use App\Models\CommunityResourceTable;
use App\Models\DoReferralTables;
use App\Models\HomeFall;
use App\Models\HomeHygiene;
use App\Models\HomeService;
use App\Models\LifeSupport;
use App\Models\ElderLiving;

class FormTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

    //common test
    public function test_put_form_failed_assessment_case_not_found()
    {
        $data = [
            'user_role' => 'manager',
            'is_bzn' => true,
            'is_cga' => true,
        ];

        $response = $this->putJson("assessments-api/v1/assessment-case-forms/"
            . "100?form_name=physiological_measurement", $data);

        $response->assertNotFound();
    }

    public function test_get_form_success_null()
    {
        $assessment_case = AssessmentCase::factory()->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=physiological_measurement");

        $response->assertOk();
        $response->assertJsonPath('data', null);
    }

    public function test_get_form_failed_invalid_form_name()
    {
        $assessment_case = AssessmentCase::factory()->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=invalid_form_name");

        $response->assertUnprocessable();
    }

    public function test_get_form_failed_not_found()
    {
        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "100?form_name=physiological_measurement");

        $response->assertNotFound();
    }

    // Individual Forms

    // Physicological Measurement Form
    public function test_put_form_physiological_measurement_success()
    {
        $assessment_case = AssessmentCase::factory()->create();

        $data = [
            'is_cga' => true,
            'is_bzn' => true,
            'temperature' => $this->faker->randomFloat(2,25,48), //decimal
            'sitting_sbp' => $this->faker->numberBetween(10,20),
            'sitting_dbp' => $this->faker->numberBetween(10,20),
            'standing_sbp' => $this->faker->numberBetween(10,20),
            'standing_dbp' => $this->faker->numberBetween(10,20),
            'blood_oxygen' => $this->faker->numberBetween(1,2),
            'heart_rate' => $this->faker->numberBetween(1,2),
            'heart_rythm' => $this->faker->numberBetween(1,2),
            'kardia' => $this->faker->numberBetween(1,2),
            'blood_sugar' => $this->faker->randomFloat(2,10,20), //decimal
            'blood_sugar_time' => $this->faker->numberBetween(1,2),
            'waistline' => $this->faker->randomFloat(2,20,250), //decimal
            'weight' => $this->faker->randomFloat(2,10,200), //decimal
            'height' => $this->faker->randomFloat(2,2,3), //decimal
            'respiratory_rate' => $this->faker->numberBetween(1, 2),
            'blood_options' => $this->faker->numberBetween(1,2),
            'blood_text' => $this->faker->word,
            'meal_text' => $this->faker->word
        ];

        $response = $this->putJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=physiological_measurement", $data);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.temperature', sprintf('%0.2f', $data['temperature']))
                ->where('data.sitting_sbp', $data['sitting_sbp'])
                ->where('data.sitting_dbp', $data['sitting_dbp'])
                ->where('data.standing_sbp', $data['standing_sbp'])
                ->where('data.standing_dbp', $data['standing_dbp'])
                ->where('data.blood_oxygen', $data['blood_oxygen'])
                ->where('data.heart_rate', $data['heart_rate'])
                ->where('data.kardia', $data['kardia'])
                ->where('data.blood_sugar', sprintf('%0.2f', $data['blood_sugar']))
                ->where('data.blood_sugar_time', $data['blood_sugar_time'])
                ->where('data.waistline', sprintf('%0.2f', $data['waistline']))
                ->where('data.weight', sprintf('%0.2f', $data['weight']))
                ->where('data.height', sprintf('%0.2f', $data['height']))
                ->where('data.respiratory_rate', $data['respiratory_rate'])
                ->where('data.blood_options', $data['blood_options'])
                ->where('data.blood_text', $data['blood_text'])
                ->where('data.meal_text', $data['meal_text'])
        );
    }

    public function test_get_form_physiological_measurement_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $form = PhysiologicalMeasurementForm::factory()
            ->for($assessment_case)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=physiological_measurement");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.temperature', sprintf('%0.2f', $form->temperature))
                ->where('data.sitting_sbp', $form->sitting_sbp)
                ->where('data.sitting_dbp', $form->sitting_dbp)
                ->where('data.standing_sbp', $form->standing_sbp)
                ->where('data.standing_dbp', $form->standing_dbp)
                ->where('data.blood_oxygen', $form->blood_oxygen)
                ->where('data.heart_rate', $form->heart_rate)
                ->where('data.kardia', $form->kardia)
                ->where('data.blood_sugar', sprintf('%0.2f', $form->blood_sugar))
                ->where('data.blood_sugar_time', $form->blood_sugar_time)
                ->where('data.waistline', sprintf('%0.2f', $form->waistline))
                ->where('data.weight', sprintf('%0.2f', $form->weight))
                ->where('data.height', sprintf('%0.2f', $form->height))
                ->where('data.respiratory_rate', $form->respiratory_rate)
                ->where('data.blood_options', $form->blood_options)
                ->where('data.blood_text', $form->blood_text)
                ->where('data.meal_text', $form->meal_text)
        );
    }

    // Re Physicological Measurement Form
    public function test_put_form_re_physiological_measurement_success()
    {
        $assessment_case = AssessmentCase::factory()->create();

        $data = [
            'is_cga' => true,
            'is_bzn' => true,

            're_temperature' => $this->faker->randomFloat(2,25,48), //decimal
            're_sitting_sbp' => $this->faker->numberBetween(10,20),
            're_sitting_dbp' => $this->faker->numberBetween(10,20),
            're_standing_sbp' => $this->faker->numberBetween(10,20),
            're_standing_dbp' => $this->faker->numberBetween(10,20),
            're_blood_oxygen' => $this->faker->numberBetween(1,2),
            're_heart_rate' => $this->faker->numberBetween(1,2),
            're_heart_rythm' => $this->faker->numberBetween(1,2),
            're_kardia' => $this->faker->numberBetween(1,2),
            're_blood_sugar' => $this->faker->randomFloat(2,10,20), //decimal
            're_blood_sugar_time' => $this->faker->numberBetween(1,2),
            're_waistline' => $this->faker->randomFloat(2,20,250), //decimal
            're_weight' => $this->faker->randomFloat(2,10,200), //decimal
            're_height' => $this->faker->randomFloat(2,2,3), //decimal
            're_respiratory_rate' => $this->faker->numberBetween(1, 2),
            're_blood_options' => $this->faker->numberBetween(1,2),
            're_blood_text' => $this->faker->word,
            're_meal_text' => $this->faker->word
        ];

        $response = $this->putJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=re_physiological_measurement", $data);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.re_temperature', sprintf('%0.2f', $data['re_temperature']))
                ->where('data.re_sitting_sbp', $data['re_sitting_sbp'])
                ->where('data.re_sitting_dbp', $data['re_sitting_dbp'])
                ->where('data.re_standing_sbp', $data['re_standing_sbp'])
                ->where('data.re_standing_dbp', $data['re_standing_dbp'])
                ->where('data.re_blood_oxygen', $data['re_blood_oxygen'])
                ->where('data.re_heart_rate', $data['re_heart_rate'])
                ->where('data.re_kardia', $data['re_kardia'])
                ->where('data.re_blood_sugar', sprintf('%0.2f', $data['re_blood_sugar']))
                ->where('data.re_blood_sugar_time', $data['re_blood_sugar_time'])
                ->where('data.re_waistline', sprintf('%0.2f', $data['re_waistline']))
                ->where('data.re_weight', sprintf('%0.2f', $data['re_weight']))
                ->where('data.re_height', sprintf('%0.2f', $data['re_height']))
                ->where('data.re_respiratory_rate', $data['re_respiratory_rate'])
                ->where('data.re_blood_options', $data['re_blood_options'])
                ->where('data.re_blood_text', $data['re_blood_text'])
                ->where('data.re_meal_text', $data['re_meal_text'])
        );
    }

    public function test_get_form_re_physiological_measurement_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $form = RePhysiologicalMeasurementForm::factory()
            ->for($assessment_case)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=re_physiological_measurement");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.re_temperature', sprintf('%0.2f', $form->re_temperature))
                ->where('data.re_sitting_sbp', $form->re_sitting_sbp)
                ->where('data.re_sitting_dbp', $form->re_sitting_dbp)
                ->where('data.re_standing_sbp', $form->re_standing_sbp)
                ->where('data.re_standing_dbp', $form->re_standing_dbp)
                ->where('data.re_blood_oxygen', $form->re_blood_oxygen)
                ->where('data.re_heart_rate', $form->re_heart_rate)
                ->where('data.re_kardia', $form->re_kardia)
                ->where('data.re_blood_sugar', sprintf('%0.2f', $form->re_blood_sugar))
                ->where('data.re_blood_sugar_time', $form->re_blood_sugar_time)
                ->where('data.re_waistline', sprintf('%0.2f', $form->re_waistline))
                ->where('data.re_weight', sprintf('%0.2f', $form->re_weight))
                ->where('data.re_height', sprintf('%0.2f', $form->re_height))
                ->where('data.re_respiratory_rate', $form->re_respiratory_rate)
                ->where('data.re_blood_options', $form->re_blood_options)
                ->where('data.re_blood_text', $form->re_blood_text)
                ->where('data.re_meal_text', $form->re_meal_text)
        );
    }

    // Medical Condition Form
    // public function test_put_form_medical_condition_success()
    // {
    //     $assessment_case = AssessmentCase::factory()->create();
    //     MedicationAdherenceForm::factory()->create([
    //         'assessment_case_id' => $assessment_case->id
    //     ]);

    //         'has_medical_history' => $this->faker->boolean,
    //         'premorbid' => $this->faker->text,
    //         'premorbid_start_month' => $this->faker->numberBetween(1,2),
    //         'premorbid_start_year' => $this->faker->numberBetween([2000-2012]),
    //         'premorbid_end_month' => $this->faker->numberBetween(1,2),
    //         'premorbid_end_year' => $this->faker->numberBetween([2000-2012]),
    //         'followup_appointment' => $this->faker->text,
    //         'has_medication' => $this->faker->boolean,
    //         'medication_description' => $this->faker->text,
    //         'has_food_allergy' => $this->faker->boolean,
    //         'food_allergy_description' => $this->faker->text,
    //         'has_drug_allergy' => $this->faker->boolean,
    //         'drug_allergy_description' => $this->faker->text,
    //     ];

    //     $response = $this->putJson("assessments-api/v1/assessment-case-forms/"
    //         . "{$assessment_case->id}?form_name=medical_condition", $data);

    //     $response->assertOk();
    //     $response->assertJson(
    //         fn (AssertableJson $json) => $json
    //             ->where('data.medical_condition.id', $assessment_case->id)
    //             ->where('data.medical_condition.has_medical_history', $data['has_medical_history'])
    //             ->where('data.medical_condition.premorbid', $data['premorbid'])
    //             ->where('data.medical_condition.followup_appointment', $data['followup_appointment'])
    //             ->where('data.medical_condition.has_food_allergy', $data['has_food_allergy'])
    //             ->where('data.medical_condition.food_allergy_description', $data['food_allergy_description'])
    //             ->where('data.medical_condition.has_drug_allergy', $data['has_drug_allergy'])
    //             ->where('data.medical_condition.drug_allergy_description', $data['drug_allergy_description'])
    //             ->where('data.medical_condition.has_medication', $data['has_medication'])
    //             ->where('data.medical_condition.medication_description', $data['medication_description'])
    //     );
    // }

    public function test_get_form_medical_condition_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $form = MedicalConditionForm::factory()
            ->for($assessment_case)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=medical_condition");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.medical_condition.id', $assessment_case->id)
                ->where('data.medical_condition.has_medical_history', $form->has_medical_history)
                ->where('data.medical_condition.premorbid', $form->premorbid)
                ->where('data.medical_condition.followup_appointment', $form->followup_appointment)
                ->where('data.medical_condition.has_food_allergy', $form->has_food_allergy)
                ->where('data.medical_condition.food_allergy_description', $form->food_allergy_description)
                ->where('data.medical_condition.has_drug_allergy', $form->has_drug_allergy)
                ->where('data.medical_condition.drug_allergy_description', $form->drug_allergy_description)
                ->where('data.medical_condition.has_medication', $form->has_medication)
                ->where('data.medical_condition.medication_description', $form->medication_description)
        );
    }

    // Social Background Form
    public function test_put_form_social_background_success()
    {

        $assessment_case = AssessmentCase::factory()->create();

        $data = [
            'is_cga' => true,
            'is_bzn' => true,
            'marital_status' => $this->faker->randomElement([1, 2, 3, 4]),
            'safety_alarm' => $this->faker->boolean,
            'has_carer' => $this->faker->boolean,
            'carer_option' => $this->faker->randomNumber,
            'carer' => $this->faker->text,
            'employment_status' => $this->faker->randomElement([1, 2, 3, 4]),
            'has_community_resource' => $this->faker->boolean,
            'education_level' => $this->faker->randomElement([1, 2, 3, 4]),
            // 'financial_state' => $this->faker->randomElement([1, 2, 3, 4, 5, 6]),
            'smoking_option' => $this->faker->randomElement([1, 2, 3]),
            'smoking' => $this->faker->randomNumber,
            'drinking_option' => $this->faker->randomElement([1, 2, 3]),
            'drinking' => $this->faker->randomNumber,
            'has_religion' => $this->faker->boolean,
            'religion' => $this->faker->text,
            'has_social_activity' => $this->faker->boolean,
            'social_activity' => $this->faker->text,
            'lubben_total_score' => $this->faker->numberBetween(0, 30),
            'employment_remark' => $this->faker->word,
        ];

        $response = $this->putJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=social_background", $data);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.marital_status', $data['marital_status'])
                ->where('data.safety_alarm', $data['safety_alarm'])
                ->where('data.has_carer', $data['has_carer'])
                ->where('data.carer_option', $data['carer_option'])
                ->where('data.carer', $data['carer'])
                ->where('data.employment_status', $data['employment_status'])
                ->where('data.has_community_resource', $data['has_community_resource'])
                ->where('data.education_level', $data['education_level'])
                // ->where('data.financial_state', $data['financial_state'])
                ->where('data.smoking_option', $data['smoking_option'])
                ->where('data.smoking', $data['smoking'])
                ->where('data.drinking_option', $data['drinking_option'])
                ->where('data.drinking', $data['drinking'])
                ->where('data.has_religion', $data['has_religion'])
                ->where('data.religion', $data['religion'])
                ->where('data.employment_remark', $data['employment_remark'])
                ->where('data.has_social_activity', $data['has_social_activity'])
                ->where('data.social_activity', $data['social_activity'])
                ->where('data.lubben_total_score', $data['lubben_total_score'])
        );
    }

    public function test_get_form_social_background_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $form = SocialBackgroundForm::factory()
            ->for($assessment_case)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=social_background");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.marital_status', $form->marital_status)
                ->where('data.safety_alarm', $form->safety_alarm)
                ->where('data.has_carer', $form->has_carer)
                ->where('data.carer_option', $form->carer_option)
                ->where('data.carer', $form->carer)
                ->where('data.employment_status', $form->employment_status)
                ->where('data.has_community_resource', $form->has_community_resource)
                ->where('data.education_level', $form->education_level)
                // ->where('data.financial_state', $form->financial_state)
                ->where('data.smoking_option', $form->smoking_option)
                ->where('data.smoking', $form->smoking)
                ->where('data.drinking_option', $form->drinking_option)
                ->where('data.drinking', $form->drinking)
                ->where('data.has_religion', $form->has_religion)
                ->where('data.religion', $form->religion)
                ->where('data.employment_remark', $form->employment_remark)
                ->where('data.has_social_activity', $form->has_social_activity)
                ->where('data.social_activity', $form->social_activity)
                ->where('data.lubben_total_score', $form->lubben_total_score)
        );
    }

    public function test_structure_form_social_background_success()
    {
        AssessmentCase::factory()->create(['id' => 1]);
        SocialBackgroundForm::factory()
            ->create(['id' => 1, 'assessment_case_id' => 1])
            ->each(function ($instance) {
                $instance->livingStatusTable()
                    ->saveMany(LivingStatusTable::factory(2)->make());
            });

        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "1?form_name=social_background");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'marital_status',
                'safety_alarm',
                'has_carer',
                'carer_option',
                'carer',
                'employment_status',
                'has_community_resource',
                'education_level',
                // 'financial_state',
                'smoking_option',
                'smoking',
                'drinking_option',
                'drinking',
                'has_religion',
                'religion',
                'has_social_activity',
                'social_activity',
                'lubben_total_score',
                'employment_remark',
                'living_status_table' => [
                    '*' => [
                        'ls_options',
                    ]
                ],
                'community_resource_table' => [
                    '*' => [
                        'community_resource'
                    ]
                ]
            ]
        ]);
    }

    // Test sync lubben value
    public function test_compare_lubben_total_score()
    {
        $assessment_case = AssessmentCase::factory()
            ->has(LubbenSocialNetworkScaleForm::factory()->count(1), 'lubbenSocialNetworkScaleForm')
            ->has(SocialBackgroundForm::factory()->count(1), 'socialBackgroundForm')
            ->create();

        $form = SocialBackgroundForm::factory()
            ->for($assessment_case)
            ->create([
                'lubben_total_score' => $assessment_case->lubbenSocialNetworkScaleForm->lubben_total_score,
            ]);
        $this->assertEquals($form->lubben_total_score, $assessment_case->lubbenSocialNetworkScaleForm->lubben_total_score);
    }

    // Function And Mobility Form
    public function test_put_form_function_mobility_success()
    {
        $assessment_case = AssessmentCase::factory()->create();

        $data = [
            'is_cga' => true,
            'is_bzn' => true,
            'iadl' => $this->faker->numberBetween(1, 3),
            'total_iadl_score' => $this->faker->numberBetween(0, 18),
            'mobility' => $this->faker->numberBetween(1, 4),
            'walk_with_assistance' => $this->faker->numberBetween(1, 7),
            'mobility_tug' => $this->faker->word,
            'left_single_leg' => $this->faker->boolean,
            'right_single_leg' => $this->faker->boolean,
            'range_of_motion' => $this->faker->numberBetween(1, 2),
            'upper_limb_left' => $this->faker->numberBetween(0, 5),
            'upper_limb_right' => $this->faker->numberBetween(0, 5),
            'lower_limb_left' => $this->faker->numberBetween(0, 5),
            'lower_limb_right' => $this->faker->numberBetween(0, 5),
            'fall_history' => $this->faker->boolean,
            'number_of_major_fall' => $this->faker->randomNumber,
            'major_fall_tables' => [[
                            'location' => 1,
                            'injury_sustained' => 1,
                            'fall_mechanism' => 'yes',
                            'fall_mechanism_other' => 'yes',
                            'fracture' => true,
                            'fracture_text' => 'yes'
                        ]]
            // 'location' => [1, 2],
            // 'injury_sustained' => [1, 2],
            // 'fall_mechanism' => ['yes', 'yes'],
            // 'fracture' => [true, false],
            // 'fracture_text' => ['yes', 'yes'],
        ];

        $response = $this->putJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=function_mobility", $data);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'iadl',
                'total_iadl_score',
                'mobility',
                'walk_with_assistance',
                'mobility_tug',
                'left_single_leg',
                'right_single_leg',
                'range_of_motion',
                'upper_limb_left',
                'upper_limb_right',
                'lower_limb_left',
                'lower_limb_right',
                'fall_history',
                'number_of_major_fall',
                'major_fall_table' => [
                    '*' => [
                        'location',
                        'injury_sustained',
                        'fall_mechanism',
                        'fall_mechanism_other',
                        'fracture',
                        'fracture_text'
                    ]
                ]
            ]
        ]);
    }

    public function test_get_form_function_mobility_success()
    {
        AssessmentCase::factory()->create(['id' => 1]);
        FunctionMobilityForm::factory()
            ->create(['id' => 1, 'assessment_case_id' => 1])
            ->each(function ($instance) {
                $instance->majorFallTable()
                    ->saveMany(MajorFallTable::factory(2)->make());
            });


        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "1?form_name=function_mobility");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'iadl',
                'total_iadl_score',
                'mobility',
                'walk_with_assistance',
                'mobility_tug',
                'left_single_leg',
                'right_single_leg',
                'range_of_motion',
                'upper_limb_left',
                'upper_limb_right',
                'lower_limb_left',
                'lower_limb_right',
                'fall_history',
                'number_of_major_fall',
                'major_fall_table' => [
                    '*' => [
                        'location',
                        'injury_sustained',
                        'fall_mechanism',
                        'fall_mechanism_other',
                        'fracture',
                        'fracture_text'
                    ]
                ]
            ]
        ]);
    }

    // Barthel Index Form
    public function test_put_form_barthel_index_success()
    {
        $assessment_case = AssessmentCase::factory()->create();

        $data = [
            'is_cga' => true,
            'is_bzn' => true,
            'bowels' => $this->faker->numberBetween(0, 2),
            'bladder' => $this->faker->numberBetween(0, 2),
            'grooming' => $this->faker->numberBetween(0, 1),
            'toilet_use' => $this->faker->numberBetween(0, 2),
            'feeding' => $this->faker->numberBetween(0, 2),
            'transfer' => $this->faker->numberBetween(0, 3),
            'mobility' => $this->faker->numberBetween(0, 3),
            'dressing' => $this->faker->numberBetween(0, 2),
            'stairs' => $this->faker->numberBetween(0, 2),
            'bathing' => $this->faker->numberBetween(0, 1),
            'barthel_total_score' => $this->faker->numberBetween(0, 20),
        ];

        $response = $this->putJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=barthel_index", $data);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.bowels', $data['bowels'])
                ->where('data.bladder', $data['bladder'])
                ->where('data.grooming', $data['grooming'])
                ->where('data.toilet_use', $data['toilet_use'])
                ->where('data.feeding', $data['feeding'])
                ->where('data.transfer', $data['transfer'])
                ->where('data.mobility', $data['mobility'])
                ->where('data.dressing', $data['dressing'])
                ->where('data.stairs', $data['stairs'])
                ->where('data.bathing', $data['bathing'])
                ->where('data.barthel_total_score', $data['barthel_total_score'])
        );
    }

    public function test_get_form_barthel_index_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $form = BarthelIndexForm::factory()
            ->for($assessment_case)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=barthel_index");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.bowels', $form->bowels)
                ->where('data.bladder', $form->bladder)
                ->where('data.grooming', $form->grooming)
                ->where('data.toilet_use', $form->toilet_use)
                ->where('data.feeding', $form->feeding)
                ->where('data.transfer', $form->transfer)
                ->where('data.mobility', $form->mobility)
                ->where('data.dressing', $form->dressing)
                ->where('data.stairs', $form->stairs)
                ->where('data.bathing', $form->bathing)
                ->where('data.barthel_total_score', $form->barthel_total_score)
        );
    }

    // Medication Adherence Form
    public function test_put_form_medication_adherence_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $assessment_date = new Carbon($this->faker->dateTime);
        $data = [
            'is_cga' => true,
            'is_bzn' => true,
            'elderly_central_ref_number' => $this->faker->word,
            'assessment_date' => Carbon::parse($assessment_date)->format('Y-m-d'),
            'assessor_name' => $this->faker->word,
            'assessment_kind' => $this->faker->numberBetween(0, 2),
            'is_forget_sometimes' => $this->faker->boolean,
            'is_missed_meds' => $this->faker->boolean,
            'is_reduce_meds' => $this->faker->boolean,
            'is_forget_when_travel' => $this->faker->boolean,
            'is_meds_yesterday' => $this->faker->boolean,
            'is_stop_when_better' => $this->faker->boolean,
            'is_annoyed' => $this->faker->boolean,
            'forget_sometimes' => $this->faker->text,
            'missed_meds' => $this->faker->text,
            'reduce_meds' => $this->faker->text,
            'forget_when_travel' => $this->faker->text,
            'meds_yesterday' => $this->faker->text,
            'stop_when_better' => $this->faker->text,
            'annoyed' => $this->faker->text,
            'forget_frequency' => $this->faker->numberBetween(1, 5),
        ];

        $response = $this->putJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=medication_adherence", $data);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.is_forget_sometimes', $data['is_forget_sometimes'])
                ->where('data.is_missed_meds', $data['is_missed_meds'])
                ->where('data.is_reduce_meds', $data['is_reduce_meds'])
                ->where('data.is_forget_when_travel', $data['is_forget_when_travel'])
                ->where('data.is_meds_yesterday', $data['is_meds_yesterday'])
                ->where('data.is_stop_when_better', $data['is_stop_when_better'])
                ->where('data.is_annoyed', $data['is_annoyed'])
                ->where('data.forget_sometimes', $data['forget_sometimes'])
                ->where('data.missed_meds', $data['missed_meds'])
                ->where('data.reduce_meds', $data['reduce_meds'])
                ->where('data.forget_when_travel', $data['forget_when_travel'])
                ->where('data.meds_yesterday', $data['meds_yesterday'])
                ->where('data.stop_when_better', $data['stop_when_better'])
                ->where('data.annoyed', $data['annoyed'])
                ->where('data.forget_frequency', $data['forget_frequency'])
        );
    }

    public function test_get_form_medication_adherence_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $form = MedicationAdherenceForm::factory()
            ->for($assessment_case)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=medication_adherence");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.elderly_central_ref_number', $form->elderly_central_ref_number)
                ->where('data.assessment_date', $form->assessment_date)
                ->where('data.assessment_kind', $form->assessment_kind)
                ->where('data.assessor_name', $form->assessor_name)
                ->where('data.is_forget_sometimes', $form->is_forget_sometimes)
                ->where('data.is_missed_meds', $form->is_missed_meds)
                ->where('data.is_reduce_meds', $form->is_reduce_meds)
                ->where('data.is_forget_when_travel', $form->is_forget_when_travel)
                ->where('data.is_meds_yesterday', $form->is_meds_yesterday)
                ->where('data.is_stop_when_better', $form->is_stop_when_better)
                ->where('data.is_annoyed', $form->is_annoyed)
                ->where('data.forget_sometimes', $form->forget_sometimes)
                ->where('data.missed_meds', $form->missed_meds)
                ->where('data.reduce_meds', $form->reduce_meds)
                ->where('data.forget_when_travel', $form->forget_when_travel)
                ->where('data.meds_yesterday', $form->meds_yesterday)
                ->where('data.stop_when_better', $form->stop_when_better)
                ->where('data.annoyed', $form->annoyed)
                ->where('data.forget_frequency', $form->forget_frequency)
        );
    }

    // Lubben Social Network Scale Form
    public function test_put_form_lubben_social_network_scale_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $assessment_date = new Carbon($this->faker->dateTime);
        $data = [
            'is_cga' => true,
            'is_bzn' => true,
            'elderly_central_ref_number' => $this->faker->word,
            'assessment_date' => Carbon::parse($assessment_date)->format('Y-m-d'),
            'assessor_name' => $this->faker->word,
            'assessment_kind' => $this->faker->numberBetween(0, 2),
            'relatives_sum' => $this->faker->numberBetween(0, 5),
            'relatives_to_talk' => $this->faker->numberBetween(0, 5),
            'relatives_to_help' => $this->faker->numberBetween(0, 5),
            'friends_sum' => $this->faker->numberBetween(0, 5),
            'friends_to_talk' => $this->faker->numberBetween(0, 5),
            'friends_to_help' => $this->faker->numberBetween(0, 5),
            'lubben_total_score' => $this->faker->numberBetween(0, 15),
        ];

        $response = $this->putJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=lubben_social_network_scale", $data);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.elderly_central_ref_number', $data['elderly_central_ref_number'])
                ->where('data.assessment_date', $data['assessment_date'])
                ->where('data.assessment_kind', $data['assessment_kind'])
                ->where('data.relatives_sum', $data['relatives_sum'])
                ->where('data.relatives_to_talk', $data['relatives_to_talk'])
                ->where('data.relatives_to_help', $data['relatives_to_help'])
                ->where('data.friends_sum', $data['friends_sum'])
                ->where('data.friends_to_talk', $data['friends_to_talk'])
                ->where('data.friends_to_help', $data['friends_to_help'])
                ->where('data.lubben_total_score', $data['lubben_total_score'])
        );
    }

    public function test_get_form_lubben_social_network_scale_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $form = LubbenSocialNetworkScaleForm::factory()
            ->for($assessment_case)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=lubben_social_network_scale");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.elderly_central_ref_number', $form->elderly_central_ref_number)
                ->where('data.assessment_date', $form->assessment_date)
                ->where('data.assessment_kind', $form->assessment_kind)
                ->where('data.relatives_sum', $form->relatives_sum)
                ->where('data.relatives_to_talk', $form->relatives_to_talk)
                ->where('data.relatives_to_help', $form->relatives_to_help)
                ->where('data.friends_sum', $form->friends_sum)
                ->where('data.friends_to_talk', $form->friends_to_talk)
                ->where('data.friends_to_help', $form->friends_to_help)
                ->where('data.lubben_total_score', $form->lubben_total_score)
        );
    }

    // Geriatric Depression Scale Form
    public function test_put_form_geriatric_depression_scale_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $assessment_date = new Carbon($this->faker->dateTime);
        $data = [
            'is_cga' => true,
            'is_bzn' => true,
            'elderly_central_ref_number' => $this->faker->word,
            'assessment_date' => Carbon::parse($assessment_date)->format('Y-m-d'),
            'assessor_name' => $this->faker->word,
            'assessment_kind' => $this->faker->numberBetween(0, 2),
            'is_satisfied' => $this->faker->numberBetween(0, 1),
            'is_given_up' => $this->faker->numberBetween(0, 1),
            'is_feel_empty' => $this->faker->numberBetween(0, 1),
            'is_often_bored' => $this->faker->numberBetween(0, 1),
            'is_happy_a_lot' => $this->faker->numberBetween(0, 1),
            'is_affraid' => $this->faker->numberBetween(0, 1),
            'is_happy_all_day' => $this->faker->numberBetween(0, 1),
            'is_feel_helpless' => $this->faker->numberBetween(0, 1),
            'is_prefer_stay' => $this->faker->numberBetween(0, 1),
            'is_memory_problem' => $this->faker->numberBetween(0, 1),
            'is_good_to_alive' => $this->faker->numberBetween(0, 1),
            'is_feel_useless' => $this->faker->numberBetween(0, 1),
            'is_feel_energic' => $this->faker->numberBetween(0, 1),
            'is_hopeless' => $this->faker->numberBetween(0, 1),
            'is_people_better' => $this->faker->numberBetween(0, 1),
            'gds15_score' => $this->faker->numberBetween(0, 15),
        ];

        $response = $this->putJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=geriatric_depression_scale", $data);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.elderly_central_ref_number', $data['elderly_central_ref_number'])
                ->where('data.assessment_date', $data['assessment_date'])
                ->where('data.assessment_kind', $data['assessment_kind'])
                ->where('data.is_satisfied', $data['is_satisfied'])
                ->where('data.is_given_up', $data['is_given_up'])
                ->where('data.is_feel_empty', $data['is_feel_empty'])
                ->where('data.is_often_bored', $data['is_often_bored'])
                ->where('data.is_happy_a_lot', $data['is_happy_a_lot'])
                ->where('data.is_affraid', $data['is_affraid'])
                ->where('data.is_happy_all_day', $data['is_happy_all_day'])
                ->where('data.is_feel_helpless', $data['is_feel_helpless'])
                ->where('data.is_prefer_stay', $data['is_prefer_stay'])
                ->where('data.is_memory_problem', $data['is_memory_problem'])
                ->where('data.is_good_to_alive', $data['is_good_to_alive'])
                ->where('data.is_feel_useless', $data['is_feel_useless'])
                ->where('data.is_feel_energic', $data['is_feel_energic'])
                ->where('data.is_hopeless', $data['is_hopeless'])
                ->where('data.is_people_better', $data['is_people_better'])
                ->where('data.gds15_score', $data['gds15_score'])
        );
    }

    public function test_get_form_geriatric_depression_scale_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $form = GeriatricDepressionScaleForm::factory()
            ->for($assessment_case)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=geriatric_depression_scale");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.elderly_central_ref_number', $form->elderly_central_ref_number)
                ->where('data.assessment_date', $form->assessment_date)
                ->where('data.assessment_kind', $form->assessment_kind)
                ->where('data.is_satisfied', $form->is_satisfied)
                ->where('data.is_given_up', $form->is_given_up)
                ->where('data.is_feel_empty', $form->is_feel_empty)
                ->where('data.is_often_bored', $form->is_often_bored)
                ->where('data.is_happy_a_lot', $form->is_happy_a_lot)
                ->where('data.is_affraid', $form->is_affraid)
                ->where('data.is_happy_all_day', $form->is_happy_all_day)
                ->where('data.is_feel_helpless', $form->is_feel_helpless)
                ->where('data.is_prefer_stay', $form->is_prefer_stay)
                ->where('data.is_memory_problem', $form->is_memory_problem)
                ->where('data.is_good_to_alive', $form->is_good_to_alive)
                ->where('data.is_feel_useless', $form->is_feel_useless)
                ->where('data.is_feel_energic', $form->is_feel_energic)
                ->where('data.is_hopeless', $form->is_hopeless)
                ->where('data.is_people_better', $form->is_people_better)
                ->where('data.gds15_score', $form->gds15_score)
        );
    }

    // IADL Form
    public function test_put_form_iadl_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $assessment_date = new Carbon($this->faker->dateTime);
        $data = [
            'is_cga' => true,
            'is_bzn' => true,
            'elderly_central_ref_number' => $this->faker->text,
            'assessment_date' => Carbon::parse($assessment_date)->format('Y-m-d'),
            'assessor_name' => $this->faker->text,
            'assessment_kind' => $this->faker->numberBetween(0, 2),
            'can_use_phone' => $this->faker->numberBetween(0, 2),
            'text_use_phone' => $this->faker->text,
            'can_take_ride' => $this->faker->numberBetween(0, 2),
            'text_take_ride' => $this->faker->text,
            'can_buy_food' => $this->faker->numberBetween(0, 2),
            'text_buy_food' => $this->faker->text,
            'can_cook' => $this->faker->numberBetween(0, 2),
            'text_cook' => $this->faker->text,
            'can_do_housework' => $this->faker->numberBetween(0, 2),
            'text_do_housework' => $this->faker->text,
            'can_do_repairment' => $this->faker->numberBetween(0, 2),
            'text_do_repairment' => $this->faker->text,
            'can_do_laundry' => $this->faker->numberBetween(0, 2),
            'text_do_laundry' => $this->faker->text,
            'can_take_medicine' => $this->faker->numberBetween(0, 2),
            'text_take_medicine' => $this->faker->text,
            'can_handle_finances' => $this->faker->numberBetween(0, 2),
            'text_handle_finances' => $this->faker->text,
            'iadl_total_score' => $this->faker->numberBetween(0, 18),
        ];

        $response = $this->putJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=iadl", $data);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.elderly_central_ref_number', $data['elderly_central_ref_number'])
                ->where('data.assessment_date', $data['assessment_date'])
                ->where('data.assessment_kind', $data['assessment_kind'])
                ->where('data.can_use_phone', $data['can_use_phone'])
                ->where('data.text_use_phone', $data['text_use_phone'])
                ->where('data.can_take_ride', $data['can_take_ride'])
                ->where('data.text_take_ride', $data['text_take_ride'])
                ->where('data.can_buy_food', $data['can_buy_food'])
                ->where('data.text_buy_food', $data['text_buy_food'])
                ->where('data.can_cook', $data['can_cook'])
                ->where('data.text_cook', $data['text_cook'])
                ->where('data.can_do_housework', $data['can_do_housework'])
                ->where('data.text_do_housework', $data['text_do_housework'])
                ->where('data.can_do_repairment', $data['can_do_repairment'])
                ->where('data.text_do_repairment', $data['text_do_repairment'])
                ->where('data.can_do_laundry', $data['can_do_laundry'])
                ->where('data.text_do_laundry', $data['text_do_laundry'])
                ->where('data.can_take_medicine', $data['can_take_medicine'])
                ->where('data.text_take_medicine', $data['text_take_medicine'])
                ->where('data.can_handle_finances', $data['can_handle_finances'])
                ->where('data.text_handle_finances', $data['text_handle_finances'])
                ->where('data.iadl_total_score', $data['iadl_total_score'])
        );
    }

    public function test_get_form_iadl_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $form = IadlForm::factory()
            ->for($assessment_case)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=iadl");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.elderly_central_ref_number', $form->elderly_central_ref_number)
                ->where('data.assessment_date', $form->assessment_date)
                ->where('data.assessment_kind', $form->assessment_kind)
                ->where('data.can_use_phone', $form->can_use_phone)
                ->where('data.text_use_phone', $form->text_use_phone)
                ->where('data.can_take_ride', $form->can_take_ride)
                ->where('data.text_take_ride', $form->text_take_ride)
                ->where('data.can_buy_food', $form->can_buy_food)
                ->where('data.text_buy_food', $form->text_buy_food)
                ->where('data.can_cook', $form->can_cook)
                ->where('data.text_cook', $form->text_cook)
                ->where('data.can_do_housework', $form->can_do_housework)
                ->where('data.text_do_housework', $form->text_do_housework)
                ->where('data.can_do_repairment', $form->can_do_repairment)
                ->where('data.text_do_repairment', $form->text_do_repairment)
                ->where('data.can_do_laundry', $form->can_do_laundry)
                ->where('data.text_do_laundry', $form->text_do_laundry)
                ->where('data.can_take_medicine', $form->can_take_medicine)
                ->where('data.text_take_medicine', $form->text_take_medicine)
                ->where('data.can_handle_finances', $form->can_handle_finances)
                ->where('data.text_handle_finances', $form->text_handle_finances)
                ->where('data.iadl_total_score', $form->iadl_total_score)
        );
    }

    // Montreal Cognitive Assessment Form
    public function test_put_form_montreal_cognitive_assessment_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $assessment_date = new Carbon($this->faker->dateTime);
        $data = [
            'is_cga' => true,
            'is_bzn' => true,
            'elderly_central_ref_number' => $this->faker->text,
            'assessment_date' => Carbon::parse($assessment_date)->format('Y-m-d'),
            'assessor_name' => $this->faker->text,
            'assessment_kind' => $this->faker->randomNumber,
            'memory_c11' => $this->faker->boolean,
            'memory_c12' => $this->faker->boolean,
            'memory_c13' => $this->faker->boolean,
            'memory_c14' => $this->faker->boolean,
            'memory_c15' => $this->faker->boolean,
            'memory_c21' => $this->faker->boolean,
            'memory_c22' => $this->faker->boolean,
            'memory_c23' => $this->faker->boolean,
            'memory_c24' => $this->faker->boolean,
            'memory_c25' => $this->faker->boolean,
            'memory_score' => $this->faker->randomFloat(2, 0, 5),
            'language_fluency1' => $this->faker->word,
            'language_fluency2' => $this->faker->word,
            'language_fluency3' => $this->faker->word,
            'language_fluency4' => $this->faker->word,
            'language_fluency5' => $this->faker->word,
            'language_fluency6' => $this->faker->word,
            'language_fluency7' => $this->faker->word,
            'language_fluency8' => $this->faker->word,
            'language_fluency9' => $this->faker->word,
            'language_fluency10' => $this->faker->word,
            'language_fluency11' => $this->faker->word,
            'language_fluency12' => $this->faker->word,
            'language_fluency13' => $this->faker->word,
            'language_fluency14' => $this->faker->word,
            'language_fluency15' => $this->faker->word,
            'language_fluency16' => $this->faker->word,
            'language_fluency17' => $this->faker->word,
            'language_fluency18' => $this->faker->word,
            'language_fluency19' => $this->faker->word,
            'language_fluency20' => $this->faker->word,
            'all_words' => $this->faker->randomFloat(2, 0, 9),
            'repeat_words' => $this->faker->randomFloat(2, 0, 9),
            'non_animal_words' => $this->faker->randomFloat(2, 0, 9),
            'language_fluency_score' => $this->faker->randomFloat(2, 0, 9),
            'orientation_day' => $this->faker->numberBetween(1, 31),
            'orientation_month' => $this->faker->numberBetween(1, 12),
            'orientation_year' => $this->faker->numberBetween(1900, date('Y') + 1),
            'orientation_week' => $this->faker->numberBetween(1, 4),
            'orientation_place' => $this->faker->word,
            'orientation_area' => $this->faker->word,
            'orientation_score' => $this->faker->randomFloat(2, 0, 6),
            'face_word' => $this->faker->randomElement([1, 2, 3]),
            'velvet_word' => $this->faker->randomElement([1, 2, 3]),
            'church_word' => $this->faker->randomElement([1, 2, 3]),
            'daisy_word' => $this->faker->randomElement([1, 2, 3]),
            'red_word' => $this->faker->randomElement([1, 2, 3]),
            'delayed_memory_score' => $this->faker->randomFloat(2, 0, 10),
            'category_percentile' => $this->faker->randomElement([1, 2, 3, 4]),
            'total_moca_score' => $this->faker->randomFloat(2, 0, 30),
            'education_level' => $this->faker->word
        ];

        $response = $this->putJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=montreal_cognitive_assessment", $data);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.elderly_central_ref_number', $data['elderly_central_ref_number'])
                ->where('data.assessment_date', $data['assessment_date'])
                ->where('data.assessment_kind', $data['assessment_kind'])
                ->where('data.memory_c11', $data['memory_c11'])
                ->where('data.memory_c12', $data['memory_c12'])
                ->where('data.memory_c13', $data['memory_c13'])
                ->where('data.memory_c14', $data['memory_c14'])
                ->where('data.memory_c15', $data['memory_c15'])
                ->where('data.memory_c21', $data['memory_c21'])
                ->where('data.memory_c22', $data['memory_c22'])
                ->where('data.memory_c23', $data['memory_c23'])
                ->where('data.memory_c24', $data['memory_c24'])
                ->where('data.memory_c25', $data['memory_c25'])
                ->where('data.memory_score', sprintf('%0.2f', $data['memory_score']))
                ->where('data.language_fluency1', $data['language_fluency1'])
                ->where('data.language_fluency2', $data['language_fluency2'])
                ->where('data.language_fluency3', $data['language_fluency3'])
                ->where('data.language_fluency4', $data['language_fluency4'])
                ->where('data.language_fluency5', $data['language_fluency5'])
                ->where('data.language_fluency6', $data['language_fluency6'])
                ->where('data.language_fluency7', $data['language_fluency7'])
                ->where('data.language_fluency8', $data['language_fluency8'])
                ->where('data.language_fluency9', $data['language_fluency9'])
                ->where('data.language_fluency10', $data['language_fluency10'])
                ->where('data.language_fluency11', $data['language_fluency11'])
                ->where('data.language_fluency12', $data['language_fluency12'])
                ->where('data.language_fluency13', $data['language_fluency13'])
                ->where('data.language_fluency14', $data['language_fluency14'])
                ->where('data.language_fluency15', $data['language_fluency15'])
                ->where('data.language_fluency16', $data['language_fluency16'])
                ->where('data.language_fluency17', $data['language_fluency17'])
                ->where('data.language_fluency18', $data['language_fluency18'])
                ->where('data.language_fluency19', $data['language_fluency19'])
                ->where('data.language_fluency20', $data['language_fluency20'])
                ->where('data.all_words', sprintf('%0.2f', $data['all_words']))
                ->where('data.repeat_words', sprintf('%0.2f', $data['repeat_words']))
                ->where('data.non_animal_words', sprintf('%0.2f', $data['non_animal_words']))
                ->where('data.language_fluency_score', sprintf('%0.2f', $data['language_fluency_score']))
                ->where('data.orientation_day', $data['orientation_day'])
                ->where('data.orientation_month', $data['orientation_month'])
                ->where('data.orientation_year', $data['orientation_year'])
                ->where('data.orientation_week', $data['orientation_week'])
                ->where('data.orientation_place', $data['orientation_place'])
                ->where('data.orientation_area', $data['orientation_area'])
                ->where('data.orientation_score', sprintf('%0.2f', $data['orientation_score']))
                ->where('data.face_word', $data['face_word'])
                ->where('data.velvet_word', $data['velvet_word'])
                ->where('data.church_word', $data['church_word'])
                ->where('data.daisy_word', $data['daisy_word'])
                ->where('data.red_word', $data['red_word'])
                ->where('data.delayed_memory_score', sprintf('%0.2f', $data['delayed_memory_score']))
                ->where('data.category_percentile', $data['category_percentile'])
                ->where('data.total_moca_score', sprintf('%0.2f', $data['total_moca_score']))
                ->where('data.education_level', $data['education_level'])
        );
    }

    public function test_get_form_montreal_cognitive_assessment_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $form = MontrealCognitiveAssessmentForm::factory()
            ->for($assessment_case)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=montreal_cognitive_assessment");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.elderly_central_ref_number', $form->elderly_central_ref_number)
                ->where('data.assessment_date', $form->assessment_date)
                ->where('data.assessment_kind', $form->assessment_kind)
                ->where('data.memory_c11', $form->memory_c11)
                ->where('data.memory_c12', $form->memory_c12)
                ->where('data.memory_c13', $form->memory_c13)
                ->where('data.memory_c14', $form->memory_c14)
                ->where('data.memory_c15', $form->memory_c15)
                ->where('data.memory_c21', $form->memory_c21)
                ->where('data.memory_c22', $form->memory_c22)
                ->where('data.memory_c23', $form->memory_c23)
                ->where('data.memory_c24', $form->memory_c24)
                ->where('data.memory_c25', $form->memory_c25)
                ->where('data.memory_score', sprintf('%0.2f', $form->memory_score))
                ->where('data.language_fluency1', $form->language_fluency1)
                ->where('data.language_fluency2', $form->language_fluency2)
                ->where('data.language_fluency3', $form->language_fluency3)
                ->where('data.language_fluency4', $form->language_fluency4)
                ->where('data.language_fluency5', $form->language_fluency5)
                ->where('data.language_fluency6', $form->language_fluency6)
                ->where('data.language_fluency7', $form->language_fluency7)
                ->where('data.language_fluency8', $form->language_fluency8)
                ->where('data.language_fluency9', $form->language_fluency9)
                ->where('data.language_fluency10', $form->language_fluency10)
                ->where('data.language_fluency11', $form->language_fluency11)
                ->where('data.language_fluency12', $form->language_fluency12)
                ->where('data.language_fluency13', $form->language_fluency13)
                ->where('data.language_fluency14', $form->language_fluency14)
                ->where('data.language_fluency15', $form->language_fluency15)
                ->where('data.language_fluency16', $form->language_fluency16)
                ->where('data.language_fluency17', $form->language_fluency17)
                ->where('data.language_fluency18', $form->language_fluency18)
                ->where('data.language_fluency19', $form->language_fluency19)
                ->where('data.language_fluency20', $form->language_fluency20)
                ->where('data.all_words', sprintf('%0.2f', $form->all_words))
                ->where('data.repeat_words', sprintf('%0.2f', $form->repeat_words))
                ->where('data.non_animal_words', sprintf('%0.2f', $form->non_animal_words))
                ->where('data.language_fluency_score', sprintf('%0.2f', $form->language_fluency_score))
                ->where('data.orientation_day', $form->orientation_day)
                ->where('data.orientation_month', $form->orientation_month)
                ->where('data.orientation_year', $form->orientation_year)
                ->where('data.orientation_week', $form->orientation_week)
                ->where('data.orientation_place', $form->orientation_place)
                ->where('data.orientation_area', $form->orientation_area)
                ->where('data.orientation_score', sprintf('%0.2f', $form->orientation_score))
                ->where('data.face_word', $form->face_word)
                ->where('data.velvet_word', $form->velvet_word)
                ->where('data.church_word', $form->church_word)
                ->where('data.daisy_word', $form->daisy_word)
                ->where('data.red_word', $form->red_word)
                ->where('data.delayed_memory_score', sprintf('%0.2f', $form->delayed_memory_score))
                ->where('data.category_percentile', $form->category_percentile)
                ->where('data.total_moca_score', sprintf('%0.2f', $form->total_moca_score))
                ->where('data.education_level', $form->education_level)
        );
    }

    // Genogram Form
    public function test_get_download_file_genogram_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $form = GenogramForm::factory()
            ->for($assessment_case)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=genogram");

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data')
                ->where('data.id', $assessment_case->id)
                ->where('data.file_name', $form->file_name)
                ->where('data.url', $form->url)
        );
    }

    // Physical Condition Form
    public function test_put_form_physical_condition_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $hour_minute = new Carbon($this->faker->dateTime);
        $data = [
            'is_cga' => true,
            'is_bzn' => true,
            // General Condition
            'general_condition' => $this->faker->numberBetween(1, 4),
            'eye_opening_response' => $this->faker->numberBetween(1, 4),
            'verbal_response' => $this->faker->numberBetween(1, 5),
            'motor_response' => $this->faker->numberBetween(1, 6),
            'glasgow_score' => $this->faker->numberBetween(0, 15),

            // Mental State
            'mental_state' => $this->faker->numberBetween(1, 4),
            'edu_percentile' => $this->faker->numberBetween(1, 4),
            'moca_score' => $this->faker->numberBetween(0, 30),

            // Emotional State
            'emotional_state' => $this->faker->numberBetween(1, 3),
            'geriatric_score' => $this->faker->numberBetween(0, 30),

            // Sensory
            'is_good' => $this->faker->boolean,
            'is_deaf' => $this->faker->boolean,
            'dumb_left' => $this->faker->boolean,
            'dumb_right' => $this->faker->boolean,
            'non_verbal' => $this->faker->boolean,
            'is_visual_impaired' => $this->faker->boolean,
            'blind_left' => $this->faker->boolean,
            'blind_right' => $this->faker->boolean,
            'no_vision' => $this->faker->boolean,
            'is_assistive_devices' => $this->faker->boolean,
            'denture' => $this->faker->boolean,
            'hearing_aid' => $this->faker->boolean,
            'glasses' => $this->faker->boolean,

            // Nutrition
            'dat_special_diet' => $this->faker->numberBetween(1,2),
            'special_diet' => $this->faker->word,
            'is_special_feeding' => $this->faker->numberBetween(1,2),
            'special_feeding' => $this->faker->numberBetween(1, 2),
            'thickener_formula' => $this->faker->word,
            'fluid_restriction' => $this->faker->word,
            'tube_next_change' => $this->faker->word,
            'milk_formula' => $this->faker->word,
            'milk_regime' => $this->faker->word,
            'feeding_person' => $this->faker->numberBetween(1, 2),
            'feeding_person_text' => $this->faker->word,
            'feeding_technique' => $this->faker->numberBetween(1, 4),
            'ng_tube' => $this->faker->word,

            // Skin Condition
            'intact_abnormal' => $this->faker->numberBetween(1,2),
            'is_napkin_associated' => $this->faker->boolean,
            'is_dry' => $this->faker->boolean,
            'is_cellulitis' => $this->faker->boolean,
            'cellulitis_desc' => $this->faker->word,
            'is_eczema' => $this->faker->boolean,
            'eczema_desc' => $this->faker->word,
            'is_scalp' => $this->faker->boolean,
            'scalp_desc' => $this->faker->word,
            'is_itchy' => $this->faker->boolean,
            'itchy_desc' => $this->faker->word,
            'is_wound' => $this->faker->boolean,
            'wound_desc' => $this->faker->word,
            'wound_size' => $this->faker->randomFloat(2, 0, 100),
            'tunneling_time' => '02:15',
            'wound_bed' => $this->faker->randomFloat(2, 0, 100),
            'granulating_tissue' => $this->faker->randomFloat(2, 0, 100),
            'necrotic_tissue' => $this->faker->randomFloat(2, 0, 100),
            'sloughy_tissue' => $this->faker->randomFloat(2, 0, 100),
            'other_tissue' => $this->faker->randomFloat(2, 0, 100),
            'exudate_amount' => $this->faker->numberBetween(1, 3),
            'exudate_type' => $this->faker->numberBetween(1, 4),
            'other_exudate' => $this->faker->word,
            'surrounding_skin' => $this->faker->numberBetween(1, 6),
            'other_surrounding' => $this->faker->word,
            'odor' => $this->faker->numberBetween(1,2),
            'pain' => $this->faker->numberBetween(1,2),

            // Elimination
            'bowel_habit' => $this->faker->numberBetween(1,2),
            'abnormal_option' => $this->faker->numberBetween(1, 3),
            'fi_bowel' => $this->faker->numberBetween(1, 2),
            'urinary_habit' => $this->faker->numberBetween(1, 3),
            'fi_urine' => $this->faker->numberBetween(1, 2),
            'urine_device' => $this->faker->numberBetween(1, 2),
            'catheter_type' => $this->faker->numberBetween(1, 3),
            'catheter_next_change' => $this->faker->word,
            'catheter_size_fr' => $this->faker->numberBetween(1, 3),

            // Pain
            'is_pain' => $this->faker->numberBetween(1,2),
            'napkin_associated_desc' => $this->faker->word,
        ];

        $response = $this->putJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=physical_condition", $data);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)

                // General Condition
                ->where('data.general_condition', $data['general_condition'])
                ->where('data.eye_opening_response', $data['eye_opening_response'])
                ->where('data.verbal_response', $data['verbal_response'])
                ->where('data.motor_response', $data['motor_response'])
                ->where('data.glasgow_score', $data['glasgow_score'])

                // Mental State
                ->where('data.mental_state', $data['mental_state'])
                ->where('data.edu_percentile', $data['edu_percentile'])
                ->where('data.moca_score', $data['moca_score'])

                // Emotional State
                ->where('data.emotional_state', $data['emotional_state'])
                ->where('data.geriatric_score', $data['geriatric_score'])

                // Sensory
                ->where('data.is_good', $data['is_good'])
                ->where('data.is_deaf', $data['is_deaf'])
                ->where('data.dumb_left', $data['dumb_left'])
                ->where('data.dumb_right', $data['dumb_right'])
                ->where('data.non_verbal', $data['non_verbal'])
                ->where('data.is_visual_impaired', $data['is_visual_impaired'])
                ->where('data.blind_left', $data['blind_left'])
                ->where('data.blind_right', $data['blind_right'])
                ->where('data.no_vision', $data['no_vision'])
                ->where('data.is_assistive_devices', $data['is_assistive_devices'])
                ->where('data.denture', $data['denture'])
                ->where('data.hearing_aid', $data['hearing_aid'])
                ->where('data.glasses', $data['glasses'])

                // Nutrition
                ->where('data.dat_special_diet', $data['dat_special_diet'])
                ->where('data.special_diet', $data['special_diet'])
                ->where('data.is_special_feeding', $data['is_special_feeding'])
                ->where('data.special_feeding', $data['special_feeding'])
                ->where('data.thickener_formula', $data['thickener_formula'])
                ->where('data.fluid_restriction', $data['fluid_restriction'])
                ->where('data.tube_next_change', $data['tube_next_change'])
                ->where('data.milk_formula', $data['milk_formula'])
                ->where('data.milk_regime', $data['milk_regime'])
                ->where('data.feeding_person', $data['feeding_person'])
                ->where('data.feeding_person_text', $data['feeding_person_text'])
                ->where('data.feeding_technique', $data['feeding_technique'])
                ->where('data.ng_tube', $data['ng_tube'])

                // Skin Condition
                ->where('data.intact_abnormal', $data['intact_abnormal'])
                ->where('data.is_napkin_associated', $data['is_napkin_associated'])
                ->where('data.is_dry', $data['is_dry'])
                ->where('data.is_cellulitis', $data['is_cellulitis'])
                ->where('data.cellulitis_desc', $data['cellulitis_desc'])
                ->where('data.is_eczema', $data['is_eczema'])
                ->where('data.eczema_desc', $data['eczema_desc'])
                ->where('data.is_scalp', $data['is_scalp'])
                ->where('data.scalp_desc', $data['scalp_desc'])
                ->where('data.is_itchy', $data['is_itchy'])
                ->where('data.itchy_desc', $data['itchy_desc'])
                ->where('data.is_wound', $data['is_wound'])
                ->where('data.wound_desc', $data['wound_desc'])
                ->where('data.wound_size', sprintf('%0.2f', $data['wound_size']))
                ->where('data.tunneling_time', $data['tunneling_time'])
                ->where('data.wound_bed', sprintf('%0.2f', $data['wound_bed']))
                ->where('data.granulating_tissue', sprintf('%0.2f', $data['granulating_tissue']))
                ->where('data.necrotic_tissue', sprintf('%0.2f', $data['necrotic_tissue']))
                ->where('data.sloughy_tissue', sprintf('%0.2f', $data['sloughy_tissue']))
                ->where('data.other_tissue', sprintf('%0.2f', $data['other_tissue']))
                ->where('data.exudate_amount', $data['exudate_amount'])
                ->where('data.exudate_type', $data['exudate_type'])
                ->where('data.other_exudate', $data['other_exudate'])
                ->where('data.surrounding_skin', $data['surrounding_skin'])
                ->where('data.other_surrounding', $data['other_surrounding'])
                ->where('data.odor', $data['odor'])
                ->where('data.pain', $data['pain'])

                // Elimination
                ->where('data.bowel_habit', $data['bowel_habit'])
                ->where('data.abnormal_option', $data['abnormal_option'])
                ->where('data.fi_bowel', $data['fi_bowel'])
                ->where('data.urinary_habit', $data['urinary_habit'])
                ->where('data.fi_urine', $data['fi_urine'])
                ->where('data.urine_device', $data['urine_device'])
                ->where('data.catheter_type', $data['catheter_type'])
                ->where('data.catheter_next_change', $data['catheter_next_change'])
                ->where('data.catheter_size_fr', $data['catheter_size_fr'])
                ->where('data.napkin_associated_desc', $data['napkin_associated_desc'])

                // Pain
                ->where('data.is_pain', $data['is_pain'])                
        );
    }

    public function test_get_form_physical_condition_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $form = PhysicalConditionForm::factory()
            ->for($assessment_case)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=physical_condition");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)

                // General Condition
                ->where('data.general_condition', $form->general_condition)
                ->where('data.eye_opening_response', $form->eye_opening_response)
                ->where('data.verbal_response', $form->verbal_response)
                ->where('data.motor_response', $form->motor_response)
                ->where('data.glasgow_score', $form->glasgow_score)

                // Mental State
                ->where('data.mental_state', $form->mental_state)
                ->where('data.edu_percentile', $form->edu_percentile)
                ->where('data.moca_score', $form->moca_score)

                // Emotional State
                ->where('data.emotional_state', $form->emotional_state)
                ->where('data.geriatric_score', $form->geriatric_score)

                // Sensory
                ->where('data.is_good', $form->is_good)
                ->where('data.is_deaf', $form->is_deaf)
                ->where('data.dumb_left', $form->dumb_left)
                ->where('data.dumb_right', $form->dumb_right)
                ->where('data.non_verbal', $form->non_verbal)
                ->where('data.is_visual_impaired', $form->is_visual_impaired)
                ->where('data.blind_left', $form->blind_left)
                ->where('data.blind_right', $form->blind_right)
                ->where('data.no_vision', $form->no_vision)
                ->where('data.is_assistive_devices', $form->is_assistive_devices)
                ->where('data.denture', $form->denture)
                ->where('data.hearing_aid', $form->hearing_aid)
                ->where('data.glasses', $form->glasses)

                // Nutrition
                ->where('data.dat_special_diet', $form->dat_special_diet)
                ->where('data.special_diet', $form->special_diet)
                ->where('data.is_special_feeding', $form->is_special_feeding)
                ->where('data.special_feeding', $form->special_feeding)
                ->where('data.thickener_formula', $form->thickener_formula)
                ->where('data.fluid_restriction', $form->fluid_restriction)
                ->where('data.tube_next_change', $form->tube_next_change)
                ->where('data.milk_formula', $form->milk_formula)
                ->where('data.milk_regime', $form->milk_regime)
                ->where('data.feeding_person', $form->feeding_person)
                ->where('data.feeding_person_text', $form->feeding_person_text)
                ->where('data.feeding_technique', $form->feeding_technique)
                ->where('data.ng_tube', $form->ng_tube)

                // Skin Condition
                ->where('data.intact_abnormal', $form->intact_abnormal)
                ->where('data.is_napkin_associated', $form->is_napkin_associated)
                ->where('data.is_dry', $form->is_dry)
                ->where('data.is_cellulitis', $form->is_cellulitis)
                ->where('data.cellulitis_desc', $form->cellulitis_desc)
                ->where('data.is_eczema', $form->is_eczema)
                ->where('data.eczema_desc', $form->eczema_desc)
                ->where('data.is_scalp', $form->is_scalp)
                ->where('data.scalp_desc', $form->scalp_desc)
                ->where('data.is_itchy', $form->is_itchy)
                ->where('data.itchy_desc', $form->itchy_desc)
                ->where('data.is_wound', $form->is_wound)
                ->where('data.wound_desc', $form->wound_desc)
                ->where('data.wound_size', sprintf('%0.2f', $form->wound_size))
                ->where('data.tunneling_time', $form->tunneling_time)
                ->where('data.wound_bed', $form->wound_bed)
                ->where('data.granulating_tissue', sprintf('%0.2f', $form->granulating_tissue))
                ->where('data.necrotic_tissue', sprintf('%0.2f', $form->necrotic_tissue))
                ->where('data.sloughy_tissue', sprintf('%0.2f', $form->sloughy_tissue))
                ->where('data.other_tissue', sprintf('%0.2f', $form->other_tissue))
                ->where('data.exudate_amount', $form->exudate_amount)
                ->where('data.exudate_type', $form->exudate_type)
                ->where('data.other_exudate', $form->other_exudate)
                ->where('data.surrounding_skin', $form->surrounding_skin)
                ->where('data.other_surrounding', $form->other_surrounding)
                ->where('data.odor', $form->odor)
                ->where('data.pain', $form->pain)

                // Elimination
                ->where('data.bowel_habit', $form->bowel_habit)
                ->where('data.abnormal_option', $form->abnormal_option)
                ->where('data.fi_bowel', $form->fi_bowel)
                ->where('data.urinary_habit', $form->urinary_habit)
                ->where('data.fi_urine', $form->fi_urine)
                ->where('data.urine_device', $form->urine_device)
                ->where('data.catheter_type', $form->catheter_type)
                ->where('data.catheter_next_change', $form->catheter_next_change)
                ->where('data.catheter_size_fr', $form->catheter_size_fr)
                ->where('data.napkin_associated_desc', $form->napkin_associated_desc)
                // Pain
                ->where('data.is_pain', $form->is_pain)
        );
    }

    public function test_structure_form_physical_condition_success(){
        AssessmentCase::factory()->create(['id' => 1]);
        PhysicalConditionForm::factory()
            ->create(['id' => 1, 'assessment_case_id' => 1])
            ->each(function ($instance) {
                $instance->pains()
                    ->saveMany(PainSiteTable::factory(2)->make());
            });


        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "1?form_name=physical_condition");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                // General Condition
                'general_condition',
                'eye_opening_response',
                'verbal_response',
                'motor_response',
                'glasgow_score',

                // Mental State
                'mental_state',
                'edu_percentile',
                'moca_score',

                // Emotional State
                'emotional_state',
                'geriatric_score',

                // Sensory
                'is_good',
                'is_deaf',
                'dumb_left',
                'dumb_right',
                'is_visual_impaired',
                'blind_left',
                'blind_right',
                'is_assistive_devices',
                'denture',
                'hearing_aid',
                'glasses',

                // Nutrition
                'special_diet',
                'is_special_feeding',
                'special_feeding',
                'thickener_formula',
                'fluid_restriction',
                'tube_next_change',
                'milk_formula',
                'milk_regime',
                'feeding_person',
                'feeding_person_text',
                'feeding_technique',
                'ng_tube',

                // Skin Condition
                'is_napkin_associated',
                'is_dry',
                'is_cellulitis',
                'cellulitis_desc',
                'is_eczema',
                'eczema_desc',
                'is_scalp',
                'scalp_desc',
                'is_itchy',
                'itchy_desc',
                'is_wound',
                'wound_desc',
                'wound_size',
                'tunneling_time',
                'wound_bed',
                'granulating_tissue',
                'necrotic_tissue',
                'sloughy_tissue',
                'other_tissue',
                'exudate_amount',
                'exudate_type',
                'other_exudate',
                'surrounding_skin',
                'other_surrounding',
                'odor',
                'pain',

                // Elimination
                'abnormal_option',
                'fi_bowel',
                'fi_urine',
                'urine_device',
                'catheter_type',
                'catheter_next_change',
                'catheter_size_fr',

                // Pain
                'is_pain',
                'pains' => [
                    '*' => [
                        'is_dull',
                    ]
                ]
            ]
        ]);
    }

    // Qualtrics Form
    public function test_put_form_qualtrics_success()
    {
        $assessment_case = AssessmentCase::factory()->create();

        $data = [
            'is_cga' => true,
            'is_bzn' => true,
            'assessor_1' => $this->faker->word,
            'assessor_2' => $this->faker->word,

            // Health Professional
            // Chronic Disease History
            'no_chronic' => $this->faker->boolean,
            'is_hypertension' => $this->faker->boolean,
            'is_heart_disease' => $this->faker->boolean,
            'is_diabetes' => $this->faker->boolean,
            'is_high_cholesterol' => $this->faker->boolean,
            'is_copd' => $this->faker->boolean,
            'is_stroke' => $this->faker->boolean,
            'is_dementia' => $this->faker->boolean,
            'is_cancer' => $this->faker->boolean,
            'is_rheumatoid' => $this->faker->boolean,
            'is_osteoporosis' => $this->faker->boolean,
            'is_gout' => $this->faker->boolean,
            'is_depression' => $this->faker->boolean,
            'is_schizophrenia' => $this->faker->boolean,
            'is_enlarged_prostate' => $this->faker->boolean,
            'is_parkinson' => $this->faker->boolean,
            'is_other_disease' => $this->faker->boolean,
            'other_disease' => $this->faker->word,
            'no_followup' => $this->faker->boolean,
            'is_general_clinic' => $this->faker->boolean,
            'is_internal_medicine' => $this->faker->boolean,
            'is_cardiology' => $this->faker->boolean,
            'is_geriatric' => $this->faker->boolean,
            'is_endocrinology' => $this->faker->boolean,
            'is_gastroenterology' => $this->faker->boolean,
            'is_nephrology' => $this->faker->boolean,
            'is_dep_respiratory' => $this->faker->boolean,
            'is_surgical' => $this->faker->boolean,
            'is_psychiatry' => $this->faker->boolean,
            'is_private_doctor' => $this->faker->boolean,
            'is_oncology' => $this->faker->boolean,
            'is_orthopedics' => $this->faker->boolean,
            'is_urology' => $this->faker->boolean,
            'is_opthalmology' => $this->faker->boolean,
            'is_ent' => $this->faker->boolean,
            'is_other_followup' => $this->faker->boolean,
            'other_followup' => $this->faker->word,
            'never_surgery' => $this->faker->boolean,
            'is_aj_replace' => $this->faker->boolean,
            'is_cataract' => $this->faker->boolean,
            'is_cholecystectomy' => $this->faker->boolean,
            'is_malignant' => $this->faker->boolean,
            'is_colectomy' => $this->faker->boolean,
            'is_thyroidectomy' => $this->faker->boolean,
            'is_hysterectomy' => $this->faker->boolean,
            'is_thongbo' => $this->faker->boolean,
            'is_pacemaker' => $this->faker->boolean,
            'is_prostatectomy' => $this->faker->boolean,
            'is_other_surgery' => $this->faker->boolean,
            'other_surgery' => $this->faker->word,
            'left_ear' => $this->faker->randomElement([0, 1, 2]),
            'right_ear' => $this->faker->randomElement([0, 1, 2]),
            'left_eye' => $this->faker->randomElement([0, 1, 2]),
            'right_eye' => $this->faker->randomElement([0, 1, 2]),
            'hearing_aid' => $this->faker->randomElement([0, 1]),
            // 'walk_aid' => $this->faker->randomElement([1, 2, 3, 4, 5, 6]),
            'other_walk_aid' => $this->faker->word,
            'amsler_grid' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'cancer_text' => $this->faker->word,
            'stroke_text' => $this->faker->word,

            // Medication
            // 'om_regular' => $this->faker->boolean,
            'om_regular_desc' => $this->faker->word,
            // 'om_needed' => $this->faker->boolean,
            'om_needed_desc' => $this->faker->word,
            // 'tm_regular' => $this->faker->boolean,
            'tm_regular_desc' => $this->faker->word,
            // 'tm_needed' => $this->faker->boolean,
            'tm_needed_desc' => $this->faker->word,
            'not_prescribed_med' => $this->faker->word,
            'forget_med' => $this->faker->randomElement([0, 1, 2]),
            'missing_med' => $this->faker->randomElement([0, 1, 2]),
            'reduce_med' => $this->faker->randomElement([0, 1, 2]),
            'left_med' => $this->faker->randomElement([0, 1, 2]),
            'take_all_med' => $this->faker->randomElement([0, 1, 2]),
            'stop_med' => $this->faker->randomElement([0, 1, 2]),
            'annoyed_by_med' => $this->faker->randomElement([0, 1, 2]),
            'diff_rem_med' => $this->faker->randomElement(["1.00", "0.75", "0.50", "0.25", "0.00", "-1.00"]),

            // Pain
            'pain_semester' => $this->faker->randomElement([0, 1, 2, 3, 4, 5]),
            'other_pain_area' => $this->faker->word,
            'pain_level' => $this->faker->randomElement(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10']),
            'pain_level_text' => $this->faker->word,

            // Fall History and Hospitalization
            'have_fallen' => $this->faker->word,
            'adm_admitted' => $this->faker->word,
            // 'hosp_month' => $this->faker->word,
            // 'hosp_year' => $this->faker->word,
            // 'hosp_hosp' => $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
            // 'hosp_hosp_other' => $this->faker->word,
            // 'hosp_way' => $this->faker->randomElement([1, 2, 3, 4]),
            // 'hosp_home' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            // 'hosp_home_else' => $this->faker->word,
            // 'hosp_reason' => $this->faker->randomElement([1, 2, 3, 4, 5]),

            // Intervention Effectiveness Evaluation
            'ife_action' => $this->faker->randomElement([1, 2, 3, 4, 5, 6]),
            'ife_self_care' => $this->faker->randomElement([1, 2, 3, 4, 5, 6]),
            'ife_usual_act' => $this->faker->randomElement([1, 2, 3, 4, 5, 6]),
            'ife_discomfort' => $this->faker->randomElement([1, 2, 3, 4, 5, 6]),
            'ife_anxiety' => $this->faker->randomElement([1, 2, 3, 4, 5, 6]),
            'health_scales' => $this->faker->word,
            'health_scale_other' => $this->faker->word,

            // Qualtrics Form Physiological Measurement
            'rest15' => $this->faker->randomElement([1, 2]),
            'eathour' => $this->faker->randomElement([1, 2, 3]),
            // 'body_temperature1' => $this->faker->randomFloat(2, 0, 100),
            // 'body_temperature2' => $this->faker->randomFloat(2, 0, 100),
            // 'sit_upward1' => $this->faker->randomFloat(2, 0, 100),
            // 'sit_upward2' => $this->faker->randomFloat(2, 0, 100),
            // 'sit_depression1' => $this->faker->randomFloat(2, 0, 100),
            // 'sit_depression2' => $this->faker->randomFloat(2, 0, 100),
            // 'sta_upward1' => $this->faker->randomFloat(2, 0, 100),
            // 'sta_upward2' => $this->faker->randomFloat(2, 0, 100),
            // 'sta_depression1' => $this->faker->randomFloat(2, 0, 100),
            // 'sta_depression2' => $this->faker->randomFloat(2, 0, 100),
            // 'blood_ox1' => $this->faker->randomFloat(2, 0, 100),
            // 'blood_ox2' => $this->faker->randomFloat(2, 0, 100),
            // 'heartbeat1' => $this->faker->randomFloat(2, 0, 100),
            // 'heartbeat2' => $this->faker->randomFloat(2, 0, 100),
            // 'blood_glucose1' => $this->faker->randomFloat(2, 0, 100),
            // 'blood_glucose2' => $this->faker->randomFloat(2, 0, 100),
            // 'phy_kardia' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            // 'phy_waist' => $this->faker->randomFloat(2, 0, 100),
            // 'phy_weight' => $this->faker->randomFloat(2, 0, 100),
            // 'phy_height' => $this->faker->randomFloat(2, 0, 100),

            // Physiological Measurement
            'temperature' => $this->faker->randomFloat(2,27,47),
            'sitting_sbp' => $this->faker->numberBetween(10,20),
            'sitting_dbp' => $this->faker->numberBetween(10,20),
            'standing_sbp' => $this->faker->numberBetween(10,20),
            'standing_dbp' => $this->faker->numberBetween(10,20),
            'blood_oxygen' => $this->faker->numberBetween(1,2),
            'heart_rate' => $this->faker->numberBetween(1,2),
            'heart_rythm' => $this->faker->numberBetween(1,2),
            'kardia' => $this->faker->numberBetween(1,2),
            'blood_sugar' => $this->faker->randomFloat(2,10,20), //decimal
            'blood_sugar_time' => $this->faker->numberBetween(1,2),
            'waistline' => $this->faker->randomFloat(2,20,39),
            'weight' => $this->faker->randomFloat(2,20,29),
            'height' => $this->faker->randomFloat(2,2,3), //decimal
            'respiratory_rate' => $this->faker->numberBetween(1, 2),
            'abnormality' => $this->faker->numberBetween(1, 2),
            'other_abnormality' => $this->faker->word,
            'blood_options' => $this->faker->numberBetween(1,2),
            'blood_text' => $this->faker->word,
            'meal_text' => $this->faker->word,

            // Re Physiological Measurement
            're_temperature' => $this->faker->randomFloat(2,27,47),
            're_sitting_sbp' => $this->faker->numberBetween(10,20),
            're_sitting_dbp' => $this->faker->numberBetween(10,20),
            're_standing_sbp' => $this->faker->numberBetween(10,20),
            're_standing_dbp' => $this->faker->numberBetween(10,20),
            're_blood_oxygen' => $this->faker->numberBetween(1,2),
            're_heart_rate' => $this->faker->numberBetween(1,2),
            're_heart_rythm' => $this->faker->numberBetween(1,2),
            're_kardia' => $this->faker->numberBetween(1,2),
            're_blood_sugar' => $this->faker->randomFloat(2,10,20), //decimal
            're_blood_sugar_time' => $this->faker->numberBetween(1,2),
            're_waistline' => $this->faker->randomFloat(2,20,39),
            're_weight' => $this->faker->randomFloat(2,20,29),
            're_height' => $this->faker->randomFloat(2,2,3), //decimal
            're_respiratory_rate' => $this->faker->numberBetween(1, 2),
            're_blood_options' => $this->faker->numberBetween(1,2),
            're_blood_text' => $this->faker->word,
            're_meal_text' => $this->faker->word,

            // Fall Risk
            'timedup_test' => $this->faker->randomElement([1, 2]),
            'timedup_test_skip' => $this->faker->word,
            'timeup_device' => $this->faker->randomElement([1, 2, 3, 4]),
            'timedup_other' => $this->faker->word,
            'timedup_sec' => $this->faker->numberBetween(1, 10),
            // 'timedup_remark' => $this->faker->randomElement([1, 2, 3, 4]),
            'timedup_sec_desc' => $this->faker->word,
            'tr_none' => $this->faker->boolean,
            'tr_stopped' => $this->faker->boolean,
            'tr_impaired' => $this->faker->boolean,
            'tr_others' => $this->faker->boolean,
            'timeup_remark_others' => $this->faker->word,
            'singlestart_sts' => $this->faker->randomElement([1, 2]),
            'singlestart_skip' => $this->faker->word,
            'left_sts' => $this->faker->randomElement([1, 2, 3]),
            'right_sts' => $this->faker->randomElement([1, 2, 3]),

            // Qualtrics Remarks
            'qualtrics_remarks' => $this->faker->word,

            // Free Text
            'fallrisk_fa' => $this->faker->word,
            'fallrisk_rs' => $this->faker->word,
            'hosp_fa' => $this->faker->word,
            'hosp_rs' => $this->faker->word,
            'remark_fa' => $this->faker->word,
            'remark_rs' => $this->faker->word
        ];

        $response = $this->putJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=qualtrics", $data);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.assessor_1', $data['assessor_1'])
                ->where('data.assessor_2', $data['assessor_2'])

                // Chronic Disease History
                ->where('data.no_chronic', $data['no_chronic'])
                ->where('data.is_hypertension', $data['is_hypertension'])
                ->where('data.is_heart_disease', $data['is_heart_disease'])
                ->where('data.is_diabetes', $data['is_diabetes'])
                ->where('data.is_high_cholesterol', $data['is_high_cholesterol'])
                ->where('data.is_copd', $data['is_copd'])
                ->where('data.is_stroke', $data['is_stroke'])
                ->where('data.is_dementia', $data['is_dementia'])
                ->where('data.is_cancer', $data['is_cancer'])
                ->where('data.is_rheumatoid', $data['is_rheumatoid'])
                ->where('data.is_osteoporosis', $data['is_osteoporosis'])
                ->where('data.is_gout', $data['is_gout'])
                ->where('data.is_depression', $data['is_depression'])
                ->where('data.is_schizophrenia', $data['is_schizophrenia'])
                ->where('data.is_enlarged_prostate', $data['is_enlarged_prostate'])
                ->where('data.is_parkinson', $data['is_parkinson'])
                ->where('data.is_other_disease', $data['is_other_disease'])
                ->where('data.other_disease', $data['other_disease'])
                ->where('data.no_followup', $data['no_followup'])
                ->where('data.is_general_clinic', $data['is_general_clinic'])
                ->where('data.is_internal_medicine', $data['is_internal_medicine'])
                ->where('data.is_cardiology', $data['is_cardiology'])
                ->where('data.is_geriatric', $data['is_geriatric'])
                ->where('data.is_endocrinology', $data['is_endocrinology'])
                ->where('data.is_gastroenterology', $data['is_gastroenterology'])
                ->where('data.is_nephrology', $data['is_nephrology'])
                ->where('data.is_dep_respiratory', $data['is_dep_respiratory'])
                ->where('data.is_surgical', $data['is_surgical'])
                ->where('data.is_psychiatry', $data['is_psychiatry'])
                ->where('data.is_private_doctor', $data['is_private_doctor'])
                ->where('data.is_oncology', $data['is_oncology'])
                ->where('data.is_orthopedics', $data['is_orthopedics'])
                ->where('data.is_urology', $data['is_urology'])
                ->where('data.is_opthalmology', $data['is_opthalmology'])
                ->where('data.is_ent', $data['is_ent'])
                ->where('data.is_other_followup', $data['is_other_followup'])
                ->where('data.other_followup', $data['other_followup'])
                ->where('data.never_surgery', $data['never_surgery'])
                ->where('data.is_aj_replace', $data['is_aj_replace'])
                ->where('data.is_cataract', $data['is_cataract'])
                ->where('data.is_cholecystectomy', $data['is_cholecystectomy'])
                ->where('data.is_malignant', $data['is_malignant'])
                ->where('data.is_colectomy', $data['is_colectomy'])
                ->where('data.is_thyroidectomy', $data['is_thyroidectomy'])
                ->where('data.is_hysterectomy', $data['is_hysterectomy'])
                ->where('data.is_thongbo', $data['is_thongbo'])
                ->where('data.is_pacemaker', $data['is_pacemaker'])
                ->where('data.is_prostatectomy', $data['is_prostatectomy'])
                ->where('data.is_other_surgery', $data['is_other_surgery'])
                ->where('data.other_surgery', $data['other_surgery'])
                ->where('data.left_ear', $data['left_ear'])
                ->where('data.right_ear', $data['right_ear'])
                ->where('data.left_eye', $data['left_eye'])
                ->where('data.right_eye', $data['right_eye'])
                ->where('data.hearing_aid', $data['hearing_aid'])
                // ->where('data.walk_aid', $data['walk_aid'])
                ->where('data.other_walk_aid', $data['other_walk_aid'])
                ->where('data.amsler_grid', $data['amsler_grid'])
                ->where('data.abnormality', $data['abnormality'])
                ->where('data.other_abnormality', $data['other_abnormality'])
                ->where('data.cancer_text', $data['cancer_text'])
                ->where('data.stroke_text', $data['stroke_text'])

                // Medication
                // ->where('data.om_regular', $data['om_regular'])
                ->where('data.om_regular_desc', $data['om_regular_desc'])
                // ->where('data.om_needed', $data['om_needed'])
                ->where('data.om_needed_desc', $data['om_needed_desc'])
                // ->where('data.tm_regular', $data['tm_regular'])
                ->where('data.tm_regular_desc', $data['tm_regular_desc'])
                // ->where('data.tm_needed', $data['tm_needed'])
                ->where('data.tm_needed_desc', $data['tm_needed_desc'])
                ->where('data.not_prescribed_med', $data['not_prescribed_med'])
                ->where('data.forget_med', $data['forget_med'])
                ->where('data.missing_med', $data['missing_med'])
                ->where('data.reduce_med', $data['reduce_med'])
                ->where('data.left_med', $data['left_med'])
                ->where('data.take_all_med', $data['take_all_med'])
                ->where('data.stop_med', $data['stop_med'])
                ->where('data.annoyed_by_med', $data['annoyed_by_med'])
                ->where('data.diff_rem_med', $data['diff_rem_med'])

                // Pain
                ->where('data.pain_semester', $data['pain_semester'])
                ->where('data.other_pain_area', $data['other_pain_area'])
                ->where('data.pain_level', $data['pain_level'])
                ->where('data.pain_level_text', $data['pain_level_text'])

                // Fall History and Hospitalization
                ->where('data.have_fallen', $data['have_fallen'])
                ->where('data.adm_admitted', $data['adm_admitted'])
                // ->where('data.hosp_month', $data['hosp_month'])
                // ->where('data.hosp_year', $data['hosp_year'])
                // ->where('data.hosp_hosp', $data['hosp_hosp'])
                // ->where('data.hosp_hosp_other', $data['hosp_hosp_other'])
                // ->where('data.hosp_way', $data['hosp_way'])
                // ->where('data.hosp_home', $data['hosp_home'])
                // ->where('data.hosp_home_else', $data['hosp_home_else'])
                // ->where('data.hosp_reason', $data['hosp_reason'])

                // Intervention Effectiveness Evaluation
                ->where('data.ife_action', $data['ife_action'])
                ->where('data.ife_self_care', $data['ife_self_care'])
                ->where('data.ife_usual_act', $data['ife_usual_act'])
                ->where('data.ife_discomfort', $data['ife_discomfort'])
                ->where('data.ife_anxiety', $data['ife_anxiety'])
                ->where('data.health_scales', $data['health_scales'])
                ->where('data.health_scale_other', $data['health_scale_other'])

                // Qualtrics Form Physiological Measurement
                ->where('data.rest15', $data['rest15'])
                ->where('data.eathour', $data['eathour'])
                // ->where('data.body_temperature1', sprintf('%0.2f', $data['body_temperature1']))
                // ->where('data.body_temperature2', sprintf('%0.2f', $data['body_temperature2']))
                // ->where('data.sit_upward1', sprintf('%0.2f', $data['sit_upward1']))
                // ->where('data.sit_upward2', sprintf('%0.2f', $data['sit_upward2']))
                // ->where('data.sit_depression1', sprintf('%0.2f', $data['sit_depression1']))
                // ->where('data.sit_depression2', sprintf('%0.2f', $data['sit_depression2']))
                // ->where('data.sta_upward1', sprintf('%0.2f', $data['sta_upward1']))
                // ->where('data.sta_upward2', sprintf('%0.2f', $data['sta_upward2']))
                // ->where('data.sta_depression1', sprintf('%0.2f', $data['sta_depression1']))
                // ->where('data.sta_depression2', sprintf('%0.2f', $data['sta_depression2']))
                // ->where('data.blood_ox1', sprintf('%0.2f', $data['blood_ox1']))
                // ->where('data.blood_ox2', sprintf('%0.2f', $data['blood_ox2']))
                // ->where('data.heartbeat1', sprintf('%0.2f', $data['heartbeat1']))
                // ->where('data.heartbeat2', sprintf('%0.2f', $data['heartbeat2']))
                // ->where('data.blood_glucose1', sprintf('%0.2f', $data['blood_glucose1']))
                // ->where('data.blood_glucose2', sprintf('%0.2f', $data['blood_glucose2']))
                // ->where('data.phy_kardia', $data['phy_kardia'])
                // ->where('data.phy_waist', sprintf('%0.2f', $data['phy_waist']))
                // ->where('data.phy_weight', sprintf('%0.2f', $data['phy_weight']))
                // ->where('data.phy_height', sprintf('%0.2f', $data['phy_height']))
                
                // Physiological Measurement
                ->where('data.temperature', sprintf('%0.2f', $data['temperature']))
                ->where('data.sitting_sbp', $data['sitting_sbp'])
                ->where('data.sitting_dbp', $data['sitting_dbp'])
                ->where('data.standing_sbp', $data['standing_sbp'])
                ->where('data.standing_dbp', $data['standing_dbp'])
                ->where('data.blood_oxygen', $data['blood_oxygen'])
                ->where('data.heart_rate', $data['heart_rate'])
                ->where('data.kardia', $data['kardia'])
                ->where('data.blood_sugar', sprintf('%0.2f', $data['blood_sugar']))
                ->where('data.blood_sugar_time', $data['blood_sugar_time'])
                ->where('data.waistline', sprintf('%0.2f', $data['waistline']))
                ->where('data.weight', sprintf('%0.2f', $data['weight']))
                ->where('data.height', sprintf('%0.2f', $data['height']))
                ->where('data.respiratory_rate', $data['respiratory_rate'])
                ->where('data.blood_options', $data['blood_options'])
                ->where('data.blood_text', $data['blood_text'])
                ->where('data.meal_text', $data['meal_text'])

                // Re Physiological Measurement
                ->where('data.re_temperature', sprintf('%0.2f', $data['re_temperature']))
                ->where('data.re_sitting_sbp', $data['re_sitting_sbp'])
                ->where('data.re_sitting_dbp', $data['re_sitting_dbp'])
                ->where('data.re_standing_sbp', $data['re_standing_sbp'])
                ->where('data.re_standing_dbp', $data['re_standing_dbp'])
                ->where('data.re_blood_oxygen', $data['re_blood_oxygen'])
                ->where('data.re_heart_rate', $data['re_heart_rate'])
                ->where('data.re_kardia', $data['re_kardia'])
                ->where('data.re_blood_sugar', sprintf('%0.2f', $data['re_blood_sugar']))
                ->where('data.re_blood_sugar_time', $data['re_blood_sugar_time'])
                ->where('data.re_waistline', sprintf('%0.2f', $data['re_waistline']))
                ->where('data.re_weight', sprintf('%0.2f', $data['re_weight']))
                ->where('data.re_height', sprintf('%0.2f', $data['re_height']))
                ->where('data.re_respiratory_rate', $data['re_respiratory_rate'])
                ->where('data.re_blood_options', $data['re_blood_options'])
                ->where('data.re_blood_text', $data['re_blood_text'])
                ->where('data.re_meal_text', $data['re_meal_text'])

                // Fall Risk
                ->where('data.timedup_test', $data['timedup_test'])
                ->where('data.timedup_test_skip', $data['timedup_test_skip'])
                ->where('data.timeup_device', $data['timeup_device'])
                ->where('data.timedup_other', $data['timedup_other'])
                ->where('data.timedup_sec', $data['timedup_sec'])
                ->where('data.timedup_sec_desc', $data['timedup_sec_desc'])
                ->where('data.tr_none', $data['tr_none'])
                ->where('data.tr_stopped', $data['tr_stopped'])
                ->where('data.tr_impaired', $data['tr_impaired'])
                ->where('data.tr_others', $data['tr_others'])
                // ->where('data.timedup_remark', $data['timedup_remark'])
                ->where('data.timeup_remark_others', $data['timeup_remark_others'])
                ->where('data.singlestart_sts', $data['singlestart_sts'])
                ->where('data.singlestart_skip', $data['singlestart_skip'])
                ->where('data.left_sts', $data['left_sts'])
                ->where('data.right_sts', $data['right_sts'])

                // Qualtrics Remarks
                ->where('data.qualtrics_remarks', $data['qualtrics_remarks'])
                ->where('data.fallrisk_fa', $data['fallrisk_fa'])
                ->where('data.fallrisk_rs', $data['fallrisk_rs'])
                ->where('data.hosp_fa', $data['hosp_fa'])
                ->where('data.hosp_rs', $data['hosp_rs'])
                ->where('data.remark_fa', $data['remark_fa'])
                ->where('data.remark_rs', $data['remark_rs'])
        );
    }

    public function test_get_form_qualtrics_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $form = QualtricsForm::factory()
            ->for($assessment_case)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=qualtrics");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.assessor_1', $form->assessor_1)
                ->where('data.assessor_2', $form->assessor_2)

                // Chronic Disease History
                ->where('data.no_chronic', $form->no_chronic)
                ->where('data.is_hypertension', $form->is_hypertension)
                ->where('data.is_heart_disease', $form->is_heart_disease)
                ->where('data.is_diabetes', $form->is_diabetes)
                ->where('data.is_high_cholesterol', $form->is_high_cholesterol)
                ->where('data.is_copd', $form->is_copd)
                ->where('data.is_stroke', $form->is_stroke)
                ->where('data.is_dementia', $form->is_dementia)
                ->where('data.is_cancer', $form->is_cancer)
                ->where('data.is_rheumatoid', $form->is_rheumatoid)
                ->where('data.is_osteoporosis', $form->is_osteoporosis)
                ->where('data.is_gout', $form->is_gout)
                ->where('data.is_depression', $form->is_depression)
                ->where('data.is_schizophrenia', $form->is_schizophrenia)
                ->where('data.is_enlarged_prostate', $form->is_enlarged_prostate)
                ->where('data.is_parkinson', $form->is_parkinson)
                ->where('data.is_other_disease', $form->is_other_disease)
                ->where('data.other_disease', $form->other_disease)
                ->where('data.no_followup', $form->no_followup)
                ->where('data.is_general_clinic', $form->is_general_clinic)
                ->where('data.is_internal_medicine', $form->is_internal_medicine)
                ->where('data.is_cardiology', $form->is_cardiology)
                ->where('data.is_geriatric', $form->is_geriatric)
                ->where('data.is_endocrinology', $form->is_endocrinology)
                ->where('data.is_gastroenterology', $form->is_gastroenterology)
                ->where('data.is_nephrology', $form->is_nephrology)
                ->where('data.is_dep_respiratory', $form->is_dep_respiratory)
                ->where('data.is_surgical', $form->is_surgical)
                ->where('data.is_psychiatry', $form->is_psychiatry)
                ->where('data.is_private_doctor', $form->is_private_doctor)
                ->where('data.is_oncology', $form->is_oncology)
                ->where('data.is_orthopedics', $form->is_orthopedics)
                ->where('data.is_urology', $form->is_urology)
                ->where('data.is_opthalmology', $form->is_opthalmology)
                ->where('data.is_ent', $form->is_ent)
                ->where('data.is_other_followup', $form->is_other_followup)
                ->where('data.other_followup', $form->other_followup)
                ->where('data.never_surgery', $form->never_surgery)
                ->where('data.is_aj_replace', $form->is_aj_replace)
                ->where('data.is_cataract', $form->is_cataract)
                ->where('data.is_cholecystectomy', $form->is_cholecystectomy)
                ->where('data.is_malignant', $form->is_malignant)
                ->where('data.is_colectomy', $form->is_colectomy)
                ->where('data.is_thyroidectomy', $form->is_thyroidectomy)
                ->where('data.is_hysterectomy', $form->is_hysterectomy)
                ->where('data.is_thongbo', $form->is_thongbo)
                ->where('data.is_pacemaker', $form->is_pacemaker)
                ->where('data.is_prostatectomy', $form->is_prostatectomy)
                ->where('data.is_other_surgery', $form->is_other_surgery)
                ->where('data.other_surgery', $form->other_surgery)
                ->where('data.left_ear', $form->left_ear)
                ->where('data.right_ear', $form->right_ear)
                ->where('data.left_eye', $form->left_eye)
                ->where('data.right_eye', $form->right_eye)
                ->where('data.hearing_aid', $form->hearing_aid)
                // ->where('data.walk_aid', $form->walk_aid)
                ->where('data.other_walk_aid', $form->other_walk_aid)
                ->where('data.amsler_grid', $form->amsler_grid)

                // Medication
                // ->where('data.om_regular', $form->om_regular)
                ->where('data.om_regular_desc', $form->om_regular_desc)
                // ->where('data.om_needed', $form->om_needed)
                ->where('data.om_needed_desc', $form->om_needed_desc)
                // ->where('data.tm_regular', $form->tm_regular)
                ->where('data.tm_regular_desc', $form->tm_regular_desc)
                // ->where('data.tm_needed', $form->tm_needed)
                ->where('data.tm_needed_desc', $form->tm_needed_desc)
                ->where('data.not_prescribed_med', $form->not_prescribed_med)
                ->where('data.forget_med', $form->forget_med)
                ->where('data.missing_med', $form->missing_med)
                ->where('data.reduce_med', $form->reduce_med)
                ->where('data.left_med', $form->left_med)
                ->where('data.take_all_med', $form->take_all_med)
                ->where('data.stop_med', $form->stop_med)
                ->where('data.annoyed_by_med', $form->annoyed_by_med)
                ->where('data.diff_rem_med', $form->diff_rem_med)

                // Pain
                ->where('data.pain_semester', $form->pain_semester)
                ->where('data.other_pain_area', $form->other_pain_area)
                ->where('data.pain_level', $form->pain_level)
                ->where('data.pain_level_text', $form->pain_level_text)

                // Fall History and Hospitalization
                ->where('data.have_fallen', $form->have_fallen)
                ->where('data.adm_admitted', $form->adm_admitted)
                // ->where('data.hosp_month', $form->hosp_month)
                // ->where('data.hosp_year', $form->hosp_year)
                // ->where('data.hosp_hosp', $form->hosp_hosp)
                // ->where('data.hosp_hosp_other', $form->hosp_hosp_other)
                // ->where('data.hosp_way', $form->hosp_way)
                // ->where('data.hosp_home', $form->hosp_home)
                // ->where('data.hosp_home_else', $form->hosp_home_else)
                // ->where('data.hosp_reason', $form->hosp_reason)

                // Intervention Effectiveness Evaluation
                ->where('data.ife_action', $form->ife_action)
                ->where('data.ife_self_care', $form->ife_self_care)
                ->where('data.ife_usual_act', $form->ife_usual_act)
                ->where('data.ife_discomfort', $form->ife_discomfort)
                ->where('data.ife_anxiety', $form->ife_anxiety)
                ->where('data.health_scales', $form->health_scales)
                ->where('data.health_scale_other', $form->health_scale_other)

                // Qualtrics Form Physiological Measurement
                ->where('data.rest15', $form->rest15)
                ->where('data.eathour', $form->eathour)
                // ->where('data.body_temperature1', sprintf('%0.2f', $form->body_temperature1))
                // ->where('data.body_temperature2', sprintf('%0.2f', $form->body_temperature2))
                // ->where('data.sit_upward1', sprintf('%0.2f', $form->sit_upward1))
                // ->where('data.sit_upward2', sprintf('%0.2f', $form->sit_upward2))
                // ->where('data.sit_depression1', sprintf('%0.2f', $form->sit_depression1))
                // ->where('data.sit_depression2', sprintf('%0.2f', $form->sit_depression2))
                // ->where('data.sta_upward1', sprintf('%0.2f', $form->sta_upward1))
                // ->where('data.sta_upward2', sprintf('%0.2f', $form->sta_upward2))
                // ->where('data.sta_depression1', sprintf('%0.2f', $form->sta_depression1))
                // ->where('data.sta_depression2', sprintf('%0.2f', $form->sta_depression2))
                // ->where('data.blood_ox1', sprintf('%0.2f', $form->blood_ox1))
                // ->where('data.blood_ox2', sprintf('%0.2f', $form->blood_ox2))
                // ->where('data.heartbeat1', sprintf('%0.2f', $form->heartbeat1))
                // ->where('data.heartbeat2', sprintf('%0.2f', $form->heartbeat2))
                // ->where('data.blood_glucose1', sprintf('%0.2f', $form->blood_glucose1))
                // ->where('data.blood_glucose2', sprintf('%0.2f', $form->blood_glucose2))
                // ->where('data.phy_kardia', $form->phy_kardia)
                // ->where('data.phy_waist', sprintf('%0.2f', $form->phy_waist))
                // ->where('data.phy_weight', sprintf('%0.2f', $form->phy_weight))
                // ->where('data.phy_height', sprintf('%0.2f', $form->phy_height))

                // Physiological Measurement
                ->where('data.temperature', $form->temperature)
                ->where('data.sitting_sbp', $form->sitting_sbp)
                ->where('data.sitting_dbp', $form->sitting_dbp)
                ->where('data.standing_sbp', $form->standing_sbp)
                ->where('data.standing_dbp', $form->standing_dbp)
                ->where('data.blood_oxygen', $form->blood_oxygen)
                ->where('data.heart_rate', $form->heart_rate)
                ->where('data.kardia', $form->kardia)
                ->where('data.blood_sugar', sprintf('%0.2f', $form->blood_sugar))
                ->where('data.blood_sugar_time', $form->blood_sugar_time)
                ->where('data.waistline', $form->waistline)
                ->where('data.weight', $form->weight)
                ->where('data.height', sprintf('%0.2f', $form->height))
                ->where('data.respiratory_rate', $form->respiratory_rate)

                // Re Physiological Measurement
                ->where('data.re_temperature', $form->re_temperature)
                ->where('data.re_sitting_sbp', $form->re_sitting_sbp)
                ->where('data.re_sitting_dbp', $form->re_sitting_dbp)
                ->where('data.re_standing_sbp', $form->re_standing_sbp)
                ->where('data.re_standing_dbp', $form->re_standing_dbp)
                ->where('data.re_blood_oxygen', $form->re_blood_oxygen)
                ->where('data.re_heart_rate', $form->re_heart_rate)
                ->where('data.re_kardia', $form->re_kardia)
                ->where('data.re_blood_sugar', sprintf('%0.2f', $form->re_blood_sugar))
                ->where('data.re_blood_sugar_time', $form->re_blood_sugar_time)
                ->where('data.re_waistline', $form->re_waistline)
                ->where('data.re_weight', $form->re_weight)
                ->where('data.re_height', sprintf('%0.2f', $form->re_height))
                ->where('data.re_respiratory_rate', $form->re_respiratory_rate)

                // Fall Risk
                ->where('data.timedup_test', $form->timedup_test)
                ->where('data.timedup_test_skip', $form->timedup_test_skip)
                ->where('data.timeup_device', $form->timeup_device)
                ->where('data.timedup_other', $form->timedup_other)
                ->where('data.timedup_sec', $form->timedup_sec)
                ->where('data.timedup_sec_desc', $form->timedup_sec_desc)
                ->where('data.tr_none', $form->tr_none)
                ->where('data.tr_stopped', $form->tr_stopped)
                ->where('data.tr_impaired', $form->tr_impaired)
                ->where('data.tr_others', $form->tr_others)
                // ->where('data.timedup_remark', $form->timedup_remark)
                ->where('data.timeup_remark_others', $form->timeup_remark_others)
                ->where('data.singlestart_sts', $form->singlestart_sts)
                ->where('data.singlestart_skip', $form->singlestart_skip)
                ->where('data.left_sts', $form->left_sts)
                ->where('data.right_sts', $form->right_sts)

                // Qualtrics Remarks
                ->where('data.qualtrics_remarks', $form->qualtrics_remarks)
                ->where('data.fallrisk_fa', $form->fallrisk_fa)
                ->where('data.fallrisk_rs', $form->fallrisk_rs)
                ->where('data.hosp_fa', $form->hosp_fa)
                ->where('data.hosp_rs', $form->hosp_rs)
                ->where('data.remark_fa', $form->remark_fa)
                ->where('data.remark_rs', $form->remark_rs)
        );
    }

    public function test_structure_form_qualtrics_success()
    {
        AssessmentCase::factory()->create(['id' => 1]);
        QualtricsForm::factory()
            ->create(['id' => 1, 'assessment_case_id' => 1])
            ->each(function ($instance) {
                $instance->hospitalizationTables()
                    ->saveMany(HospitalizationTables::factory(2)->make());
            });


        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "1?form_name=qualtrics");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'adm_admitted',
                'hospitalization_tables' => [
                    '*' => [
                            'hosp_month',
                            'hosp_year',
                            'hosp_hosp',
                            'hosp_hosp_other',
                            'hosp_way',
                            'hosp_home',
                            'hosp_home_else',
                            'hosp_reason'
                    ]
                ],
                'walk_aids',
            ]
        ]);
    }

    // Social Worker Form
    public function test_put_form_social_worker_success()
    {
        $assessment_case = AssessmentCase::factory()->create();

        $data = [
            'is_cga' => true,
            'is_bzn' => true,
            'assessor_1' => $this->faker->word,
            'assessor_2' => $this->faker->word,

            // Social Worker
            // Elderly Information
            'elder_marital' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            // 'elder_living' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'elder_carer' => $this->faker->randomElement([1, 2, 3]),
            'elder_is_carer' => $this->faker->randomElement([1, 2, 3]),
            'elder_edu' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'elder_religious' => $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7, 8, 9]),
            'elder_housetype' => $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7, 8]),
            'elder_bell' => $this->faker->randomElement([1, 2, 3]),
            // 'elder_home_fall' => $this->faker->randomElement([1, 2, 3, 4, 5, 6]),
            // 'elder_home_hygiene' => $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7]),
            'elder_home_bug' => $this->faker->randomElement([1, 2]),

            // Social Service
            'elderly_center' => $this->faker->randomElement([1, 2]),
            // 'home_service' => $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7]),
            'elderly_daycare' => $this->faker->randomElement([1, 2]),
            'longterm_service' => $this->faker->randomElement([1, 2]),
            // 'life_support' => $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7]),
            'financial_support' => $this->faker->randomElement([1, 2]),

            // Lifestyle
            'spesific_program' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'high_cardio20' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'low_cardio40' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'recreation' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'streching3w' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'daily_workout' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'ate_fruit24' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'ate_veggie35' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'ate_dairy23' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'ate_protein23' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'have_breakfast' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'smoking_behavior' => $this->faker->randomElement([1, 2, 3]),
            'alcohol_frequent' => $this->faker->randomElement([1, 2, 3, 4, 5]),

            // Functional
            'diff_wearing' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'diff_bathing' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'diff_eating' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'diff_wakeup' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'diff_toilet' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'diff_urine' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'can_use_phone' => $this->faker->numberBetween(0, 2),
            'text_use_phone' => $this->faker->text,
            'can_take_ride' => $this->faker->numberBetween(0, 2),
            'text_take_ride' => $this->faker->text,
            'can_buy_food' => $this->faker->numberBetween(0, 2),
            'text_buy_food' => $this->faker->text,
            'can_cook' => $this->faker->numberBetween(0, 2),
            'text_cook' => $this->faker->text,
            'can_do_housework' => $this->faker->numberBetween(0, 2),
            'text_do_housework' => $this->faker->text,
            'can_do_repairment' => $this->faker->numberBetween(0, 2),
            'text_do_repairment' => $this->faker->text,
            'can_do_laundry' => $this->faker->numberBetween(0, 2),
            'text_do_laundry' => $this->faker->text,
            'can_take_medicine' => $this->faker->numberBetween(0, 2),
            'text_take_medicine' => $this->faker->text,
            'can_handle_finances' => $this->faker->numberBetween(0, 2),
            'text_handle_finances' => $this->faker->text,
            'iadl_total_score' => $this->faker->numberBetween(0, 18),

            // Cognitive
            // 'forget_stuff' => $this->faker->randomElement([1, 2, 3]),
            // 'forget_friend' => $this->faker->randomElement([1, 2, 3]),
            // 'forget_word' => $this->faker->randomElement([1, 2, 3]),
            // 'correct_word' => $this->faker->randomElement([1, 2, 3]),
            // 'bad_memory' => $this->faker->randomElement([1, 2, 3]),
            'moca_edu' => $this->faker->randomElement([1, 2, 3, 4, 5, 6]),

            // Psycho Social
            'relatives_sum' => $this->faker->numberBetween(0, 5),
            'relatives_to_talk' => $this->faker->numberBetween(0, 5),
            'relatives_to_help' => $this->faker->numberBetween(0, 5),
            'friends_sum' => $this->faker->numberBetween(0, 5),
            'friends_to_talk' => $this->faker->numberBetween(0, 5),
            'friends_to_help' => $this->faker->numberBetween(0, 5),
            'lubben_total_score' => $this->faker->numberBetween(0, 30),
            'genogram_done' => $this->faker->boolean,
            'less_friend' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'feel_ignored' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'feel_lonely' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'most_time_good_mood' => $this->faker->numberBetween(0, 1),
            'irritable_and_fidgety' => $this->faker->numberBetween(0, 1),
            'good_to_be_alive' => $this->faker->numberBetween(0, 1),
            'feeling_down' => $this->faker->numberBetween(0, 1),
            'gds4_score' => $this->faker->numberBetween(0, 4),

            // Stratification & Remark
            // 'do_referral' => $this->faker->randomElement([1, 2, 3, 4, 5, 6]),
            'diagnosed_dementia' => $this->faker->randomElement([0, 1]),
            'suggest' => $this->faker->randomElement([1, 2, 3]),
            'not_suitable' => $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7]),
            'sw_remark' => $this->faker->word,

            // Free Text
            'social_fa' => $this->faker->word,
            'social_rs' => $this->faker->word,
            'stratification_fa' => $this->faker->word,
            'stratification_rs' => $this->faker->word,
            'psycho_fa' => $this->faker->word,
            'psycho_rs' => $this->faker->word,
            'cognitive_fa' => $this->faker->word,
            'cognitive_rs' => $this->faker->word,


            // Some Text
            'elder_edu_text' => $this->faker->word,
            'elder_religious_text' => $this->faker->word,
            'elder_housetype_text' => $this->faker->word,
            'elder_home_fall_text' => $this->faker->word,
            'elder_home_hygiene_text' => $this->faker->word,
            'home_service_text' => $this->faker->word,

        ];

        $response = $this->putJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=social_worker", $data);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.assessor_1', $data['assessor_1'])
                ->where('data.assessor_2', $data['assessor_2'])

                // Social Worker
                // Elderly Information                
                ->where('data.elder_marital', $data['elder_marital'])
                // ->where('data.elder_living', $data['elder_living'])
                ->where('data.elder_carer', $data['elder_carer'])
                ->where('data.elder_is_carer', $data['elder_is_carer'])
                ->where('data.elder_edu', $data['elder_edu'])
                ->where('data.elder_religious', $data['elder_religious'])
                ->where('data.elder_housetype', $data['elder_housetype'])
                ->where('data.elder_bell', $data['elder_bell'])
                // ->where('data.elder_home_fall', $data['elder_home_fall'])
                // ->where('data.elder_home_hygiene', $data['elder_home_hygiene'])
                ->where('data.elder_home_bug', $data['elder_home_bug'])

                // Social Service
                ->where('data.elderly_center', $data['elderly_center'])
                // ->where('data.home_service', $data['home_service'])
                ->where('data.elderly_daycare', $data['elderly_daycare'])
                ->where('data.longterm_service', $data['longterm_service'])
                // ->where('data.life_support', $data['life_support'])
                ->where('data.financial_support', $data['financial_support'])

                // Lifestyle
                ->where('data.spesific_program', $data['spesific_program'])
                ->where('data.high_cardio20', $data['high_cardio20'])
                ->where('data.low_cardio40', $data['low_cardio40'])
                ->where('data.recreation', $data['recreation'])
                ->where('data.streching3w', $data['streching3w'])
                ->where('data.daily_workout', $data['daily_workout'])
                ->where('data.ate_fruit24', $data['ate_fruit24'])
                ->where('data.ate_veggie35', $data['ate_veggie35'])
                ->where('data.ate_dairy23', $data['ate_dairy23'])
                ->where('data.ate_protein23', $data['ate_protein23'])
                ->where('data.have_breakfast', $data['have_breakfast'])
                ->where('data.smoking_behavior', $data['smoking_behavior'])
                ->where('data.alcohol_frequent', $data['alcohol_frequent'])

                // Functional
                ->where('data.diff_wearing', $data['diff_wearing'])
                ->where('data.diff_bathing', $data['diff_bathing'])
                ->where('data.diff_eating', $data['diff_eating'])
                ->where('data.diff_wakeup', $data['diff_wakeup'])
                ->where('data.diff_toilet', $data['diff_toilet'])
                ->where('data.diff_urine', $data['diff_urine'])
                ->where('data.can_use_phone', $data['can_use_phone'])
                ->where('data.text_use_phone', $data['text_use_phone'])
                ->where('data.can_take_ride', $data['can_take_ride'])
                ->where('data.text_take_ride', $data['text_take_ride'])
                ->where('data.can_buy_food', $data['can_buy_food'])
                ->where('data.text_buy_food', $data['text_buy_food'])
                ->where('data.can_cook', $data['can_cook'])
                ->where('data.text_cook', $data['text_cook'])
                ->where('data.can_do_housework', $data['can_do_housework'])
                ->where('data.text_do_housework', $data['text_do_housework'])
                ->where('data.can_do_repairment', $data['can_do_repairment'])
                ->where('data.text_do_repairment', $data['text_do_repairment'])
                ->where('data.can_do_laundry', $data['can_do_laundry'])
                ->where('data.text_do_laundry', $data['text_do_laundry'])
                ->where('data.can_take_medicine', $data['can_take_medicine'])
                ->where('data.text_take_medicine', $data['text_take_medicine'])
                ->where('data.can_handle_finances', $data['can_handle_finances'])
                ->where('data.text_handle_finances', $data['text_handle_finances'])
                ->where('data.iadl_total_score', $data['iadl_total_score'])

                // Cognitive
                // ->where('data.forget_stuff', $data['forget_stuff'])
                // ->where('data.forget_friend', $data['forget_friend'])
                // ->where('data.forget_word', $data['forget_word'])
                // ->where('data.correct_word', $data['correct_word'])
                // ->where('data.bad_memory', $data['bad_memory'])
                ->where('data.moca_edu', $data['moca_edu'])

                // Psycho Social
                ->where('data.relatives_sum', $data['relatives_sum'])
                ->where('data.relatives_to_talk', $data['relatives_to_talk'])
                ->where('data.relatives_to_help', $data['relatives_to_help'])
                ->where('data.friends_sum', $data['friends_sum'])
                ->where('data.friends_to_talk', $data['friends_to_talk'])
                ->where('data.friends_to_help', $data['friends_to_help'])
                ->where('data.lubben_total_score', $data['lubben_total_score'])
                ->where('data.genogram_done', $data['genogram_done'])
                ->where('data.less_friend', $data['less_friend'])
                ->where('data.feel_ignored', $data['feel_ignored'])
                ->where('data.feel_lonely', $data['feel_lonely'])
                ->where('data.most_time_good_mood', $data['most_time_good_mood'])
                ->where('data.irritable_and_fidgety', $data['irritable_and_fidgety'])
                ->where('data.good_to_be_alive', $data['good_to_be_alive'])
                ->where('data.feeling_down', $data['feeling_down'])
                ->where('data.gds4_score', $data['gds4_score'])

                // Stratification & Remark
                // ->where('data.do_referral', $data['do_referral'])
                ->where('data.diagnosed_dementia', $data['diagnosed_dementia'])
                ->where('data.suggest', $data['suggest'])
                ->where('data.not_suitable', $data['not_suitable'])
                ->where('data.sw_remark', $data['sw_remark'])

                // Free Text
                ->where('data.social_fa', $data['social_fa'])
                ->where('data.social_rs', $data['social_rs'])
                ->where('data.stratification_fa', $data['stratification_fa'])
                ->where('data.stratification_rs', $data['stratification_rs'])
                ->where('data.psycho_fa', $data['psycho_fa'])
                ->where('data.psycho_rs', $data['psycho_rs'])
                ->where('data.cognitive_fa', $data['cognitive_fa'])
                ->where('data.cognitive_rs', $data['cognitive_rs'])

                // Some Text
                ->where('data.elder_edu_text', $data['elder_edu_text'])
                ->where('data.elder_religious_text', $data['elder_religious_text'])
                ->where('data.elder_housetype_text', $data['elder_housetype_text'])
                ->where('data.elder_home_fall_text', $data['elder_home_fall_text'])
                ->where('data.elder_home_hygiene_text', $data['elder_home_hygiene_text'])
                ->where('data.home_service_text', $data['home_service_text'])
        );
    }

    public function test_get_form_social_worker_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $form = SocialWorkerForm::factory()
            ->for($assessment_case)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=social_worker");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.assessor_1', $form->assessor_1)
                ->where('data.assessor_2', $form->assessor_2)

                // Social Worker
                // Elderly Information
                ->where('data.elder_marital', $form->elder_marital)
                // ->where('data.elder_living', $form->elder_living)
                ->where('data.elder_carer', $form->elder_carer)
                ->where('data.elder_is_carer', $form->elder_is_carer)
                ->where('data.elder_edu', $form->elder_edu)
                ->where('data.elder_religious', $form->elder_religious)
                ->where('data.elder_housetype', $form->elder_housetype)
                ->where('data.elder_bell', $form->elder_bell)
                // ->where('data.elder_home_fall', $form->elder_home_fall)
                // ->where('data.elder_home_hygiene', $form->elder_home_hygiene)
                ->where('data.elder_home_bug', $form->elder_home_bug)

                // Social Service
                ->where('data.elderly_center', $form->elderly_center)
                // ->where('data.home_service', $form->home_service)
                ->where('data.elderly_daycare', $form->elderly_daycare)
                ->where('data.longterm_service', $form->longterm_service)
                // ->where('data.life_support', $form->life_support)
                ->where('data.financial_support', $form->financial_support)

                // Lifestyle
                ->where('data.spesific_program', $form->spesific_program)
                ->where('data.high_cardio20', $form->high_cardio20)
                ->where('data.low_cardio40', $form->low_cardio40)
                ->where('data.recreation', $form->recreation)
                ->where('data.streching3w', $form->streching3w)
                ->where('data.daily_workout', $form->daily_workout)
                ->where('data.ate_fruit24', $form->ate_fruit24)
                ->where('data.ate_veggie35', $form->ate_veggie35)
                ->where('data.ate_dairy23', $form->ate_dairy23)
                ->where('data.ate_protein23', $form->ate_protein23)
                ->where('data.have_breakfast', $form->have_breakfast)
                ->where('data.smoking_behavior', $form->smoking_behavior)
                ->where('data.alcohol_frequent', $form->alcohol_frequent)

                // Functional
                ->where('data.diff_wearing', $form->diff_wearing)
                ->where('data.diff_bathing', $form->diff_bathing)
                ->where('data.diff_eating', $form->diff_eating)
                ->where('data.diff_wakeup', $form->diff_wakeup)
                ->where('data.diff_toilet', $form->diff_toilet)
                ->where('data.diff_urine', $form->diff_urine)
                ->where('data.can_use_phone', $form->can_use_phone)
                ->where('data.text_use_phone', $form->text_use_phone)
                ->where('data.can_take_ride', $form->can_take_ride)
                ->where('data.text_take_ride', $form->text_take_ride)
                ->where('data.can_buy_food', $form->can_buy_food)
                ->where('data.text_buy_food', $form->text_buy_food)
                ->where('data.can_cook', $form->can_cook)
                ->where('data.text_cook', $form->text_cook)
                ->where('data.can_do_housework', $form->can_do_housework)
                ->where('data.text_do_housework', $form->text_do_housework)
                ->where('data.can_do_repairment', $form->can_do_repairment)
                ->where('data.text_do_repairment', $form->text_do_repairment)
                ->where('data.can_do_laundry', $form->can_do_laundry)
                ->where('data.text_do_laundry', $form->text_do_laundry)
                ->where('data.can_take_medicine', $form->can_take_medicine)
                ->where('data.text_take_medicine', $form->text_take_medicine)
                ->where('data.can_handle_finances', $form->can_handle_finances)
                ->where('data.text_handle_finances', $form->text_handle_finances)
                ->where('data.iadl_total_score', $form->iadl_total_score)

                // Cognitive
                // ->where('data.forget_stuff', $form->forget_stuff)
                // ->where('data.forget_friend', $form->forget_friend)
                // ->where('data.forget_word', $form->forget_word)
                // ->where('data.correct_word', $form->correct_word)
                // ->where('data.bad_memory', $form->bad_memory)
                ->where('data.moca_edu', $form->moca_edu)

                // Psycho Social
                ->where('data.relatives_sum', $form->relatives_sum)
                ->where('data.relatives_to_talk', $form->relatives_to_talk)
                ->where('data.relatives_to_help', $form->relatives_to_help)
                ->where('data.friends_sum', $form->friends_sum)
                ->where('data.friends_to_talk', $form->friends_to_talk)
                ->where('data.friends_to_help', $form->friends_to_help)
                ->where('data.lubben_total_score', $form->lubben_total_score)
                ->where('data.genogram_done', $form->genogram_done)
                ->where('data.less_friend', $form->less_friend)
                ->where('data.feel_ignored', $form->feel_ignored)
                ->where('data.feel_lonely', $form->feel_lonely)
                ->where('data.most_time_good_mood', $form->most_time_good_mood)
                ->where('data.irritable_and_fidgety', $form->irritable_and_fidgety)
                ->where('data.good_to_be_alive', $form->good_to_be_alive)
                ->where('data.feeling_down', $form->feeling_down)
                ->where('data.gds4_score', $form->gds4_score)

                // Stratification & Remark
                // ->where('data.do_referral', $form->do_referral)
                ->where('data.diagnosed_dementia', $form->diagnosed_dementia)
                ->where('data.suggest', $form->suggest)
                ->where('data.not_suitable', $form->not_suitable)
                ->where('data.sw_remark', $form->sw_remark)

                // Free Text
                ->where('data.social_fa', $form->social_fa)
                ->where('data.social_rs', $form->social_rs)
                ->where('data.stratification_fa', $form->stratification_fa)
                ->where('data.stratification_rs', $form->stratification_rs)
                ->where('data.psycho_fa', $form->psycho_fa)
                ->where('data.psycho_rs', $form->psycho_rs)
                ->where('data.cognitive_fa', $form->cognitive_fa)
                ->where('data.cognitive_rs', $form->cognitive_rs)

                // Some Text
                ->where('data.elder_edu_text', $form->elder_edu_text)
                ->where('data.elder_religious_text', $form->elder_religious_text)
                ->where('data.elder_housetype_text', $form->elder_housetype_text)
                ->where('data.elder_home_fall_text', $form->elder_home_fall_text)
                ->where('data.elder_home_hygiene_text', $form->elder_home_hygiene_text)
                ->where('data.home_service_text', $form->home_service_text)
        );
    }

    public function test_structure_form_social_worker_success()
    {
        AssessmentCase::factory()->create(['id' => 1]);
        SocialWorkerForm::factory()
            ->create(['id' => 1, 'assessment_case_id' => 1])
            ->each(function ($instance) {
                $instance->doReferral()
                    ->saveMany(DoReferralTables::factory(2)->make());
                $instance->elderHomeFall()
                    ->saveMany(HomeFall::factory(2)->make());
                $instance->elderHomeHygiene()
                    ->saveMany(HomeHygiene::factory(2)->make());
                $instance->homeService()
                    ->saveMany(HomeService::factory(2)->make());
                $instance->lifeSupport()
                    ->saveMany(LifeSupport::factory(2)->make());
                $instance->elderLiving()
                    ->saveMany(ElderLiving::factory(2)->make());
            });

        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "1?form_name=social_worker");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'elder_marital',
                'do_referral' => [
                    '*' => [
                        'do_referral',
                    ]
                ],
                'elder_home_fall' => [
                    '*' => [
                        'elder_home_fall',
                    ]
                ],
                'elder_home_hygiene' => [
                    '*' => [
                        'elder_home_hygiene',
                    ]
                ], 
                'home_service' => [
                    '*' => [
                        'home_service',
                    ]
                ], 
                'life_support' => [
                    '*' => [
                        'life_support',
                    ]
                ], 
                'elder_living' => [
                    '*' => [
                        'elder_living',
                    ]
                ],
            ]
        ]);
    }

    // Case Status
    public function test_put_assessment_case_status_success()
    {
        $assessment_case = AssessmentCase::factory()->create();

        $data = [
            'is_cga' => true,
            'is_bzn' => true,
            'status' => $this->faker->randomNumber,
            'remarks' => $this->faker->text,
        ];

        $response = $this->putJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=assessment_case_status", $data);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.status', $data['status'])
                ->where('data.remarks', $data['remarks'])
        );
    }

    public function test_get_assessment_case_status_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $form = AssessmentCaseStatus::factory()
            ->for($assessment_case)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-forms/"
            . "{$assessment_case->id}?form_name=assessment_case_status");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $assessment_case->id)
                ->where('data.status', $form->status)
                ->where('data.remarks', $form->remarks)
        );
    }
}
