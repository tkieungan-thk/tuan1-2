<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
/** 
 * @package App\Http\Requests
 */
class UpdatePasswordRequest extends FormRequest
{
    protected $errorBag = 'updatePassword';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * @property-read string $current_password
     * @property-read string $password
     * @property-read string $password_confirmation
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => __('validation.required', ['attribute' => __('auth.current_password')]),
            'current_password.current_password' => __('auth.current_password_invalid'),
            'password.required' => __('validation.required', ['attribute' => __('auth.new_password')]),
            'password.confirmed' => __('validation.confirmed', ['attribute' => __('auth.new_password')]),
        ];
    }
}
