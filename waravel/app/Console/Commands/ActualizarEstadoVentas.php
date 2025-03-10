<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActualizarEstadoVentas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:actualizar-estado-ventas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el estado de las ventas cada 24 horas simulando el proceso de entrega de una venta';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Iniciando actualizacion de ventas');

        Venta::whereNotIn('estado', ['Entregado', 'Cancelado'])
            ->update([
                'estado' =>  DB::raw("
                    CASE
                        WHEN estado = 'Pendiente' THEN 'Procesando'
                        WHEN estado = 'Procesando' THEN 'Enviado'
                        WHEN estado = 'Enviado' THEN 'Entregado'
                        ELSE estado
                    END
                "),
            ]);
        Log::info('Finalizando actualizacion de ventas');
        $this->info('Estado de ventas actualizado');
    }
}
