<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')?->id ?? 'NULL';

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],

            'email' => [
                'nullable',
                'email',
                'unique:customers,email,' . $customerId
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
                'unique:customers,phone,' . $customerId
            ],

            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}