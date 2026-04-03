<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Track\StoreTrackRequest;
use App\Http\Resources\TrackResource;
use App\Models\Release;
use App\Models\Track;
use App\Services\TrackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

final class TrackController extends Controller
{
    public function __construct(
        private readonly TrackService $service,
    ) {}

    /**
     * List tracks for a release
     *
     * Returns all tracks for the given release.
     */
    public function index(Release $release): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', [Track::class, $release]);

        $tracks = $this->service->listForRelease($release);

        return TrackResource::collection($tracks);
    }

    /**
     * Add a track to a release
     *
     * Creates a new track record for the release. Accepts multipart/form-data with an audio file.
     */
    public function store(StoreTrackRequest $request, Release $release): JsonResponse
    {
        Gate::authorize('create', [Track::class, $release]);

        $dto = $request->toDto();

        // Handle file upload
        $uploadedFile = $request->file('file');
        if ($uploadedFile instanceof \Illuminate\Http\UploadedFile) {
            $path = $uploadedFile->store("tracks/{$release->key}", 'public');
            $dto = new \App\DataTransferObjects\Track\CreateTrackData(
                title: $dto->title,
                format: $dto->format,
                track_number: $dto->track_number,
                duration_seconds: $dto->duration_seconds,
                file_url: $path !== false ? "/storage/{$path}" : $dto->file_url,
                file_size: (int) $uploadedFile->getSize(),
                authors: $dto->authors,
                composers: $dto->composers,
                lyrics: $dto->lyrics,
                isrc: $dto->isrc,
            );
        }

        $track = $this->service->create($release, $dto);

        $track->setRelation('release', $release);

        return TrackResource::make($track)
            ->response()
            ->setStatusCode(201)
            ->header('Location', "/api/v1/releases/{$release->key}/tracks/{$track->key}");
    }

    /**
     * Remove a track from a release
     *
     * Deletes a track. Only allowed when release is in draft or rejected status.
     */
    public function destroy(Release $release, Track $track): JsonResponse
    {
        Gate::authorize('delete', [Track::class, $release]);

        $this->service->delete($release, $track);

        return response()->json(null, 204);
    }
}
