<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim((string) $this->email)),
            'name' => trim((string) $this->name),
            'cpf' => $this->cpf ? preg_replace('/\D/', '', (string) $this->cpf) : null,
            'phone' => $this->phone ? preg_replace('/\D/', '', (string) $this->phone) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'cpf' => ['nullable', 'string', 'digits:11', 'unique:users,cpf'],
            'phone' => ['nullable', 'string', 'digits:11'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->numbers()->symbols(),
            ],
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
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'As senhas digitadas não coincidem.',
            'password.min'       => 'A senha deve ter no mínimo 8 caracteres.',
            'password.letters'   => 'A senha deve conter ao menos uma letra.',
            'password.symbols'   => 'A senha deve conter ao menos um símbolo.',
        ];
    }
}