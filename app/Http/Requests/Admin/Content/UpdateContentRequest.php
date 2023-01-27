<?php

namespace App\Http\Requests\Admin\Content;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->can('edit records');
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
                Rule::unique('content')->ignore($this->name, 'name'),
                'max:255'
            ],
            'slug' => [
                'required',
                Rule::unique('content')->ignore($this->name, 'name'),
                'max:255'
            ],
            'status' => 'required',
            'meta_description' => 'required',
            'content' => 'required',
            'sidebar' => 'nullable',
            'tags' => 'nullable|array'
        ];
    }
}
