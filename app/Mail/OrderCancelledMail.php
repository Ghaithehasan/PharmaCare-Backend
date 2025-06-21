<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\User;

class OrderCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $supplier;
    public $cancellationReason;
    public $cancelledAt;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, $supplier, $cancellationReason = null, $cancelledAt = null)
    {
        $this->order = $order;
        $this->supplier = $supplier;
        $this->cancellationReason = $cancellationReason;
        $this->cancelledAt = $cancelledAt ?? now();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تم إلغاء طلبية من المورد - ' . $this->order->order_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.order_cancelled',
            with: [
                'order_number' => $this->order->order_number,
                'supplier_name' => $this->supplier->contact_person_name,
                'order_total' => $this->order->calculateTotal(),
                'cancellation_reason' => $this->cancellationReason,
                'cancelled_at' => $this->cancelledAt->format('Y-m-d H:i:s'),
                'order_date' => $this->order->created_at->format('Y-m-d'),
            ]
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
