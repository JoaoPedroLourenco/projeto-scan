<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'batch_code' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
            'expiration_date' => ['required', 'date', 'after:today'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'O produto é obrigatório',
            'product_id.exists' => 'O produto não existe',
            'batch_code.required' => 'O código é obrigatório',
            'batch_code.string' => 'O código não é valido',
            'quantity.required' => 'A quantidade é obrigatória',
            'quantity.integer' => 'A quantidade deve ser um número inteiro',
            'quantity.min' => 'A quantidade deve ser maior que 0',
            'expiration_date.required' => 'A data de validade é obrigatória',
            'expiration_date.after' => 'A data de validade precisa ser futura',
            'unit_price.required' => 'O preço é obrigatório',
            'unit_price.numeric' => 'O preço deve ser um número',
            'unit_price.min' => 'O preço mínimo é 0',
            'reason.string' => 'O motivo não é valido',
            'notes.string' => 'A observação não é valida',
        ];
    }
}