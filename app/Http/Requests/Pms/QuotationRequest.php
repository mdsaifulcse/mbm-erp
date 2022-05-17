<?php

namespace App\Http\Requests\Pms;

use Illuminate\Foundation\Http\FormRequest;

class QuotationRequest extends FormRequest
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
            'quotation_date' => ['required', 'date'],
            'reference_no' => 'required|max:15|unique:quotations',
            "supplier_id"    => "required",
            "supplier_id.*"  => "exists:suppliers,id",
            "request_proposal_id"    => "required",
            "request_proposal_id.*"  => "exists:request_proposals,id",
            'sum_of_subtoal' => 'required|max:15',
            'discount' => 'nullable|max:15',
            'vat' => 'nullable|max:15',
            'gross_price' => 'required|max:15',
            'type' => 'required|in:online,manual',
        ];
    }
}
