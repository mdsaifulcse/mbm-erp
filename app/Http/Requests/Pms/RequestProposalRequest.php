<?php

namespace App\Http\Requests\Pms;

use Illuminate\Foundation\Http\FormRequest;

class RequestProposalRequest extends FormRequest
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
            'request_date' => ['required', 'date'],
            'reference_no' => 'required|max:15|unique:request_proposals',

            "supplier_id"    => "required|array|min:1",
            "supplier_id.*"  => "exists:suppliers,id",
            "product_id"    => "required|array|min:1",
            "product_id.*"  => "exists:products,id",
        ];
    }
}
