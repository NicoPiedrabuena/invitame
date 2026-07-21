<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resumen de invitados</title>
</head>
<body style="margin:0;background:#fff8f1;color:#292524;font-family:Arial,sans-serif">
    <div style="max-width:680px;margin:0 auto;padding:32px 20px">
        <div style="background:#ffffff;border:1px solid #ead8c2;border-radius:18px;padding:28px">
            <p style="margin:0;color:#a16207;font-size:12px;font-weight:bold;letter-spacing:2px;text-transform:uppercase">Cierre de reservas</p>
            <h1 style="margin:10px 0 8px;font-size:28px">{{ $invitation->title }}</h1>
            <p style="margin:0 0 24px;color:#78716c">Hola {{ $invitation->user->name }}, finalizó el plazo de confirmación. Este es el detalle recibido.</p>

            <div style="margin-bottom:26px;padding:18px;background:#f5f5f4;border-radius:12px">
                <strong>{{ $confirmedGuests }}</strong> invitados confirmados ·
                <strong>{{ $declinedResponses }}</strong> respuestas negativas
            </div>

            @forelse ($invitation->rsvps as $rsvp)
                <div style="padding:18px 0;border-top:1px solid #e7e5e4">
                    <p style="margin:0 0 6px;font-weight:bold">{{ $rsvp->guest_name }} — {{ $rsvp->attending ? 'Asistirá' : 'No asistirá' }}</p>
                    @if ($rsvp->attending)
                        <p style="margin:0 0 8px;color:#57534e">{{ $rsvp->total_attendees }} {{ $rsvp->total_attendees === 1 ? 'persona' : 'personas' }}</p>
                        @foreach ($rsvp->guests ?? [] as $guest)
                            <p style="margin:4px 0;color:#57534e">
                                • {{ $guest['name'] }} — {{ ucfirst($guest['dietary_restriction'] ?? 'ninguna') }}
                                @if (!empty($guest['dietary_comment'])) ({{ $guest['dietary_comment'] }}) @endif
                            </p>
                        @endforeach
                    @endif
                    @if ($rsvp->message)
                        <p style="margin:10px 0 0;color:#78716c;font-style:italic">“{{ $rsvp->message }}”</p>
                    @endif
                </div>
            @empty
                <p style="color:#78716c">No se recibieron confirmaciones para esta invitación.</p>
            @endforelse

            <p style="margin:26px 0 0">
                <a href="{{ route('invitations.show', $invitation) }}" style="display:inline-block;background:#40540f;color:#ffffff;text-decoration:none;padding:12px 18px;border-radius:9px;font-weight:bold">Ver panel de reservas</a>
            </p>
        </div>
    </div>
</body>
</html>
