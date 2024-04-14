<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'doReferral',
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
