<?php

namespace App\Http\Requests;

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
            'meta_description' => 'required|max:160',
            'content' => 'required',
            'sources' => 'nullable'
        ];
    }
}
