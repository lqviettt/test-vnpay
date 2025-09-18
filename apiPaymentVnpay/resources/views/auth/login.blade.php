@extends('layouts.app')
@section('content')
<div class="container" style="max-width: 400px;">
    <h2 class="mb-4">Đăng nhập</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Tên đăng nhập</label>
            <input type="text" name="name" id="name" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
    </form>
    @if(session('error'))
        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
    @endif
</div>
@endsection
