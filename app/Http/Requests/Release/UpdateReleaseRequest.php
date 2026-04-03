<?php

declare(strict_types=1);

namespace App\Http\Requests\Release;

use App\DataTransferObjects\Release\UpdateReleaseData;
use App\Enums\ReleaseType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateReleaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', Rule::in(ReleaseType::values())],
            'artist_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'genre' => ['sometimes', 'nullable', 'string', 'max:100'],
            'language' => ['sometimes', 'nullable', 'string', 'max:10'],
            'description' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'release_date' => ['sometimes', 'nullable', 'date', 'after:today'],
            'cover_url' => ['sometimes', 'nullable', 'string', 'url', 'max:2048'],
            'metadata' => ['sometimes', 'nullable', 'array'],
        ];
    }

    public function toDto(): UpdateReleaseData
    {
        return UpdateReleaseData::from($this->validated());
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->has('title') && is_string($this->title)) {
            $merge['title'] = strip_tags(trim($this->title));
        }

        if ($this->has('artist_name') && is_string($this->artist_name)) {
            $merge['artist_name'] = strip_tags(trim($this->artist_name));
        }

        if (! empty($merge)) {
            $this->merge($merge);
        }
    }
}
