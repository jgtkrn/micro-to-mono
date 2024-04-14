<?php

namespace App\Http\Requests\v2\Elders;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMeetingNotesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'note_content' => 'nullable|string',
            'updated_by' => 'nullable|integer',
            'updated_by_name' => 'nullable|string',
        ];
    }
}
