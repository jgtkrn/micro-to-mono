<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialWorkerForm>
 */
class SocialWorkerFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
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
            'elder_living_text' => $this->faker->word,
            'elder_religious_text' => $this->faker->word,
            'elder_housetype_text' => $this->faker->word,
            'elder_home_fall_text' => $this->faker->word,
            'elder_home_hygiene_text' => $this->faker->word,
            'home_service_text' => $this->faker->word,
            'referral_other_text' => $this->faker->word,
        ];
    }
}
