<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $supplier;

    /**
     * Create a new message instance.
     */
    public function __construct($payment, $supplier)
    {
        $this->payment = $payment;
        $this->supplier = $supplier;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'إشعار دفع جديد - فاتورة رقم: ' . $this->payment->invoice->invoice_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.payment_notification',
            with: [
                'supplier_name' => $this->supplier->name,
                'invoice_number' => $this->payment->invoice->invoice_number,
                'payment_amount' => $this->payment->paid_amount,
                'payment_method' => $this->payment->payment_method,
                'payment_date' => $this->payment->payment_date,
                'payment_status' => $this->payment->status,
                'total_invoice_amount' => $this->payment->invoice->total_amount,
                'remaining_amount' => $this->payment->invoice->total_amount - $this->payment->invoice->payments()->where('status', 'confirmed')->sum('paid_amount'),
                'order_number' => $this->payment->invoice->order->order_number ?? 'غير محدد'
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
