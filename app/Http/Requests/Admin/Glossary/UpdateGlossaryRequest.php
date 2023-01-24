<?php

namespace App\Http\Requests\Admin\Glossary;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateGlossaryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
                Rule::unique('glossary')->ignore($this->name, 'name'),
                'max:255'
            ],
            'slug' => [
                'required',
                Rule::unique('glossary')->ignore($this->name, 'name'),
                'max:255'
            ],
            'excerpt' => 'nullable',
            'content' => 'nullable',
        ];
    }
}
