<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use App\DataTransferObjects\Order\CreateOrderData;
use App\Models\Release;
use App\Models\ServiceCatalog;
use Illuminate\Foundation\Http\FormRequest;

final class StoreOrderRequest extends FormRequest
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
            'service_key' => ['required', 'string', 'exists:services,key'],
            'release_key' => ['sometimes', 'nullable', 'string', 'exists:releases,key'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:5000'],
        ];
    }

    public function toDto(): CreateOrderData
    {
        /** @var ServiceCatalog $service */
        $service = ServiceCatalog::where('key', $this->validated('service_key'))->firstOrFail();

        $releaseId = null;
        if ($this->validated('release_key')) {
            /** @var Release $release */
            $release = Release::where('key', $this->validated('release_key'))->firstOrFail();
            $releaseId = $release->id;
        }

        return new CreateOrderData(
            service_id: $service->id,
            release_id: $releaseId,
            notes: $this->validated('notes'),
        );
    }
}
