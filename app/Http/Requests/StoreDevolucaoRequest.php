<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDevolucaoRequest extends FormRequest
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
            'pedido_item_id' => [
                'required',
                'integer',
                'exists:pedido_items,id',
            ],
            'quantidade' => [
                'required',
                'integer',
                'min:1',
            ],
            'motivo' => [
                'required',
                'string',
                'min:10',
                'max:1000',
            ],
            'tipo' => [
                'nullable',
                'string',
                Rule::in(['devolucao', 'troca']),
            ],
            'produto_troca_id' => [
                'required_if:tipo,troca',
                'nullable',
                'integer',
                'exists:produtos,id',
                function ($attribute, $value, $fail) {
                    if ($this->input('tipo') === 'troca' && !$value) {
                        $fail('O produto de troca é obrigatório quando o tipo é troca.');
                    }
                    if ($this->input('tipo') === 'troca' && $value == $this->input('produto_id')) {
                        $fail('O produto de troca deve ser diferente do produto devolvido.');
                    }
                },
            ],
            'motivo_troca' => [
                'required_if:tipo,troca',
                'nullable',
                'string',
                'min:10',
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
            'pedido_item_id.required' => 'O item do pedido é obrigatório.',
            'pedido_item_id.exists' => 'O item do pedido informado não existe.',
            'quantidade.required' => 'A quantidade é obrigatória.',
            'quantidade.integer' => 'A quantidade deve ser um número inteiro.',
            'quantidade.min' => 'A quantidade deve ser no mínimo 1.',
            'motivo.required' => 'O motivo da devolução é obrigatório.',
            'motivo.min' => 'O motivo deve ter no mínimo 10 caracteres.',
            'motivo.max' => 'O motivo deve ter no máximo 1000 caracteres.',
            'tipo.in' => 'O tipo deve ser "devolucao" ou "troca".',
            'produto_troca_id.required_if' => 'O produto de troca é obrigatório quando o tipo é troca.',
            'produto_troca_id.exists' => 'O produto de troca informado não existe.',
            'motivo_troca.required_if' => 'O motivo da troca é obrigatório quando o tipo é troca.',
            'motivo_troca.min' => 'O motivo da troca deve ter no mínimo 10 caracteres.',
            'motivo_troca.max' => 'O motivo da troca deve ter no máximo 1000 caracteres.',
        ];
    }
}
