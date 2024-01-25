<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Mail;


class AuthController extends Controller
{

    public function register(Request $request)
    {
        app()->setLocale($request->header('Accept-Language') ?? 'en');
        DB::beginTransaction(); // Début de la transaction

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
            ], [
                'email.required' => trans('validation.custom.email_required'),
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

            $data = [
                'email' => $fields['email'],
                'name' => $fields['name'],
                'title' => trans('email.title_new_user'), // Assurez-vous que cette ligne existe
                'subject' => trans('email.title_new_user'), // Assurez-vous que cette ligne existe
                'content' => trans('email.content_new_user', ['name' => $fields['name']])
            ];

            try {
                Mail::send('emails/welcome_email', $data, function ($message) use ($data) {
                    $message->from('no-reply@urank.fr', 'urank.fr');
                    $message->to($data['email'], $data['name'])->subject($data['subject']);
                });
            } catch (\Exception $e) {
                DB::rollBack(); // Rollback en cas d'échec de l'envoi de l'email
                Log::error("Email sending failed: " . $e->getMessage());
                return response()->json([
                    'message' => trans('message_api.failed'),
                ], 500);
            }


            DB::commit(); // Commit de la transaction

            return response()->json([
                'message' => trans('message_api.validation'),
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack(); // Rollback en cas d'erreur de validation

            return response()->json([
                'message' => trans('message_api.failed'),
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
            'message' => 'Log',
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

    public function forgotPassword(Request $request)
    {
        DB::beginTransaction(); // Début de la transaction
        app()->setLocale($request->header('Accept-Language') ?? 'en');
        try {
            $request->validate(['email' => 'required|email']);

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(["msg" => "User not found"], 404);
            }

            $token = Str::random(60);
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => now(), // Utilisation de la fonction helper now() pour obtenir la date actuelle
            ]);

            $data = [
                'email' => $request->email,
                'name' => $request->email,
                'title' => trans('email.title_password_reset'), // Assurez-vous que cette ligne existe
                'token' => $token,
                'subject' => trans('email.title_password_reset'), // Assurez-vous que cette ligne existe
                'content' => trans('email.content_password_reset'),
            ];

            try {
                Mail::send('emails/email_forgot_password', $data, function ($message) use ($data) {
                    $message->from('no-reply@urank.fr', 'urank.fr');
                    $message->to($data['email'], $data['name'])->subject($data['subject']);
                });
                DB::commit(); // Commit de la transaction
            } catch (\Exception $e) {
                DB::rollBack(); // Rollback en cas d'échec de l'envoi de l'email
                Log::error("Email sending failed: " . $e->getMessage());
                return response()->json([
                    'message' => trans('message_api.failed'),
                    'error' => $e->getMessage(),
                ], 500);
            }

            return response()->json(["msg" => "E-mail sent"]);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback en cas d'exception dans la transaction
            Log::error("An error occurred: " . $e->getMessage());
            return response()->json([
                'message' => trans('message_api.failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function resetPassword(Request $request)
    {
        DB::beginTransaction(); // Début de la transaction
        app()->setLocale($request->header('Accept-Language') ?? 'en');
        try {

            $request->validate([
                'email' => 'required|email',
                'token' => 'required|string',
                'password' => 'required|string|confirmed'
            ]);

            $reset = DB::table('password_reset_tokens')->where([
                ['email', '=', $request->email],
                ['token', '=', $request->token]
            ])->first();

            if (!$reset) {
                return response()->json(["msg" => "Invalid token"], 400);
            }

            $user = User::where('email', $request->email)->first();
            $user->password = bcrypt($request->password);
            $user->save();

            // Supprimer le token de réinitialisation
            DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

            DB::commit(); // Commit de la transaction
            return response()->json(["msg" => "Password has been successfully reset"]);
        } catch (\Exception $e) {
            return response()->json(["error" => "An error occurred: " . $e->getMessage()], 500);
            DB::rollBack(); // Rollback en cas d'échec de l'envoi de l'email
        }
    }
}
