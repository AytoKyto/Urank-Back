<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeagueUser;
use App\Models\Duel;

class DashController extends Controller
{
    public function index($query_value)
    {
        try {
            // Get the query value
            $query_value = request('query_value');

            // Get data from query name
            parse_str($query_value, $query_data);


            // Récupérer les 3 premières ligues avec l'utilisateur ayant la meilleure activité
            $league_user_data = LeagueUser::where('user_id', $query_data['user_id'])
                ->orderByDesc('created_at')
                ->take(3)
                ->get();

            // Récupérer les 3 premiers duels de l'utilisateur en tant que gagnant ou perdant
            $duel_data = Duel::where(function ($query) use ($query_data) {
                $query->where('winner_user_id', $query_data['user_id'])
                    ->orWhere('loser_user_id', $query_data['user_id']);
            })
                ->orderByDesc('created_at')
                ->take(3)
                ->get();


            // Vérifier si toutes les données sont définies
            return response()->json([
                'status' => true,
                'message' => 'Requête effectuée avec succès',
                'league_user_data' => $league_user_data,
                'duel_data' => $duel_data,
            ], 200);
        } catch (\Exception $e) {
            // Retourner une erreur en cas d'exception
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la requête',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
