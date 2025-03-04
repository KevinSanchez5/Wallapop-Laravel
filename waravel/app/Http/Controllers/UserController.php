<?php

namespace App\Http\Controllers;

use App\Mail\EmailSender;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    public function index(){
        $query = User::orderBy('id', 'asc');
        $users = $query->paginate(5);
        Log::info("Obteniendo todos los usuarios desde la base de datos");
        $data = $users->getCollection()->transform(function ($user) {
            return [
                'id' => $user->id,
                'guid' => $user->guid,
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'role' => $user->role,
                'created_at' => $user->created_at->toDateTimeString(),
                'updated_at' => $user->updated_at->toDateTimeString(),
            ];
        });

        $customResponse = [
            'users' => $data,
            'paginacion' => [
                'pagina_actual' => $users->currentPage(),
                'elementos_por_pagina' => $users->perPage(),
                'ultima_pagina' => $users->lastPage(),
                'elementos_totales' => $users->total(),
            ],
        ];
        Log::info("Usuarios obtenidos correctamente");
        return response()->json($customResponse);
    }

    public function show($guid)
    {
        Log::info("Buscando usuario con guid: {$guid}");
        $userRedis = Redis::get('user_'.$guid);
        if($userRedis) {
            Log::info("Usuario obtenido desde Redis");
            return response()->json(json_decode($userRedis));
        }
        Log::info("Usuario no encontrado en Redis, buscando en la base de datos");
        $user = User::where('guid', $guid)->first();

        if(!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        Redis::set('user_' . $guid, json_encode($user), 'EX', 1800);

        Log::info("Usuario encontrado", ['user_guid' => $guid]);
        return response()->json($user);
    }

    public function store(Request $request)
    {
        Log::info("Intentando crear un nuevo usuario");
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', 'min:8', 'max:20', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/'],
            'role' => 'string|in:user,cliente,admin|max:20'
        ]);
        if ($validator->fails()) {
            Log::info("Error de validación al crear usuario", ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = User::create($request->all());
        Log::info("Usuario creado exitosamente");
        return response()->json($user, 201);
    }

    public function update(Request $request, $guid)
    {
        Log::info("Intentando actualizar usuario con guid: {$guid}");
        $user = Redis::get('user_'. $guid);
        if(!$user) {
            Log::info("Usuario no encontrado en Redis, buscando en la base de datos");
            $user = User::where('guid', $guid)->first();
        }

        if(!$user) {
            return response()->json(['message' => 'User no encontrado'], 404);
        }

        if (is_string($user)) {
            $userArray = json_decode($user, true);
            $userModel = User::hydrate([$userArray])->first();
        } elseif ($user instanceof User) {
            $userModel = $user;
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'password' => ['string', 'min:8', 'max:20', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/'],
            'role' => 'string|in:user,cliente,admin|max:20'
        ]);

        if ($validator->fails()) {
            Log::info("Error de validación al actualizar usuario", ['errors' => $validator->errors()]);
            return response()->json([
                'message'=> 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        Log::info("Actualizando el usuario con guid: {$guid}");

        $userModel->update($request->all());
        Redis::del('user_'. $guid);
        Redis::set('user_'. $guid, json_encode($userModel), 'EX',1800);

        Log::info("Usuario actualizado exitosamente");

        return response()->json($userModel);
    }

    public function destroy($guid)
    {
        Log::info("Intentando eliminar usuario con ID: {$guid}");
        $user = Redis::get('user_' . $guid);
        if ($user) {
            $user = json_decode($user, true);
            Log::info("Usuario encontrado en Redis");
        } else {
            Log::info("Usuario no encontrado en Redis, buscando en la base de datos");
            $user = User::where('guid',$guid)->first();
        }

        if(!$user) {
            return response()->json(['message' => 'User no encontrado'], 404);
        }

        if (is_array($user)) {
            $userModel = User::hydrate([$user])->first();
        } else {
            $userModel = $user;
        }

        Log::info("Eliminando el usuario con guid: {$guid}");
        $userModel->delete();

        Redis::del('user_'. $guid);

        Log::info("Usuario eliminado correctamente", ['user_guid' => $guid]);

        return response()->json(['message' => 'User eliminado correctamente']);
    }

    public function enviarCorreoRecuperarContrasenya(Request $request)
    {
        Log::info('Iniciando proceso de recuperación de contraseña', ['email' => $request->email]);

        $request->validate([
            'email' => 'required|string|email|max:255',
        ]);

        $email = $request->email;
        Log::info('Validación de email completada', ['email' => $email]);
        Log::info('Buscando usuario por email', ['email' => $email]);
        $user = $this->findUserByEmail($email);

        if(!$user){
            Log::warning('Usuario no encontrado', ['email' => $email]);
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $codigo = strtoupper(Str::random(10));
        $user->password_reset_token = Hash::make($codigo);
        $user->password_reset_expires_at = now()->addMinutes(5);
        $user->updated_at = now();
        $user->save();
        Log::info('Código de recuperación generado y almacenado en la base de datos');

        try {
            Mail::to($user->email)->send(new EmailSender($user, $codigo, null, 'recuperarContrasenya'));
            Log::info('Correo de recuperación enviado', ['email' => $user->email]);
        } catch(\Exception $e) {
            Log::error('Error al enviar el correo de recuperación', [
                'email' => $user->email,
                'exception' => $e->getMessage()
            ]);
            return response()->json(['error' => $e->getMessage()], 503);
        }

        return response()->json([
            'success' => true,
            'message' => 'Correo enviado correctamente',
        ], 200);
    }

    public function verificarCodigoCambiarContrasenya(Request $request){
        Log::info('Verificando código para cambiar la contraseña');

        $request->validate([
            'codigo' => 'required|string|max:10',
            'email' => 'required|string|email|max:255|exists:users,email',
        ]);

        $email = $request->email;
        $codigo = $request->codigo;

        $user = $this->findUserByEmail($email);

        if(!Hash::check($codigo, $user->password_reset_token)){
            Log::warning('Codigo incorrecto ingresado');
            return response()->json(['success'=>false, 'message' => 'Ha ingresado un codigo incorrecto'], 403);
        }

        if($user->password_reset_expires_at < now()){
            Log::warning('Codigo expirado');
            return response()->json(['success'=>false, 'message' => 'Código para recuperar contraseña expirado'], 403);
        }

        Log::info('Código de recuperación verificado correctamente', ['email' => $user->email]);
        return response()->json(['success' => true, 'message'=>'Codigo verificado'], 200);
    }

    public function validarEmail($request)
    {
        $user = $this->findUserByEmail($request);

        return response()->json(['exists' => $user !== null]);
    }

    public function findUserByEmail($email)
    {
        Log::info("Buscando usuario por email", ['email' => $email]);

        $user = User::where('email', $email)->first();

        if (!$user) {
            Log::warning('Usuario no encontrado', ['email' => $email]);
            return null;
        }

        Log::info("Usuario encontrado", ['email' => $email, 'user_id' => $user->id]);
        return $user;
    }
}
