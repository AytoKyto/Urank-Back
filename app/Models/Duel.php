<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Duel extends Model
{
    use HasFactory;

    protected $fillable = [
        'league_id',
        'author_id',
        'description'
    ];

    public function league() {
        return $this->belongsTo(League::class);
    }

    public function author() {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Si vous avez des relations avec DuelUser
    public function matchUsers() {
        return $this->hasMany(DuelUser::class);
    }
}
