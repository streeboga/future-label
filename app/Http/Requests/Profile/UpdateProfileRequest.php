<?php

declare(strict_types=1);

namespace App\Http\Requests\Profile;

use App\DataTransferObjects\User\UpdateProfileData;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateProfileRequest extends FormRequest
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
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'stage_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'regex:/^\+?[0-9]{10,15}$/'],
            'telegram' => ['sometimes', 'nullable', 'string', 'regex:/^@[a-zA-Z0-9_]{4,32}$/'],
            'passport_data' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'bank_details' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }

    public function toDto(): UpdateProfileData
    {
        return UpdateProfileData::from($this->validated());
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->has('name') && is_string($this->name)) {
            $merge['name'] = strip_tags(trim($this->name));
        }

        if ($this->has('stage_name') && is_string($this->stage_name)) {
            $merge['stage_name'] = strip_tags(trim($this->stage_name));
        }

        if (! empty($merge)) {
            $this->merge($merge);
        }
    }
}
