<?php

namespace App\Features\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user()->id)],
            'country' => ['nullable', 'string', 'max:100'],
            'preferences' => ['nullable', 'array'],
            'avatar' => ['nullable', 'image', 'max:2048'], // 2MB max
        ];
    }
}
