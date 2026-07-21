<?php

namespace App\Console\Commands;

use App\Mail\RsvpSummaryMail;
use App\Models\Invitation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendRsvpSummaries extends Command
{
    protected $signature = 'invitations:send-rsvp-summaries';

    protected $description = 'Envía al propietario el resumen de reservas cuyo plazo finalizó';

    public function handle(): int
    {
        if (in_array(config('mail.default'), ['log', 'array'], true)) {
            $this->warn('El envío fue omitido porque no hay un proveedor de correo real configurado.');

            return self::SUCCESS;
        }

        $sent = 0;

        Invitation::query()
            ->whereNotNull('rsvp_deadline')
            ->where('rsvp_deadline', '<=', now())
            ->whereNull('rsvp_summary_sent_at')
            ->with(['user:id,name,email', 'rsvps' => fn ($query) => $query->oldest()])
            ->chunkById(100, function ($invitations) use (&$sent) {
                foreach ($invitations as $invitation) {
                    if (! $invitation->user?->email) {
                        $this->warn("La invitación {$invitation->id} no tiene un correo de propietario.");
                        continue;
                    }

                    Mail::to($invitation->user->email)->send(new RsvpSummaryMail($invitation));
                    $invitation->forceFill(['rsvp_summary_sent_at' => now()])->save();
                    $sent++;
                }
            });

        $this->info("Resúmenes enviados: {$sent}");

        return self::SUCCESS;
    }
}
