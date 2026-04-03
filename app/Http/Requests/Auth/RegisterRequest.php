<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\DataTransferObjects\User\CreateUserData;
use Illuminate\Foundation\Http\FormRequest;

final class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return CreateUserData::rules();
    }

    public function toDto(): CreateUserData
    {
        return CreateUserData::from($this->validated());
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->name ? strip_tags(trim((string) $this->name)) : null,
            'email' => $this->email ? mb_strtolower(trim((string) $this->email)) : null,
        ]);
    }
}
