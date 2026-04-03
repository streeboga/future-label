<?php

declare(strict_types=1);

namespace App\Http\Requests\Contract;

use App\DataTransferObjects\Contract\CreateContractData;
use App\Models\Release;
use Illuminate\Foundation\Http\FormRequest;

final class StoreContractRequest extends FormRequest
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

    public function toDto(): CreateContractData
    {
        /** @var Release $release */
        $release = $this->route('release');

        return new CreateContractData(
            release_id: $release->id,
            template_version: $this->validated('template_version', '1.0'),
        );
    }
}
