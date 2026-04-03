<?php

declare(strict_types=1);

namespace App\Http\Requests\ServiceCatalog;

use App\DataTransferObjects\ServiceCatalog\UpdateServiceCatalogData;
use App\Enums\ServiceCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateServiceCatalogRequest extends FormRequest
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
            'description' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'price' => ['sometimes', 'numeric', 'min:0', 'max:99999999.99'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'category' => ['sometimes', 'string', Rule::in(ServiceCategory::values())],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function toDto(): UpdateServiceCatalogData
    {
        return UpdateServiceCatalogData::from($this->validated());
    }
}
