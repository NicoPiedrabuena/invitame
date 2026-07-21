<?php

namespace Tests\Feature;

use App\Mail\RsvpSummaryMail;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendRsvpSummariesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_the_rsvp_summary_to_the_owner_only_once_after_the_deadline(): void
    {
        config(['mail.default' => 'smtp']);
        Mail::fake();

        $owner = User::factory()->create(['email' => 'organizador@example.com']);
        $invitation = Invitation::create([
            'user_id' => $owner->id,
            'slug' => 'fiesta-prueba',
            'title' => 'Fiesta de prueba',
            'event_date' => now()->addMonth(),
            'venue_name' => 'Salón',
            'google_maps_url' => 'https://maps.example.com',
            'rsvp_deadline' => now()->subMinute(),
        ]);

        $invitation->rsvps()->create([
            'attending' => true,
            'guest_name' => 'Ana Pérez',
            'total_attendees' => 2,
            'guests' => [
                ['name' => 'Ana Pérez', 'dietary_restriction' => 'ninguna', 'dietary_comment' => null],
                ['name' => 'Juan Pérez', 'dietary_restriction' => 'celiaco', 'dietary_comment' => 'Sin contaminación cruzada'],
            ],
            'message' => '¡Nos vemos!',
        ]);

        $this->artisan('invitations:send-rsvp-summaries')->assertSuccessful();

        Mail::assertSent(RsvpSummaryMail::class, function (RsvpSummaryMail $mail) use ($owner, $invitation) {
            return $mail->hasTo($owner->email) && $mail->invitation->is($invitation);
        });
        $this->assertNotNull($invitation->fresh()->rsvp_summary_sent_at);

        $this->artisan('invitations:send-rsvp-summaries')->assertSuccessful();
        Mail::assertSentCount(1);
    }

    public function test_it_does_not_send_a_summary_before_the_deadline(): void
    {
        config(['mail.default' => 'smtp']);
        Mail::fake();

        $owner = User::factory()->create();
        Invitation::create([
            'user_id' => $owner->id,
            'slug' => 'evento-futuro',
            'title' => 'Evento futuro',
            'event_date' => now()->addMonth(),
            'venue_name' => 'Salón',
            'google_maps_url' => 'https://maps.example.com',
            'rsvp_deadline' => now()->addDay(),
        ]);

        $this->artisan('invitations:send-rsvp-summaries')->assertSuccessful();

        Mail::assertNothingSent();
    }
}
