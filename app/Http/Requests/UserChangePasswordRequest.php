<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserChangePasswordRequest extends FormRequest
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
            'password' => 'required|same:password_confirm',
            'password_confirm' => 'required',
            'currentPassword' => 'required|different:new_password',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'password.required' => 'You have to enter your new password.',
            'password_confirm.required' => 'You have to confirm your new password.',
            'password.same' => 'Your new password did not match your confirmation.',
            'currentPassword.required' => 'You need to validate yourself by entering your current password.',
            'currentPassword.different' => 'Your new password is the same as the old one...',
        ];
    }
}
