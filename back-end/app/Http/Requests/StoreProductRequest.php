<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id'    => ['required', 'integer', 'exists:categories,id'],
            'brand_id'       => ['required', 'integer', 'exists:brands,id'],
            'name'           => ['required', 'string', 'max:150'],
            'description'    => ['nullable', 'string'],
            'price'          => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'image'          => ['nullable', 'string', 'max:255'],
            'available'      => ['required', 'boolean'],
            'audience_ids'   => ['sometimes', 'array'],
            'audience_ids.*' => ['integer', 'distinct', 'exists:audiences,id'],
        ];
    }
}