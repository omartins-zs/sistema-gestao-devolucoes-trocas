<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AutorizarReembolsoRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'autorizado' => [
                'required',
                'boolean',
            ],
            'observacoes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'autorizado.required' => 'A decisão de autorização é obrigatória.',
            'autorizado.boolean' => 'A autorização deve ser verdadeira ou falsa.',
            'observacoes.max' => 'As observações devem ter no máximo 1000 caracteres.',
        ];
    }
}
