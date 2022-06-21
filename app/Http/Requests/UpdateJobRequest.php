<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJobRequest extends FormRequest
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
                Rule::unique('jobs')->ignore($this->id),
                'max:255'
            ],
            'type' => 'required',
            'category_id' => 'required|integer',
            'status' => 'required',
            'owner_id' => 'required',
            'description' => 'required',
            'posted_date' => 'required',
            'exp_date' => 'date|nullable',
            'salary' => 'nullable|integer',
            'hourly' => 'nullable|numeric',
            'url' => 'nullable|url',
        ];
    }
}
