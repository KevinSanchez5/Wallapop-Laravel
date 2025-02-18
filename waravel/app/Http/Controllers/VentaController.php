<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class VentaController extends Controller
{
    public function index(){
        $venta = Venta::all();
        return response()->json($venta);
    }

    public function show($id)
    {
        $ventaRedis = Redis::get('venta_'.$id);
        if($ventaRedis) {
            return response()->json(json_decode($ventaRedis));

        }

        $venta = Venta::find($id);

        if(!$ventaRedis) {
            return response()->json(['message' => 'Venta no encontrada'], 404);
        }

        return response()->json($venta);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guid' => 'required|string|max:255|unique:ventas',
            'comprador' => 'required|array',
            'lineaVentas' => 'required|array',
            'precioTotal' => 'required|numeric|min:0'

        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $venta = Venta::create($request->all());
        return response()->json($venta, 201);
    }

    public function update(Request $request, $id)
    {
        $venta = Redis::get('venta_'. $id);

        if(!$venta) {
            $venta = Venta::find($id);
        }

        if(!$venta) {
            return response()->json(['message' => 'Venta no encontrada'], 404);
        }
        $validator = Validator::make($request->all(), [
            'guid' => 'required|string|max:255|unique:ventas',
            'comprador' => 'required|array',
            'lineaVentas' => 'required|array',
            'precioTotal' => 'required|numeric|min:0'

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $venta->update($request->all());
        Redis::del('venta_'. $id);
        Redis::set('venta_'. $id, json_encode($venta), 'EX',1800);

        return response()->json($venta);
    }

    public function destroy($id)
    {
        $venta = Redis::get('venta_' . $id);
        if(!$venta) {
            $venta = Venta::find($id);
        }

        if(!$venta) {
            return response()->json(['message' => 'Venta no encontrada'], 404);
        }
        $venta->delete();
        Redis::del('venta_'. $id);

        return response()->json(['message' => 'Venta eliminada correctamente']);
    }
}
