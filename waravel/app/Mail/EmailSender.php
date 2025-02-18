<?php

namespace App\Mail;

use http\Exception\InvalidArgumentException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

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

        if (!in_array($tipo,['recuperarContrasenya', 'productoComprado'])){
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
        };
    }

    /**
     * Adjuntos si los ubiera.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
