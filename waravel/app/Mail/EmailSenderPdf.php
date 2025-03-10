<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailSenderPdf extends Mailable
{
    use Queueable, SerializesModels;

    public $venta;
    public $pdf;

    /**
     * Crea una nueva instancia del correo.
     */
    public function __construct($venta, $pdf)
    {
        $this->venta = $venta;
        $this->pdf = $pdf;
    }

    /**
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Factura de tu compra'
        );
    }

    /**
     * Contenido del correo.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.compra',
        );
    }

    /**
     * Adjuntar el PDF.
     */
    public function attachments(): array
    {
        $pdfContent = $this->pdf->output();
        $pdfPath = storage_path('app/public/factura.pdf');
        file_put_contents($pdfPath, $pdfContent);

        return [
            Attachment::fromPath($pdfPath)
                ->as('Factura.pdf')
                ->withMime('application/pdf'),
        ];
    }

    /**
     * Método estático para crear el email con el PDF.
     */
    public static function createWithPdf($venta, $pdf)
    {
        return new self($venta, $pdf);
    }
}
