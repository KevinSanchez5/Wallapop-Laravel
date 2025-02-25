<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.passchange');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        Log::info('Metodo cambiar contraseña llamado');
        $request->validate([
            'email' => 'required|email|string',
            'codigo' => 'required|string|max:10',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $email = $request->get('email');
        $password = $request->get('new_password');
        $codigo = $request->get('codigo');

        $user = User::where('email', $email)->first();
        if (!$user) {
            Log::warning('El usuario no existe');
            return back()->withErrors(['email' => 'El usuario con ese email no existe']);
        }
        if (!Hash::check($codigo, $user->password_reset_token)) {
            Log::warning('Codigo incorrecto');
            return back()->withErrors(['password' => 'Codigo incorrecto']);
        }
        if ($user->password_reset_expires_at < now()){
            Log::warning('Código expirado');
            return back()->withErrors(['password' => 'Codigo expirado']);
        }

        Log::info('Datos validados correctamente');

        $user->password = Hash::make($password);
        $user->save();

        Log::info('Datos actualizados correctamente');
        Auth::login($user);

        return redirect()->route('login')->with('status', 'Contraseña cambiada exitosamente');

    }
}
