<?php

// app/Http/Controllers/LeagueUserController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeagueUser;
use App\Models\User;
use App\Models\InvitationsUser;
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
            $id = Auth::id();
            $user_liste = $request['users_id'];
            $user_guest = $request['user_guest'];
            $user_add = $request['user_add'];
            $league_id = $request['league_id'];


            // Initialize $user_guest_data outside of the if scope
            $user_guest_data = null;

            // Corrected the logical condition here
            if (!empty($user_guest)) {
                $user_guest_data = User::create([
                    "name" => $user_guest,
                    // Improved email uniqueness
                    "email" => uniqid() . "@guest.test",
                    "status" => "Active",
                    "type" => 1,
                    "avatar" => "default_avatar.png",
                    "bg_color" => "#FFFFFF", // Example default color
                    "bg_avatar" => "default_bg.png",
                    "border_avatar" => "default_border.png"
                ]);
            }

            if ($user_add !== 0) {
                if (!empty($user_add)) {
                    if (!User::where('id', $user_add)->get()) {
                        $user_liste[] = $user_add; // Corrected method to add to array
                    } else {
                        return response()->json(['message' => 'L\'utilisateur n\'existe pas'], 500);
                    }
                }
            }


            // Add the guest user's ID to the user list if a guest was created
            if ($user_guest_data) {
                $user_liste[] = $user_guest_data->id; // Corrected method to add to array
            }

            foreach ($user_liste as $user_id) {
                if (!LeagueUser::where('user_id', $user_id)->where('league_id', $league_id)->exists()) {
                    LeagueUser::create([
                        'user_id' => $user_id,
                        'league_id' => $league_id,
                        'elo' => 1000,
                        'type' => 0,
                    ]);

                    if (!InvitationsUser::where('invited_user_id', $user_id)->where('user_id', $id)->exists()) {
                        InvitationsUser::create([
                            'user_id' => $id,
                            'invited_user_id' => $user_id
                        ]);
                    }
                }
            }

            // Assuming rankingService->updateRanking is correctly implemented
            $this->rankingService->updateRanking($league_id);

            DB::commit();
            return response()->json(['message' => 'League user created successfully'], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'error' => $th->getMessage()], 500);
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

    public function destroy(int $userId, int $leagueId)
    {
        DB::beginTransaction();
        try {
            $leagueUser = LeagueUser::where('user_id', $userId)->where('league_id', $leagueId)->first();
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
