<?php

// app/Models/Duel.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Duel extends Model {
    use HasFactory;

    protected $table = 'duels';
    protected $fillable = ['league_id', 'winner_user_id', 'winner_score', 'winner_score_value', 'loser_user_id', 'loser_score', 'loser_score_value', 'description'];

    public function league() {
        return $this->belongsTo(League::class, 'league_id');
    }

    public function winnerUser() {
        return $this->belongsTo(User::class, 'winner_user_id');
    }

    public function loserUser() {
        return $this->belongsTo(User::class, 'loser_user_id');
    }
}
