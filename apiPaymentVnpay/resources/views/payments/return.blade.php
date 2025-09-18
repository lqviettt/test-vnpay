<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VNPAY RESPONSE</title>
    <link href="{{ asset('css/jumbotron-narrow.css') }}" rel="stylesheet">
    <script src="{{ asset('js/jquery-1.11.3.min.js') }}"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            margin-top: 50px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            margin-bottom: 20px;
        }

        .header h3 {
            color: #007bff;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
        }

        .form-group span {
            font-weight: normal;
        }

        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn-back:hover {
            background-color: #0056b3;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header clearfix">
            <h3 class="text-muted">VNPAY RESPONSE</h3>
        </div>
        <div class="table-responsive">
            <div class="form-group">
                <label>Mã đơn hàng:</label>
                <span>{{ $data['vnp_TxnRef'] }}</span>
            </div>
            <div class="form-group">
                <label>Số tiền:</label>
                <span>{{ number_format($data['vnp_Amount'] / 100, 2) }} VND</span>
            </div>
            <div class="form-group">
                <label>Nội dung thanh toán:</label>
                <span>{{ $data['vnp_OrderInfo'] }}</span>
            </div>
            <div class="form-group">
                <label>Mã phản hồi (vnp_ResponseCode):</label>
                <span>{{ $data['vnp_ResponseCode'] }}</span>
            </div>
            <div class="form-group">
                <label>Mã GD Tại VNPAY:</label>
                <span>{{ $data['vnp_TransactionNo'] }}</span>
            </div>
            <div class="form-group">
                <label>Mã Ngân hàng:</label>
                <span>{{ $data['vnp_BankCode'] }}</span>
            </div>
            <div class="form-group">
                <label>Thời gian thanh toán:</label>
                <span>{{ $data['vnp_PayDate'] }}</span>
            </div>
            <div class="form-group">
                <label>Kết quả:</label>
                <span>
                    @if ($isSignatureValid)
                        @if ($isSuccess)
                            <span style="color:blue">GD Thành công</span>
                        @else
                            <span style="color:red">GD Không thành công</span>
                        @endif
                    @else
                        <span style="color:red">Chữ ký không hợp lệ</span>
                    @endif
                </span>
            </div>
            <a href="http://localhost:8000/" class="btn-back">Quay lại trang chủ</a>
        </div>
        <footer class="footer">
            <p>&copy; VNPAY {{ date('Y') }}</p>
        </footer>
    </div>
</body>

</html>
