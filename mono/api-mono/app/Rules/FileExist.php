<?php

namespace App\Rules;

use App\Models\v2\Appointments\File;
use Illuminate\Contracts\Validation\Rule;

class FileExist implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($attachment_ids)
    {
        $this->attachment_ids = $attachment_ids;
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
        $files = File::findMany($this->attachment_ids);
        if (count($value) == count($files)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Attachment files not found';
    }
}
