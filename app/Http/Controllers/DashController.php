<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Services\GetDataService;
use Illuminate\Support\Facades\Auth;

class DashController extends Controller
{

    protected $getDataService;

    public function __construct(GetDataService $getDataService)
    {
        $this->getDataService = $getDataService;
    }

    public function index()
    {
        try {
            $userId = Auth::id();
            // Get the query value
            $global_stats = DB::table('view_global_stats')
                ->where('user_id', $userId)
                ->first();

            $league = $this->getDataService->leagueCard($userId, 4);
            $duel_data = $this->getDataService->duelCard($userId, 4);

            // Vérifier si toutes les données sont définies
            return response()->json([
                'status' => true,
                'message' => 'Requête effectuée avec succès',
                'globalStats' => $global_stats,
                'league' => $league,
                'duelData' => $duel_data,
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
