<?php

namespace App\Http\Controllers;

use App\Models\InvitationsUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvitationsUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $id = Auth::id();
            $invitationsUser = InvitationsUser::where('user_id', $id)->get();

            return response()->json([
                'message' => 'successfully',
                'data' => $invitationsUser
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Valider les données de la requête
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'invited_user_id' => 'required|exists:users,id',
        ]);

        // Créer une nouvelle invitation d'utilisateur
        InvitationsUser::create($request->all());

        // Réponse avec code de succès
        return response()->json(['message' => 'Invitation created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(InvitationsUser $invitationsUser)
    {
        // Retourne une invitation spécifique
        return $invitationsUser;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InvitationsUser $invitationsUser)
    {
        // Valider les données de la requête
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'invited_user_id' => 'required|exists:users,id',
        ]);

        // Mettre à jour l'invitation d'utilisateur
        $invitationsUser->update($request->all());

        // Réponse avec code de succès
        return response()->json(['message' => 'Invitation updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InvitationsUser $invitationsUser)
    {
        // Supprimer l'invitation d'utilisateur
        $invitationsUser->delete();

        // Réponse avec code de succès
        return response()->json(['message' => 'Invitation deleted successfully'], 200);
    }
}
