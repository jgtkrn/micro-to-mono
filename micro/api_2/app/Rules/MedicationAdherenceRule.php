<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\AssessmentCase;

class MedicationAdherenceRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($assessment_case_id)
    {
        $this->assessment_case_id = $assessment_case_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value) {
            $assessment_case = AssessmentCase::find($this->assessment_case_id);
            $medication_adherence = $assessment_case->medicationAdherenceForm()->first();
            if (!$medication_adherence) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Medication adherence must exist if has_medication checked';
    }
}
