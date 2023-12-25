<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Mail;


class AuthController extends Controller
{

    public function register(Request $request)
    {
        try {
            $fields = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string|confirmed',
                'type' => 'required|integer',
                'avatar' => 'required|string',
                'bg_color' => 'required|string',
                'bg_avatar' => 'required|string',
                'border_avatar' => 'required|string',
            ]);

            $user = User::create([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'password' => bcrypt($fields['password']),
                'type' => $fields['type'],
                'avatar' => $fields['avatar'],
                'bg_color' => $fields['bg_color'],
                'bg_avatar' => $fields['bg_avatar'],
                'border_avatar' => $fields['border_avatar'],
            ]);

            $token = $user->createToken('myapptoken')->plainTextToken;

            $title = "Mis à jour mot de passe utilisateur CAPEM";
            $content = "Mis à jour mot de passe utilisateur CAPEM";
            $user_email = $request->email;
            $user_name = "Utilisateur CAPEM";

            $title = "Bienvenue chez CAPEM";
            $content = "Votre compte a été créé avec succès.";
            $user_email = $user->email;
            $user_name = $user->name;

            try {
                $data = ['email' => $user_email, 'name' => $user_name, 'subject' => $title, 'content' => $content];
                Mail::send('emails/welcome_email', $data, function ($message) use ($data) {
                    $message->from('contact@capem.fr', 'capem.fr');
                    $message->to($data['email'], $data['name'])->subject($data['subject']);
                });
            } catch (\Exception $e) {
                Log::error("Email sending failed: " . $e->getMessage());
                // Vous pouvez choisir de renvoyer l'erreur ou simplement la logger.
            }


            return response()->json([
                'message' => 'User created successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad creds'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];
    }
}
