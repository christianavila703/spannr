<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrganizationRequest extends FormRequest
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
                Rule::unique('organizations')->ignore($this->name, 'name'),
                'max:255'
            ],
            'type' => 'required',
            'website' => 'url|nullable',
            'summary' => 'string|max:255|nullable',
            'founded_date' => 'date_format:Y|nullable',
            'facebook' => 'string|nullable',
            'twitter' => 'string|nullable',
            'linkedin' => 'string|nullable',
            'instagram' => 'string|nullable',
            'description' => 'string|nullable',
            ''
        ];
    }
}
