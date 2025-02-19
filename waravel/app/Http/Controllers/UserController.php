<?php

namespace App\Http\Controllers;

use App\Mail\EmailSender;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(){
        $users = User::all();
        return response()->json($users);
    }

    public function show($id)
    {
        $user = Cache::remember("user_{$id}", 60, function () use ($id) {
            $user = User::find($id);
            return $user ?: null;
        });
        if(!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
            'required',
            'string',
            'min:8',
            'max:20',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/'],
            'role' => 'required|string|max:255'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = User::create($request->all());
        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if(!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $validator = Validator::make($request->all(), [
            'nombre' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'password' => [
               'string',
               'min:8',
               'max:20',
               'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/'],
            'role' => 'string|max:20'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user->update($request->all());
        Cache::forget("user_{$id}");
        return response()->json($user, 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if(!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user->delete();
        Cache::forget("user_{$id}");
        return response()->json(null, 204);
    }

    public function enviarCorreoRecuperarContrasenya(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
        ]);
        $email = $request->email;
        //$user = User::where('email', $email)->first();
        $user = DB::select("SELECT * FROM users WHERE email = ? LIMIT 1", [$email]);
        // $user = DB::table('users')->where('email', $email)->first();
        if(!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $codigo = strtoupper(Str::random(10));

        Mail::to($user->email)->send(new EmailSender($user, $codigo, null, "recuperarContrasenya"));

        return response()->json([
            'success' => true,
            'message' => 'Correo enviado',
        ], 200);
    }

}
