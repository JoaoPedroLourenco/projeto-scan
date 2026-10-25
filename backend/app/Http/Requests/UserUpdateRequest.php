<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user()->id)],
            'cpf' => ['nullable', 'string', 'digits:11', Rule::unique('users', 'cpf')->ignore($this->user()->id)],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Informe o seu e-mail.',
            'email.unique' => 'Este e-mail já está cadastrado em nosso sistema.',
            'cpf.digits' => 'O CPF deve conter exatamente 11 dígitos numéricos.',
            'cpf.unique' => 'Este CPF já está cadastrado em nosso sistema.',
        ];
    }
}