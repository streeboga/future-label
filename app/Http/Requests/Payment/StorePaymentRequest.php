<?php

declare(strict_types=1);

namespace App\Http\Requests\Payment;

use App\DataTransferObjects\Payment\CreatePaymentData;
use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StorePaymentRequest extends FormRequest
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
            'method' => ['required', 'string', Rule::in(PaymentMethod::values())],
            'return_url' => ['sometimes', 'nullable', 'string', 'url', 'max:2048'],
        ];
    }

    public function toDto(): CreatePaymentData
    {
        return CreatePaymentData::from($this->validated());
    }
}
