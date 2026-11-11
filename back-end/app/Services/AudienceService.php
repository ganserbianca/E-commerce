<?php

namespace App\Services;

use App\Models\Audience;
use App\Repositories\AudienceRepository;
use Illuminate\Database\Eloquent\Collection;

class AudienceService
{
    public function __construct(
        private readonly AudienceRepository $audienceRepository
    ) {}

    public function index(): Collection
    {
        return $this->audienceRepository->index();
    }

    public function show(int $id): Audience
    {
        return $this->audienceRepository->find($id);
    }

    public function create(array $audienceData): Audience
    {
        return $this->audienceRepository->create($audienceData);
    }

    public function update(int $id, array $audienceData): Audience
    {
        return $this->audienceRepository->update($id, $audienceData);
    }

    public function delete(int $id): bool
    {
        return $this->audienceRepository->delete($id);
    }
}