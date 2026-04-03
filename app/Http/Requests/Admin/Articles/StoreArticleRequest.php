<?php

namespace App\Http\Requests\Admin\Articles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
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
                Rule::unique('articles'),
                'max:255'
            ],
            'slug' => [
                'required',
                Rule::unique('articles'),
                'max:255'
            ],
            'status' => 'required',
            'date' => 'required',
            'excerpt' => 'required',
            'page_title' => 'string|nullable|max:255',
            'seo_title' => 'nullable|string|max:255',
            'meta_description' => 'required|max:250',
            'faq' => 'nullable|array',
            'faq.*.question' => 'nullable|string|max:5000',
            'faq.*.answer' => 'nullable|string|max:50000',
            'content' => 'required',
            'sources' => 'nullable',
            'sidebar' => 'nullable',
            'authors' => 'array|required',
            'tags' => 'array|nullable',
        ];
    }
}
