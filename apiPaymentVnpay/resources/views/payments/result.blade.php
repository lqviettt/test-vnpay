@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Kết quả tạo giao dịch</h2>
    @if(isset($payment->payment['payment_url']))
        <p>URL thanh toán: <a href="{{ $payment->payment['payment_url'] }}" target="_blank">{{ $payment->payment['payment_url'] }}</a></p>
    @else
        <p>Không tạo được URL thanh toán.</p>
    @endif
    <a href="{{ url('/payments/history') }}" class="btn btn-secondary">Xem lịch sử giao dịch</a>
</div>
@endsection
