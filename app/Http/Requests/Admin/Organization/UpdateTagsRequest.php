<?php

namespace App\Http\Requests\Admin\Organization;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTagsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('edit records');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'longevity' => 'boolean|nullable',
            'parent_tag' => 'required_if:longevity,1|nullable',
            'child_tags' => 'array|nullable',
        ];
    }

    public function messages()
    {
        return [
            'parent_tag.required_if' => 'The parent tag field is required.',
        ];
    }
}
