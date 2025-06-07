<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Medicine;

class LowStockAlert extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $medicine;
    public function __construct(Medicine $medicine)
    {
        $this->medicine = $medicine;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تنبيه: مخزون منخفض - ' . $this->medicine->medicine_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.low_stock_alert',
            with: [
                'medicine_name' => $this->medicine->medicine_name,
                'quantity' => $this->medicine->quantity,
                'alert_quantity' => $this->medicine->alert_quantity,
                // 'medicine_id' => $this->medicine->id,
            ],
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
