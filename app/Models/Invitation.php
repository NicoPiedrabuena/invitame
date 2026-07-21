<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slug',
        'title',
        'subtitle',
        'event_date',
        'event_end_date',
        'venue_name',
        'address',
        'google_maps_url',
        'dress_code',
        'dress_code_description',
        'dress_code_allowed_images',
        'dress_code_not_allowed_images',
        'gifts_message',
        'bank_alias',
        'drive_photos_url',
        'spotify_iframe_code',
        'spotify_playlist_url',
        'youtube_music_url',
        'message_wall_enabled',
        'rsvp_deadline',
        'rsvp_summary_sent_at',
        'rsvp_companions',
        'rsvp_message',
        'theme_settings',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
            'event_end_date' => 'datetime',
            'rsvp_deadline' => 'datetime',
            'rsvp_summary_sent_at' => 'datetime',
            'message_wall_enabled' => 'boolean',
            'dress_code_allowed_images' => 'array',
            'dress_code_not_allowed_images' => 'array',
            'rsvp_companions' => 'array',
            'theme_settings' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(InvitationMessage::class);
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(InvitationRsvp::class);
    }
}
