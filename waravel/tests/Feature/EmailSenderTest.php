<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Mail\EmailSender;
use InvalidArgumentException;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class EmailSenderTest extends TestCase
{
    public function testTipoInvalido()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tipo de email no válido: tipoInvalido');

        new EmailSender('usuario', 'codigo', 'producto', 'tipoInvalido');
    }

    public function testEnvelopeRecuperarContrasenya()
    {
        $email = new EmailSender('usuario', 'codigo', null, 'recuperarContrasenya');
        $envelope = $email->envelope();

        $this->assertInstanceOf(Envelope::class, $envelope);
        $this->assertEquals('Recuperación de contraseña - Waravel', $envelope->subject);
    }

    public function testEnvelopeProductoComprado()
    {
        $email = new EmailSender('usuario', null, 'producto', 'productoComprado');
        $envelope = $email->envelope();

        $this->assertInstanceOf(Envelope::class, $envelope);
        $this->assertEquals('Producto Comprado en Waravel', $envelope->subject);
    }

    public function testContentRecuperarContrasenya()
    {
        $email = new EmailSender('usuario', 'codigo', null, 'recuperarContrasenya');
        $content = $email->content();

        $this->assertInstanceOf(Content::class, $content);
        $this->assertEquals('emails.recuperarContrasenya', $content->view);
        $this->assertArrayHasKey('usuario', $content->with);
        $this->assertArrayHasKey('codigo', $content->with);
    }

    public function testContentProductoComprado()
    {
        $email = new EmailSender('usuario', null, 'producto', 'productoComprado');
        $content = $email->content();

        $this->assertInstanceOf(Content::class, $content);
        $this->assertEquals('emails.productoComprado', $content->view);
        $this->assertArrayHasKey('usuario', $content->with);
        $this->assertArrayHasKey('producto', $content->with);
    }
}
