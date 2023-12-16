<?php

namespace App\Services;

use App\Models\LeagueUser;

class RankingService
{
    public function updateRanking($leagueId)
    {
        // Récupérer tous les utilisateurs de la ligue, triés par ELO
        $users_league = LeagueUser::where('league_id', $leagueId)
            ->orderBy('elo', 'desc')
            ->get();

        // Initialisation du classement
        $ranking = 1;

        // Mise à jour de chaque utilisateur
        foreach ($users_league as $user) {
            LeagueUser::where('league_id', $leagueId)
                ->where('user_id', $user->user_id)
                ->update(['ranking' => $ranking]);

            $ranking++;
        }
    }
}
