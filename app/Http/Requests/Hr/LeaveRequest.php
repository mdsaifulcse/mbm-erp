<?php

namespace App\Http\Requests\Hr;

use Illuminate\Foundation\Http\FormRequest;

class LeaveRequest extends FormRequest
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
            'leave_ass_id'            => 'required',
            'leave_type'              => 'required',
            'leave_days'              => 'required|array|min:1',
            'leave_days.*'            => 'required|date',
            'leave_to'                => 'date',
            'leave_applied_date'      => 'required|date',
            'leave_supporting_file'   => 'mimes:docx,doc,pdf,jpg,png,jpeg|max:1024',
        ];
    }
}
