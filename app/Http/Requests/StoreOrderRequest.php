<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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

            'address_id' => [
                'required',
                'exists:addresses,id'
            ],

            'coupon_code' => 'nullable|string|exists:coupons,code',

            'payment_method' => [
                'required',
                'in:cod,easypaisa,jazzcash'
            ],

            'transaction_id' => [
                'required_if:payment_method,easypaisa,jazzcash',
                'nullable',
                'string',
            ],

            'payment_receipt' => [
                'required_if:payment_method,easypaisa,jazzcash',
                'nullable',
                'image',
                'max:4096',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:500'
            ],

            'items' => [
                'required',
                'array',
                'min:1'
            ],

            'items.*.product_id' => [
                'required',
                'exists:products,id'
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1'
            ],

            'items.*.product_variant_id' => [
                'required',
                'exists:product_variants,id'
            ],
        ];
    }
}
