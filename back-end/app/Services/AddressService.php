<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cep' => ['required', 'string', 'max:9'],
            'street' => ['required', 'string', 'max:150'],
            'number' => ['required', 'string', 'max:10'],
            'complement' => ['nullable', 'string', 'max:100'],
            'neighborhood' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:150'],
            'uf' => ['required', 'string', 'size:2'],
        ];
    }
}
