<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'message_wall_enabled',
        'rsvp_deadline',
        'rsvp_companions',
        'rsvp_message',
        'theme_settings',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
            'rsvp_deadline' => 'datetime',
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
}
