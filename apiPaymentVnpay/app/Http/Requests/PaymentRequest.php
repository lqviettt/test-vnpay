<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $code = rand(100000, 999999);
        $this->merge(['code' => $code]);

        return [
            'amount' => 'required|numeric',
            'code' => 'required|integer|unique:payments,code',
            'order_id' => 'required|integer',
            'transaction_no' => 'nullable|string',
            'bank_code' => 'nullable|string',
            'card_type' => 'nullable|string',
            'response_code' => 'nullable|string',
            'pay_date' => 'nullable|date',
            'status' => 'nullable|string',
            'message' => 'nullable|string',
        ];
    }
}
