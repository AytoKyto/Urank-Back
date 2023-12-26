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


class GuestController extends Controller
{
    public function guestInvitation(Request $request)
    {
        DB::beginTransaction(); // Début de la transaction
        app()->setLocale($request->header('Accept-Language') ?? 'en');
        try {
            $request->validate([
                'name_invitation' => 'required|string',
                'email_invitation' => 'required|string|email',
                'email_guest' => 'required|string|email',
                'id_user' => 'required|integer',
            ], [
                'email.required' => trans('validation.custom.email_required'),
            ]);

            $user = User::findOrFail($request->id_user);
            $user->email = $request->email_guest;
            $user->save();

            $token = Str::random(60);
            DB::table('guest_invitation_tokens')->insert([
                'email_guest' => $request->email_guest,
                'email_invitation' => $request->email_invitation,
                'name_invitation' => $request->name_invitation,
                'name_guest' => $user->name,
                'token' => $token,
                'created_at' => now(),
            ]);

            $data = [
                'email' => $request->email_guest,
                'email_invitation' => $request->email_invitation,
                'name' => $request->name,
                'name_invitation' => $request->name_invitation,
                'title' => trans('email.title_guest_invitation_tokens'),
                'token' => $token,
                'subject' => trans('email.title_guest_invitation_tokens'),
                'content' => trans('email.content_guest_invitation_tokens', [
                    'name' => $request->name,
                    'email' => $request->email_invitation,
                ]),
            ];

            try {
                Mail::send('emails/email_guest_invitation_tokens', $data, function ($message) use ($data) {
                    $message->from('no-reply@urank.fr', 'urank.fr');
                    $message->to($data['email'])->subject($data['subject']);
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

    public function getGuestInvitation($token)
    {
        try {
            $data = DB::table('guest_invitation_tokens')->where('token', $token)->first();

            if (!$data) {
                return response()->json([
                    'message' => trans('message_api.validation'),
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'message' => trans('message_api.validation'),
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            Log::error("An error occurred: " . $e->getMessage());
            return response()->json([
                'message' => trans('message_api.failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
