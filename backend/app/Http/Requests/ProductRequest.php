<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        $product = $this->route('product');
        $productId = is_object($product) ? $product->id : $product;

        return [
            'category_id' => [$isUpdate ? 'sometimes' : 'required', 'exists:categories,id'],
            'name'        => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'barcode'     => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                Rule::unique('products', 'barcode')->ignore($productId),
            ],
            'description' => ['nullable', 'string'],
            'price'       => [$isUpdate ? 'sometimes' : 'required', 'numeric', 'min:1'],
            'min_stock'   => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
            'allergens'   => ['nullable', 'array'],
            'allergens.*' => ['exists:allergens,id'],
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => 'A categoria é obrigatória.',
            'category_id.exists'   => 'A categoria informada não existe.',
            'name.required'        => 'O nome do produto é obrigatório.',
            'barcode.required'     => 'O código de barras é obrigatório.',
            'barcode.unique'       => 'Este código de barras já está cadastrado em outro produto.',
            'price.required'       => 'O preço do produto é obrigatório.',
            'price.numeric'        => 'O preço deve ser um valor numérico válido.',
            'allergens.*.exists'   => 'Um ou mais alergênicos informados não existem.',
        ];
    }
}
