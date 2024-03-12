<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\LeagueUser;
use App\Models\DuelUser;

class GetDataService
{
    public function leagueCard($userId, $loop)
    {
        // Récupérer tous les utilisateurs de la ligue, triés par ELO
        $league_card_data = LeagueUser::where('user_id', $userId)
            ->orderBy('ranking', 'asc')
            ->limit($loop)
            ->get();

        $league_card_user_data = [];
        foreach ($league_card_data as $duel) {
            $league_card_users = LeagueUser::where('league_id', $duel['league_id'])
                ->where('user_id', '!=', $userId)
                ->get();

            $league_card_user_data[] = [
                'duel' => $duel,
                'nbr_user' => count($league_card_users),
                'duels_users' => $league_card_users,
            ];
        }

        // Retourner les données ou effectuer d'autres opérations
        return $league_card_user_data;
    }

    public function leagueUser($leagueId, $loop)
    {
        // Récupérer tous les utilisateurs de la ligue, triés par ELO
        $league_card_data = LeagueUser::where('league_id', $leagueId)
            ->orderBy('ranking', 'asc')
            ->limit($loop)
            ->get();

        // Retourner les données ou effectuer d'autres opérations
        return $league_card_data;
    }

    public function duelCard($userId, $loop)
    {
        $duels_data = DuelUser::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($loop)
            ->get();

        $duels_user_data = [];
        foreach ($duels_data as $duel) {
            $duels_users = DuelUser::where('duel_id', $duel['duel_id'])
                ->where('user_id', '!=', $userId)
                ->get();

            $duels_user_data[] = [
                'duel' => $duel,
                'nbr_user' => count($duels_users),
                'duels_users' => $duels_users,
            ];
        }

        // Retourner les données ou effectuer d'autres opérations
        return $duels_user_data;
    }
}
