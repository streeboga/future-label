<?php

declare(strict_types=1);

namespace App\Http\Requests\Track;

use App\DataTransferObjects\Track\CreateTrackData;
use App\Enums\TrackFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreTrackRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'format' => ['required', 'string', Rule::in(TrackFormat::values())],
            'track_number' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:30'],
            'duration_seconds' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:7200'],
            'file' => ['sometimes', 'file', 'mimetypes:audio/mpeg,audio/wav,audio/x-wav,audio/flac,audio/aac,audio/ogg,application/octet-stream', 'max:102400'],
            'file_url' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'file_size' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'authors' => ['sometimes', 'nullable', 'string', 'max:500'],
            'composers' => ['sometimes', 'nullable', 'string', 'max:500'],
            'lyrics' => ['sometimes', 'nullable', 'string', 'max:10000'],
            'isrc' => ['sometimes', 'nullable', 'string', 'max:20'],
        ];
    }

    public function toDto(): CreateTrackData
    {
        return CreateTrackData::from($this->validated());
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->has('title') && is_string($this->title)) {
            $merge['title'] = strip_tags(trim($this->title));
        }

        if (! empty($merge)) {
            $this->merge($merge);
        }
    }
}
