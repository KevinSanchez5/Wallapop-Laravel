<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class VentaController extends Controller
{
    public function index(){
        Log::info('Obteniendo todas las ventas');

        $query = Venta::orderBy('id', 'asc');

        $ventas = $query->paginate(5);

        $data = $ventas->getCollection()->transform(function ($venta) {
            return [
                'id' => $venta->id,
                'guid' => $venta->guid,
                'comprador' => $venta->comprador,
                'lineaVentas' => $venta->lineaVentas,
                'precioTotal' => $venta->precioTotal,
                'estado' => $venta->estado,
                'created_at' => $venta->created_at->toDateTimeString(),
                'updated_at' => $venta->updated_at->toDateTimeString(),
            ];
        });

        $customResponse = [
            'ventas' => $data,
            'paginacion' => [
                'pagina_actual' => $ventas->currentPage(),
                'elementos_por_pagina' => $ventas->perPage(),
                'ultima_pagina' => $ventas->lastPage(),
                'elementos_totales' => $ventas->total(),
            ],
        ];

        Log::info('Ventas obtenidas de la base de datos correctamente');
        return response()->json($customResponse);
    }

    public function show($guid)
    {
        Log::info('Obteniendo venta');
        $ventaRedis = Redis::get('venta_'.$guid);

        if ($ventaRedis) {
            Log::info('Venta obtenida desde Redis');
            return response()->json(json_decode($ventaRedis, true));
        }

        Log::info('Buscando venta de la base de datos');
        $venta = Venta::where('guid',$guid)->firstOrFail();

        if (!$venta) {
            return response()->json(['message' => 'Venta no encontrada'], 404);
        }

        $data = [
            'id' => $venta->id,
            'guid' => $venta->guid,
            'comprador' => $venta->comprador,
            'lineaVentas' => $venta->lineaVentas,
            'precioTotal' => $venta->precioTotal,
            'estado' => $venta->estado,
            'created_at' => $venta->created_at->toDateTimeString(),
            'updated_at' => $venta->updated_at->toDateTimeString(),
        ];

        Log::info('Guardando venta en cache redis');
        Redis::set('venta_'. $guid, json_encode($data), 'EX',1800);

        Log::info('Venta obtenida correctamente');
        return response()->json($data);
    }

    public function store(Request $request)
    {
        Log::info('Validando venta');

        $validator = Validator::make($request->all(), [
            'guid' => 'required|string|max:255|unique:ventas',
            'comprador' => 'required|array',
            'lineaVentas' => 'required|array',
            'precioTotal' => 'required|numeric|min:0',
            'estado' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Log::info('Guardando venta en base de datos');

        $venta = Venta::create($request->all());
        Log::info('Venta creada exitosamente');

        return response()->json($venta, 201);
    }

    public function destroy($guid)
    {
        Log::info('Intentando eliminar venta');
        $venta = Redis::get('venta_' . $guid);
        if(!$venta) {
            $venta = Venta::where('guid',$guid)->firstOrFail();
        }

        if(!$venta) {
            return response()->json(['message' => 'Venta no encontrada'], 404);
        }
        Log::info('Eliminando venta de la base de datos');
        $venta->delete();
        Log::info('Eliminando venta de la cache');
        Redis::del('venta_'. $guid);
        Log::info('Venta eliminada correctamente');

        return response()->json(['message' => 'Venta eliminada correctamente']);
    }
}
