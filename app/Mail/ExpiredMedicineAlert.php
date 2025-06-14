<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Medicine;
use Carbon\Carbon;

class ExpiredMedicineAlert extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $medicine;
    public $notificationType;
    public $expire_date;

    public function __construct(Medicine $medicine, $expire_date , string $notificationType)
    {
        $this->medicine = $medicine;
        $this->expire_date=$expire_date;
        $this->notificationType = $notificationType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "تنبيه {$this->notificationType}: دواء قارب على انتهاء الصلاحية",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $expire_date = Carbon::parse($this->expire_date);
        // dd($expire_date);
        return new Content(
            view: 'mail.expired_medicine_alert',
            with: [
                'medicine_name' => $this->medicine->medicine_name,
                'quantity' => $this->medicine->quantity,
                'category' => $this->medicine->category->name,
                'expiry_date' => $expire_date->format('Y-m-d'),
                'expiry_date_diffForHumans' => $expire_date->diffForHumans(),
                'notificationType' => $this->notificationType,
                'supportEmail' => 'support@gmail.com',
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
