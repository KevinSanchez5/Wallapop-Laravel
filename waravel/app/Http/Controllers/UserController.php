<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

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

          if(!$userRedis) {
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
        $validator = Validator::make($request->all(), [
            'nombre' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'password' => ['string', 'min:8', 'max:20', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/'],
            'role' => 'string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user->update($request->all());
        Redis::del('user_'. $id);
        Redis::set('user_'. $id, json_encode($user), 'EX',1800);

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = Redis::get('user_' . $id);
        if(!$user) {
            $user = User::find($id);
        }

        if(!$user) {
            return response()->json(['message' => 'User no encontrado'], 404);
        }
        $user->delete();
        Redis::del('user_'. $id);

        return response()->json(['message' => 'User eliminado correctamente']);
    }
}
