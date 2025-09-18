<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Service\VnpayService;

class CourseController extends Controller
{
    // Hiển thị danh sách khoá học
    public function index()
    {
        $courses = Course::all();
        return view('courses.index', compact('courses'));
    }

    // Hiển thị chi tiết khoá học và nút thanh toán
    public function show($id)
    {
        $course = Course::findOrFail($id);
        $user = auth('web')->user();
        $paymentStatus = null;
        if ($user) {
            $payment = $user->payments()
                ->where('order_id', 'COURSE_' . $course->id)
                ->orderByDesc('id')
                ->first();
            if ($payment) {
                $paymentStatus = $payment->status;
            }
        }
        return view('courses.show', compact('course', 'paymentStatus'));
    }

    public function pay($id)
    {
        $user = auth('web')->user();
        $course = Course::findOrFail($id);

        $existingPayment = $user->payments()
            ->where('order_id', 'COURSE_' . $course->id)
            ->whereIn('status', ['pending', 'failed'])
            ->first();

        if ($user->payments()->where('order_id', 'COURSE_' . $course->id)->where('status', 'success')->exists()) {
            return redirect()->route('courses.show', $id)->with('error', 'Khoá học đã được thanh toán!');
        }

        if ($existingPayment) {
            $payment = $existingPayment;
            $code = $payment->code;
        } else {
            $code = rand(100000, 999999);
            $payment = $user->payments()->create([
                'code' => $code,
                'amount' => $course->price,
                'order_id' => 'COURSE_' . $course->id,
                'status' => 'pending',
            ]);
        }

        $vnpayService = app(VnpayService::class);
        $config = $vnpayService->fetchVNPay();
        $vnpUrl = $vnpayService->generateUrlPayment(null, $payment, $config);
        $payment->payment = $vnpUrl;

        return view('payments.result', ['payment' => $payment]);
    }
}
