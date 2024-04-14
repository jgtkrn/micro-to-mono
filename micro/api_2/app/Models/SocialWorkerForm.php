<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="SocialWorkerForm",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="assessor_1",
 *          type="string",
 *          example="John Doe"
 *     ),
 * 
 *     @OA\Property(
 *          property="assessor_2",
 *          type="string",
 *          example="John Cena"
 *     ),
 * 
 *     @OA\Property(
 *          property="elder_marital",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="elder_living",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="elder_carer",
 *          type="enum",
 *          format="1, 2, 3",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="elder_is_carer",
 *          type="enum",
 *          format="1, 2, 3",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="elder_edu",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="elder_religious",
 *          type="enum",
 *          format="1, 2, 3, 4, 5, 6, 7, 8, 9",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="elder_housetype",
 *          type="enum",
 *          format="1, 2, 3, 4, 5, 6, 7, 8",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="elder_bell",
 *          type="enum",
 *          format="1, 2, 3",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="elder_home_fall",
 *          type="enum",
 *          format="1, 2, 3, 4, 5, 6",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="elder_home_hygiene",
 *          type="enum",
 *          format="1, 2, 3, 4, 5, 6, 7",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="elder_home_bug",
 *          type="enum",
 *          format="1, 2",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="elderly_center",
 *          type="enum",
 *          format="1, 2",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="home_service",
 *          type="enum",
 *          format="1, 2, 3, 4, 5, 6, 7",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="elderly_daycare",
 *          type="enum",
 *          format="1, 2",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="longterm_service",
 *          type="enum",
 *          format="1, 2",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="life_support",
 *          type="enum",
 *          format="1, 2, 3, 4, 5, 6, 7",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="financial_support",
 *          type="enum",
 *          format="1, 2",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="spesific_program",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="high_cardio20",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="low_cardio40",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="recreation",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="streching3w",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="daily_workout",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="ate_fruit24",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="ate_veggie35",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="ate_dairy23",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="ate_protein23",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="have_breakfast",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="smoking_behavior",
 *          type="enum",
 *          format="1, 2, 3",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="alcohol_frequent",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="diff_wearing",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="diff_bathing",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="diff_eating",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="diff_wakeup",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="diff_toilet",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="diff_urine",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="can_use_phone",
 *          type="integer",
 *          example="1"
 *     ),
 *     
 *     @OA\Property(
 *          property="text_use_phone",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="can_take_ride",
 *          type="integer",
 *          example="1"
 *     ),
 *  
 *     @OA\Property(
 *          property="text_take_ride",
 *          type="string",
 *          example="yes"
 *     ),
 *
 *      @OA\Property(
 *          property="can_buy_food",
 *          type="integer",
 *          example="1"
 *     ),
 *  
 *     @OA\Property(
 *          property="text_buy_food",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="can_cook",
 *          type="integer",
 *          example="1"
 *     ),
 *  
 *     @OA\Property(
 *          property="text_cook",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="can_do_housework",
 *          type="integer",
 *          example="1"
 *     ),
 *  
 *     @OA\Property(
 *          property="text_do_housework",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="can_do_repairment",
 *          type="integer",
 *          example="1"
 *     ),
 *  
 *     @OA\Property(
 *          property="text_repairment",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="can_do_laundry",
 *          type="integer",
 *          example="1"
 *     ),
 *
 *     @OA\Property(
 *          property="text_do_laundry",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="can_take_medicine",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="text_take_medicine",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="can_handle_finances",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="text_handle_finances",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="iadl_total_score",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="moca_edu",
 *          type="enum",
 *          format="1, 2, 3, 4, 5, 6",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="relatives_sum",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="relatives_to_talk",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="relatives_to_help",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="friends_sum",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="friends_to_talk",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="friends_to_help",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="lubben_total_score",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="genogram_done",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="less_friend",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="feel_ignored",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="feel_lonely",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="most_time_good_mood",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="irritable_and_fidgety",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="good_to_be_alive",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="feeling_down",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="gds4_score",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="do_referral",
 *          type="enum",
 *          format="1, 2, 3, 4, 5, 5",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="diagnosed_dementia",
 *          type="enum",
 *          format="0, 1",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="suggest",
 *          type="enum",
 *          format="1, 2, 3",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="not_suitable",
 *          type="enum",
 *          format="1, 2, 3, 4, 5, 6, 7",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="sw_remark",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="elder_edu_text",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="elder_religious_text",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="elder_housetype_text",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="elder_home_fall_text",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="elder_home_hygiene_text",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="home_service_text",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="social_fa",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="social_rs",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="stratification_fa",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="stratification_rs",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="psycho_fa",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="psycho_rs",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="cognitive_fa",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="cognitive_rs",
 *          type="string",
 *          example="yes"
 *     ),
 * )
 */


class SocialWorkerForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
            'assessment_case_id' => 'integer',

            // Social Worker
            // Elderly Information
            'elder_marital' => 'integer',
            // 'elder_living' => 'integer',
            'elder_carer' => 'integer',
            'elder_is_carer' => 'integer',
            'elder_edu' => 'integer',
            'elder_religious' => 'integer',
            'elder_housetype' => 'integer',
            'elder_bell' => 'integer',
            // 'elder_home_fall' => 'integer',
            // 'elder_home_hygiene' => 'integer',
            'elder_home_bug' => 'integer',

            // Social Service
            'elderly_center' => 'integer',
            // 'home_service' => 'integer',
            'elderly_daycare' => 'integer',
            'longterm_service' => 'integer',
            // 'life_support' => 'integer',
            'financial_support' => 'integer',

            // Lifestyle
            'spesific_program' => 'integer',
            'high_cardio20' => 'integer',
            'low_cardio40' => 'integer',
            'recreation' => 'integer',
            'streching3w' => 'integer',
            'daily_workout' => 'integer',
            'ate_fruit24' => 'integer',
            'ate_veggie35' => 'integer',
            'ate_dairy23' => 'integer',
            'ate_protein23' => 'integer',
            'have_breakfast' => 'integer',
            'smoking_behavior' => 'integer',
            'alcohol_frequent' => 'integer',

            // Functional
            'diff_wearing' => 'integer',
            'diff_bathing' => 'integer',
            'diff_eating' => 'integer',
            'diff_wakeup' => 'integer',
            'diff_toilet' => 'integer',
            'diff_urine' => 'integer',
            'can_use_phone' => 'integer',
            'can_take_ride' => 'integer',
            'can_buy_food' => 'integer',
            'can_cook' => 'integer',
            'can_do_housework' => 'integer',
            'can_do_repairment' => 'integer',
            'can_do_laundry' => 'integer',
            'can_take_medicine' => 'integer',
            'can_handle_finances' => 'integer',
            'iadl_total_score' => 'integer',

            // Cognitive
            // 'forget_stuff' => 'integer',
            // 'forget_friend' => 'integer',
            // 'forget_word' => 'integer',
            // 'correct_word' => 'integer',
            // 'bad_memory' => 'integer',
            'moca_edu' => 'integer',

            // Psycho Social
            'relatives_sum' => 'integer',
            'relatives_to_talk' => 'integer',
            'relatives_to_help' => 'integer',
            'friends_sum' => 'integer',
            'friends_to_talk' => 'integer',
            'friends_to_help' => 'integer',
            'lubben_total_score' => 'integer',
            'genogram_done' => 'boolean',
            'less_friend' => 'integer',
            'feel_ignored' => 'integer',
            'feel_lonely' => 'integer',
            'most_time_good_mood' => 'integer',
            'irritable_and_fidgety' => 'integer',
            'good_to_be_alive' => 'integer',
            'feeling_down' => 'integer',
            'gds4_score' => 'integer',

            // Stratification & Remark
            'do_referral' => 'integer',
            'diagnosed_dementia' => 'integer',
            'suggest' => 'integer',
            'not_suitable' => 'integer',
            'social_5' => 'integer',
    ];
    protected $with = [
        'elderHomeFall', 
        'elderHomeHygiene', 
        'homeService',
        'lifeSupport',
        'elderLiving', 
        'doReferral'
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }

    public function doReferral()
    {
        return $this->hasMany(DoReferralTables::class);
    }

    public function elderHomeFall()
    {
        return $this->hasMany(HomeFall::class);
    }

    public function elderHomeHygiene()
    {
        return $this->hasMany(HomeHygiene::class);
    }

    public function homeService()
    {
        return $this->hasMany(HomeService::class);
    }

    public function lifeSupport()
    {
        return $this->hasMany(LifeSupport::class);
    }

    public function elderLiving()
    {
        return $this->hasMany(ElderLiving::class);
    }

    public function delete()
    {
        DoReferralTables::where('social_worker_form_id', $this->id)->delete();
        return parent::delete();
    }

}
