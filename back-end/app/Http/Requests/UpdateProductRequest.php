<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id'    => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'brand_id'       => ['sometimes', 'required', 'integer', 'exists:brands,id'],
            'name'           => ['sometimes', 'required', 'string', 'max:150'],
            'description'    => ['sometimes', 'nullable', 'string'],
            'price'          => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999999.99'],
            'stock_quantity' => ['sometimes', 'required', 'integer', 'min:0'],
            'image'          => ['sometimes', 'nullable', 'string', 'max:255'],
            'available'      => ['sometimes', 'required', 'boolean'],
            'audience_ids'   => ['sometimes', 'array'],
            'audience_ids.*' => ['integer', 'distinct', 'exists:audiences,id'],
        ];
    }
}