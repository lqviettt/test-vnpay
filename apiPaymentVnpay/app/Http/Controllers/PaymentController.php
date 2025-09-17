<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Http\Requests\VNpayRequest;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    /**
     * vnpayIPNCallback khi vnpay gửi callback IPN
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function vnpayIPNCallback(VNpayRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $result = $this->handleIPNCall($validatedData, collect($validatedData)->except('vnp_SecureHash')->toArray());

        return response()->json($result);
    }

    /**
     * Xử lý callback IPN từ VNPAY cho bảng payments
     * @param array $data
     * @param array $inputKeyDataSec
     * @return array
     */
    public static function handleIPNCall(array $data, array $inputKeyDataSec): array
    {
        $paymentCode = $data['vnp_TxnRef'];
        $transactionNo = $data['vnp_TransactionNo'];
        $vnpAmount = $data['vnp_Amount'] / 100;
        $vnpTransactionStatus = $data['vnp_TransactionStatus'];
        $vnpResponseCode = $data['vnp_ResponseCode'];
        $vnpSecureHash = $data['vnp_SecureHash'];

        // Lấy payment từ DB
        $payment = Payment::where('code', $paymentCode)->first();
        if (!$payment) {
            return [
                'RspCode' => '01',
                'Message' => 'Payment not found'
            ];
        }

        $actualAmount = $payment->amount;
        $status = $payment->status;
        $config = [
            'secret_key' => config('payment-method.vnpay.secret_key')
        ];
        $hashSecret = $config['secret_key'];

        ksort($inputKeyDataSec);
        $hashData = '';
        foreach ($inputKeyDataSec as $key => $value) {
            $hashData .= urlencode($key) . "=" . urlencode($value) . "&";
        }
        $hashData = rtrim($hashData, "&");
        $secureHash = hash_hmac('sha512', $hashData, $hashSecret);
        $isSignatureValid = $secureHash === $vnpSecureHash;
        if (!$isSignatureValid) {
            return [
                'RspCode' => '97',
                'Message' => 'Invalid signature'
            ];
        }
        if ($status === 'success') {
            return [
                'RspCode' => '02',
                'Message' => 'Payment already confirmed'
            ];
        }
        if ($actualAmount !== $vnpAmount) {
            return [
                'RspCode' => '04',
                'Message' => 'Invalid amount'
            ];
        }
        dd($vnpTransactionStatus, $vnpResponseCode);
        if ($vnpTransactionStatus === '00' || $vnpResponseCode === '00') {
            $payment->status = 'success';
            $payment->transaction_no = $transactionNo;
            $payment->response_code = $vnpResponseCode;
            $payment->pay_date = now();
            $payment->message = 'Thanh toán thành công';
            $payment->save();
            return [
                'RspCode' => '00',
                'Message' => 'Confirm Success'
            ];
        }

        return [
            'RspCode' => '99',
            'Message' => 'Unknown error'
        ];
    }

    /**
     * createPayment
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function createPayment(PaymentRequest $request): JsonResponse
    {
        $user = auth('api')->user();
        $validated = $request->validated();
        $payment = $user->payments()->create($validated);

        $config = $this->fetchVNPay();
        $vnpUrl = $this->generateUrlPayment($request->bank_code, $payment, $config);
        $payment->payment = $vnpUrl;

        return $this->created($payment);
    }

    /**
     * fetchVNPay
     *
     * @return array
     */
    private function fetchVNPay(): array
    {
        return [
            'return_url' => config('payment-method.vnpay.return_url'),
            'refund_url' => config('payment-method.vnpay.refund_url'),
            'refund_email' => config('payment-method.vnpay.refund_email'),
            'tmn_code' => config('payment-method.vnpay.tmn_code'),
            'url' => config('payment-method.vnpay.url'),
            'secret_key' => config('payment-method.vnpay.secret_key'),
        ];
    }

    /**
     * generateUrlPayment
     *
     * @param  mixed $vnpBankCode
     * @param  mixed $data
     * @param  mixed $config
     * @return array
     */
    private function generateUrlPayment(string $vnpBankCode, mixed $data, array $config): array
    {
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

        if (!empty($vnpBankCode)) {
            $inputData['vnp_BankCode'] = $vnpBankCode;
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
     * paymentReturn
     *
     * @param  mixed $request
     * @return void
     */
    public function paymentReturn(Request $request)
    {
        $vnp_HashSecret = config('app.vnp_HashSecret');
        $vnp_SecureHash = $request->input('vnp_SecureHash');
        $inputData = $request->except('vnp_SecureHash');

        ksort($inputData);
        $hashData = '';
        foreach ($inputData as $key => $value) {
            $hashData .= urlencode($key) . "=" . urlencode($value) . "&";
        }

        $hashData = rtrim($hashData, "&");
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $isSignatureValid = $secureHash === $vnp_SecureHash;
        $isSuccess = $request->input('vnp_ResponseCode') === '00';

        return view('payments.return', [
            'data' => $request->all(),
            'isSignatureValid' => $isSignatureValid,
            'isSuccess' => $isSuccess,
        ]);
    }

    /**
     * paymentHistory
     *
     * @return JsonResponse
     */
    public function paymentHistory(): JsonResponse
    {
        $user = auth('api')->user();
        $payment = $user->payments()->latest()->get();

        return $this->sendSuccess($payment);
    }
}
