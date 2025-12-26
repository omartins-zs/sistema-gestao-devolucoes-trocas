<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessarReembolsoRequest extends FormRequest
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
            'metodo' => [
                'required',
                'string',
                Rule::in(['credito_estorno', 'transferencia', 'boleto', 'pix']),
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
            'metodo.required' => 'O método de reembolso é obrigatório.',
            'metodo.in' => 'O método deve ser: credito_estorno, transferencia, boleto ou pix.',
            'observacoes.max' => 'As observações devem ter no máximo 1000 caracteres.',
        ];
    }
}
