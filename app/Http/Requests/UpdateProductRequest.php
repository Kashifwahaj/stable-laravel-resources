<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $this->route('product'), // Ignore SKU uniqueness for the current product
            'image' => 'nullable|url',
        ];
    }

    /**
     * Get the custom messages for validation errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The product name is required.',
            'description.required' => 'The description is required.',
            'price.required' => 'The price of the product is required.',
            'stock_quantity.required' => 'The stock quantity is required.',
            'sku.unique' => 'The SKU must be unique.',
            'image.url' => 'The image must be a valid URL.',
        ];
    }
}
