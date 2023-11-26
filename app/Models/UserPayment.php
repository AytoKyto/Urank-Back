<?php

// app/Models/UserPayment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPayment extends Model {
    use HasFactory;

    protected $table = 'user_payments';
    protected $fillable = ['user_id', 'status', 'amount', 'method', 'payment_date'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
