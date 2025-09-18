@extends('layouts.app')
@section('content')
    <div class="container">
        <h2>Lịch sử giao dịch thanh toán</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Số tiền</th>
                    <th>Mã giao dịch</th>
                    <th>Phương Thức</th>
                    <th>Trạng thái</th>
                    <th>Ngày thanh toán</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $bankMethods = [
                        'VNBANK' => 'ATM',
                        'VNPAYQR' => 'Mã QR',
                        'INTCARD' => 'VISA',
                    ];
                    $status = [
                        'pending' => 'Chờ thanh toán',
                        'success' => 'Thanh toán thành công',
                        'failed' => 'Thanh toán thất bại',
                    ];
                @endphp
                @foreach ($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ number_format($payment->amount) }} VND</td>
                        <td>{{ $payment->code }}</td>
                        <td>{{ $bankMethods[$payment->bank_code] ?? $payment->bank_code }}</td>
                        <td>{{ $status[$payment->status] ?? $payment->status }}</td>
                        <td>{{ $payment->pay_date ?? 'Chưa thanh toán' }}</td>
                        <td>
                            @if ($payment->status !== 'success')
                                <a href="{{ url('/payments/retry/' . $payment->code) }}" class="btn btn-warning">Thanh toán
                                    lại</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ url('/payments/create') }}" class="btn btn-primary">Tạo giao dịch mới</a>
    </div>
@endsection
