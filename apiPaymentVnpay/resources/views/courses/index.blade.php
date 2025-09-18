@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Danh sách khoá học</h2>
    <div class="row">
        @foreach($courses as $course)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">{{ $course->name }}</h5>
                    <p class="card-text">{{ $course->description }}</p>
                    <p class="card-text"><strong>Giá:</strong> {{ number_format($course->price) }} VND</p>
                    <a href="{{ route('courses.show', $course->id) }}" class="btn btn-primary">Xem chi tiết & Thanh toán</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
