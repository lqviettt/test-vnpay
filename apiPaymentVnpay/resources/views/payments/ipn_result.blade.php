@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Kết quả xác nhận IPN từ VNPAY</h2>
    <div class="alert alert-info">
        <strong>Mã phản hồi:</strong> {{ $result['RspCode'] ?? '' }}<br>
        <strong>Thông báo:</strong> {{ $result['Message'] ?? '' }}
    </div>
    <a href="{{ url('/payments/history') }}" class="btn btn-secondary">Quay lại lịch sử giao dịch</a>
</div>
@endsection
