<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class Notificacion extends Notification implements ShouldQueue
{
    use Queueable;

    public $mensaje;
    public $producto;
    public $valoracion;
    /**
     * Create a new notification instance.
     */
    public function __construct($producto, $valoracion, $tipoNotificacion)
    {
        $this->producto = $producto;
        $this->valoracion = $valoracion;
        $this->tipoNotificacion = $tipoNotificacion;

        if(!in_array($tipoNotificacion, ['productoComprado', 'carritoPagado', 'valoracionRecibida', 'productoEliminadoPorAdmin']))
        {
        Log::error('Tipo de notificacion no valida');
        }
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    /**
     * Notificación para Broadcast (Tiempo real, websockets)
     * @param object $notifiable Notificacion con la informacion
     * @return BroadcastMessage
     */
    public function toBroadCast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'mensaje' => $this->mensaje,
            'producto' => $this->producto,
            'valoracion' => $this->valoracion,
            'fecha' => now()->format('Y-m-d H:i:s'),
        ]);
    }
}
