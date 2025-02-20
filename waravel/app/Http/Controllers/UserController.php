<?php

namespace App\Http\Controllers;

use App\Mail\EmailSender;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
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
        $userRedis = Redis::get('user_'.$id);
        if($userRedis) {
            return response()->json(json_decode($userRedis));
        }

        $user = User::find($id);

          if(!$user) {
              return response()->json(['message' => 'User not found'], 404);
          }

        return response()->json($user);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', 'min:8', 'max:20', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/'],
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
        $user = Redis::get('user_'. $id);
        if(!$user) {
            $user = User::find($id);
        }

        if(!$user) {
            return response()->json(['message' => 'User no encontrado'], 404);
        }

        if (is_string($user)) {
            $userArray = json_decode($user, true);
            $userModel = User::hydrate([$userArray])->first();
        } elseif ($user instanceof User) {
            $userModel = $user;
        } else {
            $userModel = User::hydrate([$user])->first();
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'password' => ['string', 'min:8', 'max:20', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/'],
            'role' => 'string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message'=> 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $userModel->update($request->all());
        Redis::del('user_'. $id);
        Redis::set('user_'. $id, json_encode($userModel), 'EX',1800);

        return response()->json($userModel);
    }

    public function destroy($id)
    {
        $user = Redis::get('user_' . $id);
        if ($user) {
            $user = json_decode($user, true);
        } else {
            $user = User::find($id);
        }

        if(!$user) {
            return response()->json(['message' => 'User no encontrado'], 404);
        }

        if (is_array($user)) {
            $userModel = User::hydrate([$user])->first();
        } else {
            $userModel = $user;
        }

        $userModel->delete();
        Redis::del('user_'. $id);

        return response()->json(['message' => 'User eliminado correctamente']);
    }

    public function enviarCorreoRecuperarContrasenya(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
        ]);
        $email = $request->email;
        try {
            $user = User::byEmail($email)->first();
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()],500);
        }

        if(!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $codigo = strtoupper(Str::random(10));

        try {
            Mail::to($user->email)->send(new EmailSender($user, $codigo, null, 'recuperarContrasenya'));
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()],500);
        }
        return response()->json([
            'success' => true,
            'message' => 'Correo enviado',
        ], 200);
    }


    public function showEmail($email)
    {

        $user = User::where('email', $email)->first();

        if(!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

}
