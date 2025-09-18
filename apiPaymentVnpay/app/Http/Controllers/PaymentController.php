<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Http\Requests\VNpayRequest;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Service\VnpayService;

class PaymentController extends Controller
{
    /**
     * Hiển thị form tạo payment
     */
    public function showCreateForm()
    {
        return view('payments.create');
    }

    /**
     * Xử lý tạo payment từ web
     */
    public function createPaymentWeb(PaymentRequest $request)
    {
        $user = auth('web')->user();

        $payment = $user->payments()->create($request->validated());
        if (!$payment) {
            return back()->withErrors(['error' => 'Không thể tạo payment!']);
        }
        $config = app(VnpayService::class)->fetchVNPay();
        $vnpUrl = app(VnpayService::class)->generateUrlPayment($request->bank_code, $payment, $config);
        $payment->payment = $vnpUrl;
        return view('payments.result', ['payment' => $payment]);
    }

    /**
     * Hiển thị lịch sử payment cho web
     */
    public function paymentHistoryWeb()
    {
        $user = auth('web')->user();
        $payments = $user->payments()->latest()->get();
        return view('payments.history', ['payments' => $payments]);
    }

    /**
     * Thử lại thanh toán
     */
    public function retryPaymentWeb(Request $request, $code)
    {
        $user = auth('web')->user();
        $payment = $user->payments()->where('code', $code)->first();
        if (!$payment) {
            return back()->withErrors(['error' => 'Payment không tồn tại!']);
        }
        if ($payment->status === 'success') {
            return back()->withErrors(['error' => 'Payment đã thanh toán thành công!']);
        }
        $config = app(VnpayService::class)->fetchVNPay();
        $vnpUrl = app(VnpayService::class)->generateUrlPayment(null, $payment, $config);
        if ($vnpUrl) {
            $payment->payment = $vnpUrl;
            return view('payments.result', ['payment' => $payment]);
        }
        return back()->withErrors(['error' => 'Không thể tạo link thanh toán!']);
    }

    /**
     * __construct
     *
     * @param  mixed $vnpayService
     * @return void
     */
    public function __construct(protected VnpayService $vnpayService) {}

    /**
     * vnpayIPNCallback khi vnpay gửi callback IPN
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function vnpayIPNCallback(VNpayRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $result = $this->vnpayService->handleIPNCall($validatedData, collect($validatedData)->except('vnp_SecureHash')->toArray());

        return response()->json($result);
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

        $config = $this->vnpayService->fetchVNPay();
        $vnpUrl = $this->vnpayService->generateUrlPayment($request->bank_code, $payment, $config);
        $payment->payment = $vnpUrl;

        return $this->created($payment);
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
