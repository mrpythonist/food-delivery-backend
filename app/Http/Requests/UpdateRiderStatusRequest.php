<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRiderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rider_id' => [
                'required',
                'exists:riders,id',
            ],

            'is_online' => [
                'required',
                'boolean',
            ],

            'is_available' => [
                'nullable',
                'boolean',
            ],
        ];
    }
}