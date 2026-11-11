<?php

namespace App\Repositories;

use App\Exceptions\AudienceNotFoundException;
use App\Models\Audience;
use Illuminate\Database\Eloquent\Collection;

class AudienceRepository
{
    public function index(): Collection
    {
        return Audience::all();
    }

    public function find(int $id): Audience
    {
        return Audience::find($id) ?? throw new AudienceNotFoundException();
    }

    public function create(array $audienceData): Audience
    {
        return Audience::create($audienceData);
    }

    public function update(int $id, array $audienceData): Audience
    {
        $audience = $this->find($id);
        $audience->update($audienceData);

        return $audience->refresh();
    }

    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}