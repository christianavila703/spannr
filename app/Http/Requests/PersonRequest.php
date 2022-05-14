<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PersonRequest extends FormRequest
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
            'name' => [
                'required',
                Rule::unique('people')->ignore($this->name, 'name'),
                'max:255'
            ],
            'byline' => 'string|max:255|nullable',
            'job_type' => 'string|nullable',
            'website' => 'url|nullable',
            'bio' => 'string|nullable',
            'facebook' => 'string|nullable',
            'twitter' => 'string|nullable',
            'linkedin' => 'string|nullable',
            'instagram' => 'string|nullable',
            'google_scholar' => 'string|nullable',
            'image' => 'image|nullable'
        ];
    }
}
