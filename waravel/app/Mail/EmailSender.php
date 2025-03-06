<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use InvalidArgumentException;

class EmailSender extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $codigo;
    public $producto;
    public $tipo;

    /**
     * Create a new message instance.
     */
    public function __construct($usuario, $codigo, $producto, $tipo)
    {
        $this->usuario = $usuario;
        $this->codigo = $codigo;
        $this->producto = $producto;
        $this->tipo = $tipo;

        if (!in_array($tipo,['recuperarContrasenya', 'productoComprado', 'eliminarPerfil', 'productoBorrado'])){
            throw new InvalidArgumentException("Tipo de email no válido: $tipo");
        }
    }

    /**
     * Configurar el sobre (Envelope) del correo
     */
    public function envelope(): Envelope
    {
        return match ($this->tipo) {
            'recuperarContrasenya' => new Envelope(
                subject: 'Recuperación de contraseña - Waravel'
            ),
            'productoComprado' => new Envelope(
                subject: 'Producto Comprado en Waravel'
            ),
            'eliminarPerfil' => new Envelope(
                subject: 'Eliminación de perfil - Waravel'
            ),
            'productoBorrado' => new Envelope(
                subject: 'Producto Borrado en Waravel'
            ),

        };
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return match ($this->tipo) {
            'recuperarContrasenya' => new Content(
                view: 'emails.recuperarContrasenya',
                with: [
                    'usuario' => $this->usuario,
                    'codigo' => $this->codigo,
                ]
            ),
            'productoComprado' => new Content(
                view: 'emails.productoComprado',
                with: [
                    'usuario' => $this->usuario,
                    'producto' => $this->producto,
                ]
            ),
            'eliminarPerfil' => new Content(
                view: 'emails.eliminarPerfil',
                with: [
                    'usuario' => $this->usuario,
                ]
            ),
            'productoBorrado' => new Content(
                view: 'emails.productoBorrado',
                with: [
                    'usuario' => $this->usuario,
                    'producto' => $this->producto,
                ]
            ),
        };
    }

    /**
     * Adjuntos si los hubiera.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
