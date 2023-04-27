<?php

namespace App\Http\Requests\Admin\Articles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->can('edit articles');
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
                Rule::unique('articles')->ignore($this->name, 'name'),
                'max:255'
            ],
            'slug' => [
                'required',
                Rule::unique('articles')->ignore($this->slug, 'slug'),
                'max:255'
            ],
            'status' => 'required',
            'date' => 'required|date',
            'excerpt' => 'string|nullable',
            'page_title' => 'string|nullable|max:255',
            'meta_description' => 'string|max:250|nullable',
            'content' => 'required',
            'sources' => 'nullable',
            'sidebar' => 'nullable',
            'authors' => 'array|required',
            'tags' => 'array|nullable',
        ];
    }
}
