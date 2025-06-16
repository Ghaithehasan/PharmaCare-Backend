<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * بيانات الطلبية
     *
     * @var Order
     */
    public Order $order;

    /**
     * قائمة الأدوية في الطلبية
     *
     * @var array
     */
    public array $medicines;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, array $medicines)
    {
        $this->order = $order;
        $this->medicines = $medicines;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'طلبية جديدة - ' . $this->order->order_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.new_order',
            with: [
                'order' => $this->order,
                'medicines' => $this->medicines,
                'supplier' => $this->order->supplier,
                'total_price' => collect($this->medicines)->sum('total_price')
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
