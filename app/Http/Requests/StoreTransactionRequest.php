<?php

namespace App\Http\Requests;

use App\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_id' => [
                'required',
                Rule::exists('accounts', 'id')->where(fn ($query) => $query->where('user_id', auth()->id())),
            ],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(fn ($query) => $query
                    ->where('user_id', auth()->id())
                    ->where('type', $this->input('type'))),
            ],
            'transaction_date' => ['required', 'date'],
            'type' => ['required', Rule::in(array_column(TransactionType::cases(), 'value'))],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999999'],
            'description' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
