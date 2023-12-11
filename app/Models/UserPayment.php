<?php
use Illuminate\Database\Eloquent\Model;

class UserPayment extends Model
{
    protected $fillable = [
        'user_id', 'status', 'amount', 'method', 'payment_date'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
