@extends('layouts.app')
@section('content')
    <div class="container">
        <h2>Tạo giao dịch thanh toán mới</h2>

        {{-- Hiển thị thông báo lỗi --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/payments/create') }}">
            @csrf
            <div class="mb-3">
                <label for="amount" class="form-label">Số tiền</label>
                <input type="number" name="amount" id="amount" class="form-control" required min="1000"
                    value="{{ old('amount') }}">
            </div>
            <div class="mb-3">
                <label for="bank_code" class="form-label">Ngân hàng</label>
                <select name="bank_code" id="bank_code" class="form-control">
                    <option value="VNBANK" {{ old('bank_code', request('bank_code')) == 'VNBANK' ? 'selected' : '' }}>ATM
                    </option>
                    <option value="VNPAYQR" {{ old('bank_code', request('bank_code')) == 'VNPAYQR' ? 'selected' : '' }}>Mã
                        QR</option>
                    <option value="INTCARD" {{ old('bank_code', request('bank_code')) == 'INTCARD' ? 'selected' : '' }}>VISA
                    </option>
                </select>
            </div>
            <div class="mb-3">
                <label for="order_id" class="form-label">Mã đơn hàng (tuỳ chọn)</label>
                <input type="text" name="order_id" id="order_id" class="form-control" maxlength="50"
                    value="{{ old('order_id') }}">
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Nội dung</label>
                <input type="text" name="message" id="message" class="form-control" maxlength="255"
                    value="{{ old('message') }}">
            </div>
            <button type="submit" class="btn btn-primary">Tạo giao dịch</button>
        </form>
    </div>
@endsection
