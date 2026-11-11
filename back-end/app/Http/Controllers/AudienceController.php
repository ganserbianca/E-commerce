<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAudienceRequest;
use App\Http\Requests\UpdateAudienceRequest;
use App\Http\Resources\AudienceResource;
use App\Services\AudienceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AudienceController extends Controller
{
    public function __construct(
        private readonly AudienceService $audienceService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return AudienceResource::collection($this->audienceService->index());
    }

    public function show(int $id): AudienceResource
    {
        return new AudienceResource($this->audienceService->show($id));
    }

    public function store(StoreAudienceRequest $request): JsonResponse
    {
        $audience = $this->audienceService->create($request->validated());

        return (new AudienceResource($audience))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateAudienceRequest $request, int $id): AudienceResource
    {
        $audience = $this->audienceService->update($id, $request->validated());

        return new AudienceResource($audience);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->audienceService->delete($id);

        return response()->json([
            'message' => 'Público-alvo excluído com sucesso.',
        ]);
    }
}