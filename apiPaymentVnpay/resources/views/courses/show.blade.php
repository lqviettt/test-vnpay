@extends('layouts.app')
@section('content')
    <div class="container" style="max-width:600px;">
        <h2>{{ $course->name }}</h2>
        <p>{{ $course->description }}</p>
        <p><strong>Giá khoá học:</strong> {{ number_format($course->price) }} VND</p>
        @if ($paymentStatus)
            <div class="mb-3">
                <strong>Trạng thái thanh toán:</strong>
                @if ($paymentStatus === 'success')
                    <span class="badge bg-success">Đã thanh toán</span>
                @elseif($paymentStatus === 'pending')
                    <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                @elseif($paymentStatus === 'failed')
                    <span class="badge bg-danger">Thất bại</span>
                @else
                    <span class="badge bg-secondary">Không xác định</span>
                @endif
            </div>
        @endif
        @if ($paymentStatus !== 'success')
            <form method="POST" action="{{ route('courses.pay', $course->id) }}">
                @csrf
                <button type="submit" class="btn btn-success">Thanh toán khoá học này</button>
            </form>
        @endif
        @if (session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif
        <a href="{{ route('courses.index') }}" class="btn btn-secondary mt-3">Quay lại danh sách khoá học</a>
    </div>
@endsection
