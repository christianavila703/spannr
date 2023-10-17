<?php

namespace App\Http\Requests\Admin\Locations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocationRequest extends FormRequest
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
            'name' => [
                'required',
                Rule::unique('locations')->ignore($this->location, 'slug'),
                'max:255'
            ],
            'longitude' => 'required',
            'latitude' => 'required',
        ];
    }
}
