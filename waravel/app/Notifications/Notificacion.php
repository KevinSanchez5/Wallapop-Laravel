<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Notificacion extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable, Dispatchable, InteractsWithSockets, SerializesModels;

    public $mensaje;
    public $producto;
    public $carrito;
    public $valoracion;
    public $tipoNotificacion;
    /**
     * Create a new notification instance.
     */
    public function __construct($producto, $carrito, $valoracion, $tipoNotificacion)
    {
        if(!in_array($tipoNotificacion, ['productoComprado', 'carritoPagado', 'valoracionRecibida', 'productoEliminadoPorAdmin']))
        {
            Log::error('Tipo de notificacion no valida');
            return;
        }
        $this->producto = $producto;
        $this->carrito = $carrito;
        $this->valoracion = $valoracion;
        $this->tipoNotificacion = $tipoNotificacion;

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
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return match ($this->tipoNotificacion) {
            'productoComprado' => new BroadcastMessage([
                'mensaje' => 'Un producto suyo ha sido comprado',
                'valoracion' => null,
                'producto' => $this->producto,
                'fecha' => now()->format('Y-m-d H:i:s'),
            ]),
            'carritoPagado' => new BroadcastMessage([
                'mensaje' => 'Ha terminado la compra de su carrito',
                'valoracion' => null,
                'carrito' => $this->carrito,
                'fecha' => now()->format('Y-m-d H:i:s'),
            ]),
            'valoracionRecibida' => new BroadcastMessage([
                'mensaje' => 'Ha recibido una nueva valoracion',
                'valoracion' => $this->valoracion,
                'producto' => null,
                'fecha' => now()->format('Y-m-d H:i:s'),
            ]),
            'productoEliminadoPorAdmin' => new BroadcastMessage([
                'mensaje' => 'Se ha eliminado su producto por no respetar nuestras normas',
                'valoracion' => null,
                'carrito' => null,
                'producto' => $this->producto,
                'fecha' => now()->format('Y-m-d H:i:s'),
            ])
        };
    }

    public function broadcastOn()
    {
        $userId = null;
        switch ($this->tipoNotificacion) {
            case 'valoracionRecibida':
                $userId = $this->valoracion->clienteValorado->usuario_id;
                break;
            case 'productoComprado':
            case 'productoEliminadoPorAdmin':
                $userId = $this->producto->vendedor->usuario_id;
                break;
            /*TODO
            * cambiar carrito pagado han habido cambios en la clase
            * cambiar a venta en vez de carrito??*/
            case 'carritoPagado':
                $userId = $this->carrito->usuario_id;
                break;
        }
        if($userId){
            return new PrivateChannel('user' . $userId);
        }
        return [];
    }
}
