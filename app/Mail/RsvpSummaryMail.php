<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RsvpSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invitation $invitation)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Resumen de invitados · '.$this->invitation->title);
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.rsvp-summary',
            with: [
                'confirmedGuests' => $this->invitation->rsvps->where('attending', true)->sum('total_attendees'),
                'declinedResponses' => $this->invitation->rsvps->where('attending', false)->count(),
            ],
        );
    }
}
