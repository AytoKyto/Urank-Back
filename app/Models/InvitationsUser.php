<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitationsUser extends Model
{
    use HasFactory;

    protected $table = 'invitations_user';
    protected $fillable = ['user_id', 'invited_user_id'];

    protected $with = ['user'];

    public function user() {
        return $this->belongsTo(User::class, 'invited_user_id');
    }
}
