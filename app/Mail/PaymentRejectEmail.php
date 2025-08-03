<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentRejectEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $invoice;
    public $order;
    public $supplier;

    /**
     * Create a new message instance.
     */
    public function __construct($payment)
    {
        $this->payment = $payment;
        $this->invoice = $payment->invoice;
        $this->order = $this->invoice->order;
        $this->supplier = $this->order->supplier;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'رفض دفعة - فاتورة رقم ' . $this->invoice->invoice_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.payment_reject_email',
            with: [
                'payment' => $this->payment,
                'invoice' => $this->invoice,
                'order' => $this->order,
                'supplier' => $this->supplier,
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
