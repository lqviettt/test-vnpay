@extends('layouts.app')
@section('content')
    <div class="container">
        <h2>Trang chủ thanh toán VNPAY</h2>

        <a href="{{ url('/payments/create') }}" class="btn btn-primary">Tạo giao dịch mới</a>
        <a href="{{ url('/payments/history') }}" class="btn btn-secondary">Xem lịch sử giao dịch</a>
        <a href="{{ route('courses.index') }}" class="btn btn-info">Danh sách khoá học</a>
    </div>
@endsection
