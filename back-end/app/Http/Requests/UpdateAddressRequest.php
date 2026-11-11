<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cep'          => ['sometimes', 'required', 'string', 'max:9'],
            'street'       => ['sometimes', 'required', 'string', 'max:150'],
            'number'       => ['sometimes', 'required', 'string', 'max:10'],
            'complement'   => ['sometimes', 'nullable', 'string', 'max:100'],
            'neighborhood' => ['sometimes', 'required', 'string', 'max:100'],
            'city'         => ['sometimes', 'required', 'string', 'max:150'],
            'uf'           => ['sometimes', 'required', 'string', 'size:2'],
        ];
    }
}