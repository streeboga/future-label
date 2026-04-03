<?php

declare(strict_types=1);

namespace App\Http\Requests\Notification;

use App\DataTransferObjects\Notification\UpdateNotificationData;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateNotificationRequest extends FormRequest
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
            'read_at' => ['sometimes', 'nullable', 'date'],
        ];
    }

    public function toDto(): UpdateNotificationData
    {
        return UpdateNotificationData::from($this->validated());
    }
}
