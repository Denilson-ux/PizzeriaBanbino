<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ReporteCompras extends Mailable
{
    use Queueable, SerializesModels;

    public $compras;
    public $fechaInicio;
    public $fechaFin;
    public $proveedorNombre;
    public $totalCompras;
    public $montoTotal;

    /**
     * Create a new message instance.
     *
     * @param Collection $compras
     * @param string $fechaInicio
     * @param string $fechaFin
     * @param string|null $proveedorNombre
     */
    public function __construct(Collection $compras, string $fechaInicio, string $fechaFin, ?string $proveedorNombre = null)
    {
        $this->compras = $compras;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->proveedorNombre = $proveedorNombre;
        $this->totalCompras = $compras->count();
        $this->montoTotal = $compras->sum('total');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Reporte de Compras - Pizzería Bambino';
        
        if ($this->proveedorNombre) {
            $subject .= ' - Proveedor: ' . $this->proveedorNombre;
        }
        
        $subject .= ' (' . Carbon::parse($this->fechaInicio)->format('d/m/Y') . 
                   ' - ' . Carbon::parse($this->fechaFin)->format('d/m/Y') . ')';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reporte-compras',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}