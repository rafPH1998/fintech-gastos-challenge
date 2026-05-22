<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalvarDespesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxAllowedDate = now()->addDay()->format('Y-m-d');

        return [
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'date' => ['required', 'date', 'before_or_equal:'.$maxAllowedDate],
            'expenseCategoryId' => [
                'required',
                'integer',
                Rule::exists('expense_categories', 'id')->where('user_id', $this->user()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'A descrição é obrigatória.',
            'amount.required' => 'O valor é obrigatório.',
            'amount.gt' => 'O valor deve ser maior que zero.',
            'date.required' => 'A data é obrigatória.',
            'date.before_or_equal' => 'A data não pode ser futura em mais de 1 dia.',
            'expenseCategoryId.required' => 'A categoria é obrigatória.',
            'expenseCategoryId.exists' => 'A categoria selecionada não pertence ao seu usuário.',
        ];
    }
}
