<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockExitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stock_batch_id' => ['required', 'exists:stock_batches,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'stock_batch_id.required' => 'O lote é obrigatório',
            'stock_batch_id.exists' => 'O lote não existe',
            'quantity.required' => 'A quantidade é obrigatória',
            'quantity.integer' => 'A quantidade deve ser um número inteiro',
            'quantity.min' => 'A quantidade deve ser maior ou igual a 1',
            'reason.string' => 'O motivo deve ser um texto',
            'notes.string' => 'A observação deve ser um texto',
        ];
    }
}