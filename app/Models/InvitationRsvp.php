<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationRsvp extends Model
{
    use HasFactory;

    protected $fillable = [
        'invitation_id',
        'attending',
        'guest_name',
        'total_attendees',
        'guests',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'attending' => 'boolean',
            'guests' => 'array',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}
