<?php

namespace App\Http\Requests;

use App\Enums\AccountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('accounts', 'name')->where(fn ($query) => $query->where('user_id', auth()->id())),
            ],
            'type' => ['required', Rule::in(array_column(AccountType::cases(), 'value'))],
            'initial_balance' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'description' => ['nullable', 'string'],
        ];
    }
}
