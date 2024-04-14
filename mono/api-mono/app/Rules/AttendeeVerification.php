<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AttendeeVerification implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($elder_id)
    {
        $this->elder_id = $elder_id;
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
        $no_elder = [3, 5]; //value 3 is internal meeting, value 5 is on leave

        if (in_array($value, $no_elder) && $this->elder_id != null) {
            return false;
        } elseif (! in_array($value, $no_elder) && $this->elder_id == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Assessment (1), Face-to-face Consultation (2), and Tele-consultation (4) must have elder. Internal meeting (3) and On Leave (5) must not have elder';
    }
}
