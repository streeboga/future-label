<?php

declare(strict_types=1);

namespace App\Http\Requests\Payment;

use App\DataTransferObjects\Payment\UpdatePaymentData;
use Illuminate\Foundation\Http\FormRequest;

final class UpdatePaymentRequest extends FormRequest
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
            'receipt_url' => ['sometimes', 'nullable', 'string', 'url', 'max:2048'],
        ];
    }

    public function toDto(): UpdatePaymentData
    {
        return UpdatePaymentData::from($this->validated());
    }
}
