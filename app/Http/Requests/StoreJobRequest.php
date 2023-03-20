<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobRequest extends FormRequest
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

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:255'
            ],
            'type' => 'required',
            'category_id' => 'required|integer',
            'status' => 'required',
            'owner_id' => 'required',
            'owner_type' => 'required',
            'posted_date' => 'required',
            'exp_date' => 'date|nullable',
            'salary_min' => 'required|integer',
            'salary_max' => 'nullable|integer',
            'hourly' => 'nullable|numeric',
            'url' => 'required|url',
            'remote' => 'integer|nullable',
            'description' => 'required',
            'qualifications' => 'nullable'
        ];
    }
}
