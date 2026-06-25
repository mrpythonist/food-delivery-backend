<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RiderRequest extends FormRequest
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
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('riders', 'phone')
                    ->ignore($this->route('rider')),
            ],

            'is_online' => [
                'nullable',
                'boolean',
            ],

            'is_available' => [
                'nullable',
                'boolean',
            ],
        ];
    }
}
