<?php

namespace App\Http\Requests;

use App\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

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
            return $this->getRulesForGet();
        }

        if ($this->isMethod('POST')) {
            return $this->getRulesForCreate();
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return $this->getRulesForUpdate();
        }

        return [];
    }

    /**
     * Trả về validate khi lọc, tìm kiếm người dùng.
     *
     * @return array{keyword: string[], per_page: string[], status: array<Enum|string>}
     */
    private function getRulesForGet(): array
    {
        return [
            'keyword'  => ['nullable', 'string', 'max:255'],
            'status'   => ['nullable', new Enum(UserStatus::class)],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Trả về validate khi tạo mới người dùng.
     *
     * @return array{email: array<string|\Illuminate\Validation\Rules\Unique>, name: string[], password: string[]}
     */
    private function getRulesForCreate(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', 'min:6'],
        ];
    }

    /**
     * Trả về validate khi cập nhật người dùng
     *
     * @return array{email: array<string|\Illuminate\Validation\Rules\Unique>, name: string[], password: string[]}
     */
    private function getRulesForUpdate(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'confirmed', 'min:6'],
        ];
    }
}
