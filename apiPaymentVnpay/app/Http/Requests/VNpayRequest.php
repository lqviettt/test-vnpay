<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VNpayRequest extends FormRequest
{
    const REQUIRED_STRING = 'required|string';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'vnp_Amount' => 'required|numeric',
            'vnp_BankCode' => self::REQUIRED_STRING,
            'vnp_BankTranNo' => 'nullable|string',
            'vnp_CardType' => self::REQUIRED_STRING,
            'vnp_OrderInfo' => self::REQUIRED_STRING,
            'vnp_PayDate' => 'required|date_format:YmdHis',
            'vnp_ResponseCode' => self::REQUIRED_STRING,
            'vnp_TmnCode' => self::REQUIRED_STRING,
            'vnp_TransactionNo' => self::REQUIRED_STRING,
            'vnp_TransactionStatus' => self::REQUIRED_STRING,
            'vnp_TxnRef' => self::REQUIRED_STRING,
            'vnp_SecureHash' => self::REQUIRED_STRING,
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
