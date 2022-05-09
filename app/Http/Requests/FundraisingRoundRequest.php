<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FundraisingRoundRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'round' => 'required|string',
            'organization_id' => 'required|int',
            'date' => 'required|date',
            'amount_raised' => 'nullable|int',
            'announcement_url' => 'nullable|url',
            'description' => 'nullable|string',
            'valuation' => 'nullable|int',
            'investors' => 'nullable|array'
        ];
    }
}
