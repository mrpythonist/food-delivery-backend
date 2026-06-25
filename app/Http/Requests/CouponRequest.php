<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $coupon = $this->route('coupon');

        return [
            'code' => [
                'required',
                'string',
                Rule::unique('coupons', 'code')
                    ->ignore($coupon?->id),
            ],

            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'minimum_order' => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ];
    }
}
