<?php

declare(strict_types=1);

namespace App\Http\Requests\Notification;

use App\DataTransferObjects\Notification\CreateNotificationData;
use App\Enums\NotificationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreNotificationRequest extends FormRequest
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
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'type' => ['required', 'string', Rule::in(NotificationType::values())],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'data' => ['sometimes', 'nullable', 'array'],
        ];
    }

    public function toDto(): CreateNotificationData
    {
        return CreateNotificationData::from($this->validated());
    }
}
