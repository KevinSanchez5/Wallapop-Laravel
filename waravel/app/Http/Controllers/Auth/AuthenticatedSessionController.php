<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Carrito;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $this->deleteMyProductsFromCarrito();

        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard'); // Redirige al dashboard de admin
        }

        return redirect()->intended(route('pages.home', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function deleteMyProductsFromCarrito(): void
    {
        Log::info('Buscando productos que le pertenecen al usuario que ha iniciado sesión');
        $cliente = Cliente::where('usuario_id', auth()->user()->id)->first();

        if (!$cliente) {
            Log::warning('No se encontró el cliente asociado al usuario que ha iniciado sesión');
            return;
        }

        $cart = session('carrito', new Carrito([
            'lineasCarrito' => [],
            'precioTotal' => 0,
            'itemAmount' => 0
        ]));

        $newItemAmount = 0;

        foreach ($cart->lineasCarrito as $key => &$linea) {
            if ($linea->producto->vendedor_id === $cliente->id) {
                Log::info('Eliminando producto ' . $linea->producto->nombre);
                $cart->precioTotal -= $linea->precioTotal;
                unset($cart->lineasCarrito[$key]);
            } else {
                $newItemAmount += $linea->cantidad;
            }
        }

        $cart->itemAmount = $newItemAmount;
        session()->put('carrito', $cart);
    }
}
