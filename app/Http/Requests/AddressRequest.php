<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => [
                'required',
                'exists:customers,id'
            ],

            'label' => [
                'nullable',
                'string',
                'max:50'
            ],

            'recipient_name' => [
                'required',
                'string',
                'max:255'
            ],

            'phone' => [
                'required',
                'string',
                'max:20'
            ],

            'address_line_1' => [
                'required',
                'string',
                'max:255'
            ],

            'address_line_2' => [
                'nullable',
                'string',
                'max:255'
            ],

            'city' => [
                'required',
                'string',
                'max:100'
            ],

            'state' => [
                'nullable',
                'string',
                'max:100'
            ],

            'postal_code' => [
                'nullable',
                'string',
                'max:20'
            ],

            'latitude' => [
                'nullable',
                'numeric'
            ],

            'longitude' => [
                'nullable',
                'numeric'
            ],

            'is_default' => [
                'sometimes',
                'boolean'
            ],
        ];
    }
}