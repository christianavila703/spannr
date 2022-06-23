<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
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
                Rule::unique('events'),
                'max:255'
            ],
            'start_date' => 'required|date',
            'end_date' => 'date|nullable',
            'website' => 'required',
            'description' => 'required',
            'image' => 'image|nullable',
            'organizations' => 'array|nullable',
            'people' => 'array|nullable',
            'investors' => 'array|nullable',
            'event_types' => 'array|required',
            'remote' => 'integer|nullable'
        ];
    }
}
