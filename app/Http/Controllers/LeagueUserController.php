<?php

// app/Http/Controllers/LeagueUserController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeagueUser;
use App\Services\RankingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LeagueUserController extends Controller
{
    protected $rankingService;

    public function __construct(RankingService $rankingService)
    {
        $this->rankingService = $rankingService;
    }

    public function index()
    {
        try {
            $id = Auth::id();
            $leagueUser = LeagueUser::where('user_id', $id)->get();

            return response()->json([
                'message' => 'League users retrieved successfully',
                'data' => $leagueUser
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function showUserInLeague($id)
    {
        try {
            $leagueUser = LeagueUser::where('league_id', $id)->get();

            return response()->json([
                'message' => 'League users retrieved successfully',
                'data' => $leagueUser
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Vérifiez si l'utilisateur existe déjà dans la ligue
            $userControlData = LeagueUser::where([
                ['user_id', '=', $request['user_id']],
                ['league_id', '=', $request['league_id']]
            ])->get();

            // Si l'utilisateur existe déjà, renvoyez une erreur
            if ($userControlData->isNotEmpty()) {
                return response()->json([
                    'message' => 'Error',
                    'error' => "L'utilisateur existe déjà dans cette ligue"
                ], 409); // 409 Conflict ou un autre code approprié
            }

            // Créez un nouvel enregistrement LeagueUser
            $leagueUser = LeagueUser::create($request->all());

            // Mettez à jour le classement
            $this->rankingService->updateRanking($request['league_id']);

            // Validez la transaction
            DB::commit();

            // Renvoyez une réponse de succès
            return response()->json([
                'message' => 'League user created successfully',
                'data' => $leagueUser
            ], 201);
        } catch (\Throwable $th) {
            // Annulez la transaction en cas d'erreur
            DB::rollBack();

            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            if ($id === "0") {
                $id = Auth::id();
            }

            $leagueUser = LeagueUser::where('user_id', $id)->get();
            return response()->json([
                'message' => 'League user retrieved successfully',
                'data' => $leagueUser
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $leagueUser = LeagueUser::findOrFail($id);
            $leagueUser->update($request->all());
            return response()->json([
                'message' => 'League user updated successfully',
                'data' => $leagueUser
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $leagueUser = LeagueUser::findOrFail($id);
            $leagueUser->delete();
            $this->rankingService->updateRanking($leagueUser['league_id']);
            DB::commit();
            return response()->json([
                'message' => 'League user deleted successfully'
            ], 204);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
