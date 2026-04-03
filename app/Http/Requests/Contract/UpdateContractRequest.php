<?php

declare(strict_types=1);

namespace App\Http\Requests\Contract;

use App\DataTransferObjects\Contract\UpdateContractData;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateContractRequest extends FormRequest
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
            'template_version' => ['sometimes', 'string', 'max:50'],
        ];
    }

    public function toDto(): UpdateContractData
    {
        return UpdateContractData::from($this->validated());
    }
}
