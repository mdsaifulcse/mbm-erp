<?php

namespace App\Http\Requests\Hr;

use Illuminate\Foundation\Http\FormRequest;

class ShiftUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'hr_shift_start_time' => 'required',
            'hr_shift_start_date' => 'required',
            'hr_shift_end_time' => 'required',
            'hr_shift_break_time' => 'required',
            'hr_default_break_start' => 'required'
        ];
    }
}
