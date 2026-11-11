<?php

namespace App\Repositories;

use App\Exceptions\AddressNotFoundException;
use App\Models\Address;
use Illuminate\Database\Eloquent\Collection;

class AddressRepository
{
    public function index(int $userId): Collection
    {
        return Address::where('user_id', $userId)->get();
    }

    public function find(int $id): Address
    {
        return Address::find($id) ?? throw new AddressNotFoundException();
    }

    public function findByUser(int $userId, int $addressId): Address
    {
        return Address::where('user_id', $userId)->find($addressId)
            ?? throw new AddressNotFoundException();
    }

    public function create(array $addressData): Address
    {
        return Address::create($addressData);
    }

    public function update(int $id, array $addressData): Address
    {
        $address = $this->find($id);
        
        $address->update($addressData);

        return $address->refresh();
    }

    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}