<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'amount',
        'code',
        'order_id',// để lấy thông tin đơn hàng cần thanh toán (nếu có)
        'transaction_no',
        'bank_code',
        'card_type',
        'response_code',
        'pay_date',
        'status',
        'message',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'pay_date' => 'datetime',
    ];

    /**
     * user
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
