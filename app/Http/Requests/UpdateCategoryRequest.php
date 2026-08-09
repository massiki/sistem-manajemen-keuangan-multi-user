<?php

namespace App\Http\Requests;

use App\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
                Rule::unique('categories', 'name')
                    ->where(fn ($query) => $query->where('user_id', auth()->id())->where('type', $this->input('type')))
                    ->ignore($this->route('category')),
            ],
            'type' => ['required', Rule::in(array_column(TransactionType::cases(), 'value'))],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
