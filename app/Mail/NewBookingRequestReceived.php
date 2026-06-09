<?php

namespace App\Mail;

use App\Models\BookingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewBookingRequestReceived extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public BookingRequest $bookingRequest)
    {
        $this->bookingRequest->loadMissing('rentalUnit.house');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Novo pedido de reserva',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.new-booking-request-received',
        );
    }
}
