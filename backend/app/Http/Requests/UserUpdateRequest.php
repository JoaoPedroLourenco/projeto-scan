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
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user()->id)],
            'cpf' => ['nullable', 'string', 'digits:11', Rule::unique('users', 'cpf')->ignore($this->user()->id)],
            'phone' => ['nullable', 'string', 'digits:11'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'Informe o seu e-mail.',
            'email.unique' => 'Este e-mail já está cadastrado em nosso sistema.',
            'cpf.digits' => 'O CPF deve conter exatamente 11 dígitos numéricos.',
            'cpf.unique' => 'Este CPF já está cadastrado em nosso sistema.',
            'phone.digits' => 'O telefone deve conter 11 dígitos com DDD.',
        ];
    }
}