<?php

namespace App\Services;

use App\Models\Address;
use App\Repositories\AddressRepository;
use Illuminate\Database\Eloquent\Collection;

class AddressService
{
    public function __construct(
        private readonly AddressRepository $addressRepository
    ) {}

    public function index(int $userId): Collection
    {
        return $this->addressRepository->index($userId);
    }

    public function show(int $userId, int $addressId): Address
    {
        return $this->addressRepository->findByUser(
            $userId,
            $addressId
        );
    }

    public function create(int $userId, array $addressData): Address
    {
        $addressData['user_id'] = $userId;

        $address = $this->addressRepository->create($addressData);

        return $this->show($userId, $address->id);
    }

    public function update(int $userId,int $addressId,array $addressData
    ): Address 
    {
        $this->show($userId, $addressId);

        $address = $this->addressRepository->update(
            $addressId,
            $addressData
        );

        return $this->show($userId, $address->id);
    }

    public function delete(int $userId, int $addressId): bool
    {
        $this->show($userId, $addressId);

        return $this->addressRepository->delete($addressId);
    }
}
