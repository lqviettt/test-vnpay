<?php

namespace App\Service;

use App\Models\Payment;
use Carbon\Carbon;

class VnpayService
{
    /**
     * Fetch VNPay configuration.
     *
     * @return array
     */
    public function fetchVNPay(): array
    {
        return config('payment-method.vnpay');
    }

    /**
     * Generate URL for VNPay payment.
     *
     * @param string $vnpBankCode
     * @param mixed $data
     * @param array $config
     * @return array
     */
    public function generateUrlPayment($vnpBankCode, mixed $data, array $config): array
    {
        $bank_code = $vnpBankCode ?? null;
        $vnpHashSecret = $config['secret_key'];
        $vnpUrl = $config['url'];
        $vnpIpAddr = request()->ip();
        $vnpCreateDate = Carbon::now('Asia/Ho_Chi_Minh')->format('YmdHis');
        $vnpExpireDate = Carbon::now('Asia/Ho_Chi_Minh')->addMinutes(15)->format('YmdHis');
        $totalPayment = $data->amount;
        $txnRef = $data->code;

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $config['tmn_code'],
            "vnp_Amount" => $totalPayment * 100,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnpCreateDate,
            "vnp_ExpireDate" => $vnpExpireDate,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnpIpAddr,
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => "Thanh toan GD: " . $txnRef,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $config['return_url'],
            "vnp_TxnRef" => $txnRef,
        ];

        $filteredData = array_filter(
            $inputData,
            function ($key) {
                return !in_array($key, ['vnp_Version', 'vnp_TmnCode', 'vnp_ReturnUrl', 'vnp_TxnRef']);
            },
            ARRAY_FILTER_USE_KEY
        );

        if (!empty($bank_code)) {
            $inputData['vnp_BankCode'] = $bank_code;
        }

        ksort($inputData);
        $query = "";
        $index = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($index == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $index = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnpUrl = $vnpUrl . "?" . $query;
        if (isset($vnpHashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashData, $vnpHashSecret);
            $vnpUrl .= 'vnp_SecureHash=' . $vnpSecureHash;
            return array_merge(
                ['payment_url' => $vnpUrl],
                $filteredData
            );
        }

        return [];
    }

    /**
     * Handle IPN callback from VNPay for payments.
     *
     * @param array $data
     * @param array $inputKeyDataSec
     * @return array
     */
    public function handleIPNCall(array $data, array $inputKeyDataSec)
    {
        $payment = Payment::where('code', $data['vnp_TxnRef'])->first();

        if (!$payment) {
            return $this->response('01', 'Payment not found');
        }

        $hashSecret = config('payment-method.vnpay.secret_key');
        $secureHash = $this->generateSecureHash($inputKeyDataSec, $hashSecret);

        if ($secureHash !== $data['vnp_SecureHash']) {
            return $this->response('97', 'Invalid signature');
        }

        if ($payment->status === 'success') {
            return $this->response('02', 'Payment already confirmed');
        }

        if ($payment->amount !== $data['vnp_Amount']) {
            return $this->response('04', 'Invalid amount');
            // $payment->update([
            //     'status' => 'success',
            //     'transaction_no' => $data['vnp_TransactionNo'],
            //     'response_code' => $data['vnp_ResponseCode'],
            //     'pay_date' => now(),
            //     'message' => 'Thanh toán thành công',
            // ]);

            // return $this->response('00', 'Confirm Success');
        }

        if (in_array($data['vnp_TransactionStatus'], ['00']) || in_array($data['vnp_ResponseCode'], ['00'])) {
            $payment->update([
                'status' => 'success',
                'transaction_no' => $data['vnp_TransactionNo'],
                'response_code' => $data['vnp_ResponseCode'],
                'pay_date' => now(),
                'message' => 'Thanh toán thành công',
            ]);

            return $this->response('00', 'Confirm Success');
        }

        return $this->response('99', 'Unknown error');
    }

    /**
     * Generate secure hash for VNPay.
     *
     * @param array $data
     * @param string $hashSecret
     * @return string
     */
    private function generateSecureHash(array $data, string $hashSecret): string
    {
        ksort($data);
        $hashData = '';
        foreach ($data as $key => $value) {
            $hashData .= urlencode($key) . "=" . urlencode($value) . "&";
        }
        $hashData = rtrim($hashData, "&");

        return hash_hmac('sha512', $hashData, $hashSecret);
    }

    /**
     * Generate a response array.
     *
     * @param string $code
     * @param string $message
     * @return array
     */
    private function response(string $code, string $message): array
    {
        return [
            'RspCode' => $code,
            'Message' => $message,
        ];
    }
}
