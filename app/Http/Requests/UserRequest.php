<?php

namespace App\Http\Requests;

use App\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('GET')) {
            return [
                'keyword'  => ['nullable', 'string', 'max:255'],
                'status'   => ['nullable', 'in:' . implode(',', array_column(UserStatus::cases(), 'value'))],
                'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            ];
        }
        $isUpdate = $this->route('user') !== null;
        $userId   = $this->route('user')?->id;

        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                $isUpdate
                    ? Rule::unique('users', 'email')->ignore($userId)
                    : Rule::unique('users', 'email'),
            ],
            'password' => [
                $isUpdate ? 'nullable' : 'required',
                'confirmed',
                'min:6',
            ],
        ];
    }
}
