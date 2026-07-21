<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class InvitationController extends Controller
{
    /**
     * Display the authenticated user's dashboard with their invitations.
     */
   public function dashboard(): Response
{
    $invitations = Invitation::query()
        ->where('user_id', Auth::id())
        ->latest()
        ->get([
            'id',
            'slug',
            'title',
            'subtitle',
            'event_date',
            'event_end_date',
            'venue_name',
            'created_at',
            'updated_at',
        ]);

    return Inertia::render('Dashboard', [
        'invitations' => $invitations,
    ]);
}

    /**
     * Show the form for creating a new resource.
     */
   public function create(): Response
{
    return Inertia::render('Invitations/CreateInvitationWizard');
}
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'event_end_date' => ['nullable', 'date', 'after_or_equal:event_date'],
            'venue_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'google_maps_url' => ['required', 'url', 'max:2048'],
            'dress_code' => ['nullable', 'string', 'max:120'],
            'dress_code_description' => ['nullable', 'string', 'max:5000'],
            'dress_code_allowed_images' => ['nullable', 'array'],
            'dress_code_allowed_images.*' => ['file', 'image', 'max:4096'],
            'dress_code_not_allowed_images' => ['nullable', 'array'],
            'dress_code_not_allowed_images.*' => ['file', 'image', 'max:4096'],
            'gifts_message' => ['nullable', 'string', 'max:5000'],
            'bank_alias' => ['nullable', 'string', 'max:255'],
            'drive_photos_url' => ['nullable', 'url', 'max:2048'],
            'spotify_playlist_url' => ['nullable', 'url', 'max:2048'],
            'spotify_iframe_code' => ['nullable', 'string', 'max:5000'],
            'youtube_music_url' => ['nullable', 'url', 'max:2048'],
            'message_wall_enabled' => ['boolean'],
            'rsvp_deadline' => ['nullable', 'date'],
            'rsvp_companions' => ['nullable', 'array'],
            'rsvp_companions.*.name' => ['required_with:rsvp_companions', 'string', 'max:255'],
            'rsvp_companions.*.dietary_restrictions' => ['nullable', 'string', 'max:255'],
            'rsvp_message' => ['nullable', 'string', 'max:2000'],
            'theme_settings' => ['nullable', 'array'],
            'background_portada' => ['nullable', 'image', 'max:4096'],
            'background_cuenta_regresiva' => ['nullable', 'image', 'max:4096'],
            'background_ubicacion' => ['nullable', 'image', 'max:4096'],
            'background_dress_code' => ['nullable', 'image', 'max:4096'],
            'background_regalos' => ['nullable', 'image', 'max:4096'],
            'background_fotos' => ['nullable', 'image', 'max:4096'],
            'background_musica' => ['nullable', 'image', 'max:4096'],
            'background_muro' => ['nullable', 'image', 'max:4096'],
            'background_rsvp' => ['nullable', 'image', 'max:4096'],
        ]);

        $slugBase = Str::slug(trim($data['title'].' '.($data['subtitle'] ?? ''))) ?: 'invitacion';
        $slug = $slugBase;
        $suffix = 2;

        while (Invitation::query()->where('slug', $slug)->exists()) {
            $slug = $slugBase.'-'.$suffix;
            $suffix++;
        }

        $spotifyPlaylistUrl = null;
        if (! empty($data['spotify_iframe_code']) && preg_match('/src="([^"]+)"/i', $data['spotify_iframe_code'], $matches)) {
            $spotifyPlaylistUrl = $matches[1];
        }

        $dressCodeDescription = $data['dress_code_description'] ?? $data['dress_code'] ?? null;
        $legacyDressCode = $dressCodeDescription ? Str::limit($dressCodeDescription, 120, '') : null;
        $resolvedSpotifyPlaylistUrl = $spotifyPlaylistUrl ?? $data['spotify_playlist_url'] ?? null;

        $allowedImages = collect($request->file('dress_code_allowed_images', []))
            ->map(fn ($file) => $file->store('invitations/dress-code', 'public'))
            ->values()
            ->all();

        $notAllowedImages = collect($request->file('dress_code_not_allowed_images', []))
            ->map(fn ($file) => $file->store('invitations/dress-code', 'public'))
            ->values()
            ->all();

        $sectionBackgrounds = data_get($data, 'theme_settings.section_backgrounds', []);
        $backgroundMapping = [
            'background_portada' => 'portada',
            'background_cuenta_regresiva' => 'cuenta_regresiva',
            'background_ubicacion' => 'ubicacion',
            'background_dress_code' => 'dress_code',
            'background_regalos' => 'regalos',
            'background_fotos' => 'fotos',
            'background_musica' => 'musica',
            'background_muro' => 'muro',
            'background_rsvp' => 'rsvp',
        ];

        foreach ($backgroundMapping as $inputField => $sectionKey) {
            if ($request->hasFile($inputField)) {
                $sectionBackgrounds[$sectionKey] = $request->file($inputField)->store('invitations/section-backgrounds', 'public');
            }
        }

        $themeSettings = $data['theme_settings'] ?? [];
        if (data_get($themeSettings, 'appearance.repeat_background_all_sections')) {
            $sharedBackground = $sectionBackgrounds['portada'] ?? null;

            foreach ($backgroundMapping as $sectionKey) {
                $sectionBackgrounds[$sectionKey] = $sharedBackground;
            }
        }

        $themeSettings['section_backgrounds'] = $sectionBackgrounds;

        $invitation = DB::transaction(function () use ($data, $allowedImages, $notAllowedImages, $slug, $legacyDressCode, $dressCodeDescription, $resolvedSpotifyPlaylistUrl, $themeSettings) {
            return Invitation::create([
                'user_id' => Auth::id(),
                'slug' => $slug,
                'title' => $data['title'],
                'subtitle' => $data['subtitle'] ?? null,
                'event_date' => $data['event_date'],
                'event_end_date' => $data['event_end_date'] ?? null,
                'venue_name' => $data['venue_name'],
                'address' => $data['address'] ?? null,
                'google_maps_url' => $data['google_maps_url'],
                'dress_code' => $legacyDressCode,
                'dress_code_description' => $dressCodeDescription,
                'dress_code_allowed_images' => $allowedImages,
                'dress_code_not_allowed_images' => $notAllowedImages,
                'gifts_message' => $data['gifts_message'] ?? null,
                'bank_alias' => $data['bank_alias'] ?? null,
                'drive_photos_url' => $data['drive_photos_url'] ?? null,
                'spotify_iframe_code' => $data['spotify_iframe_code'] ?? null,
                'spotify_playlist_url' => $resolvedSpotifyPlaylistUrl,
                'youtube_music_url' => $data['youtube_music_url'] ?? null,
                'message_wall_enabled' => $data['message_wall_enabled'] ?? false,
                'rsvp_deadline' => $data['rsvp_deadline'] ?? null,
                'rsvp_companions' => $data['rsvp_companions'] ?? [],
                'rsvp_message' => $data['rsvp_message'] ?? null,
                'theme_settings' => $themeSettings,
            ]);
        });

        return redirect()->route('invitations.show', $invitation)
            ->with('status', 'Invitacion creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invitation $invitation): Response
    {
        $invitation = $this->ownedInvitation($invitation);

        $rsvps = $invitation->rsvps()
            ->latest()
            ->get(['id', 'attending', 'guest_name', 'total_attendees', 'guests', 'message', 'created_at']);

        $messages = $invitation->messages()
            ->latest()
            ->get(['id', 'guest_name', 'category', 'message', 'created_at']);

        return Inertia::render('Invitations/Show', [
            'invitation' => $invitation,
            'publicUrl' => url('/'.$invitation->slug),
            'rsvps' => $rsvps,
            'messages' => $messages,
            'summary' => [
                'confirmedGuests' => $rsvps->where('attending', true)->sum('total_attendees'),
                'declinedResponses' => $rsvps->where('attending', false)->count(),
                'responseCount' => $rsvps->count(),
                'messageCount' => $messages->count(),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invitation $invitation): Response
{
    $invitation = $this->ownedInvitation($invitation);

    return Inertia::render('Invitations/CreateInvitationWizard', [
        'mode' => 'edit',
        'invitation' => $invitation,
    ]);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invitation $invitation): RedirectResponse
    {
        $invitation = $this->ownedInvitation($invitation);

        $data = $request->validate([
            'slug' => ['nullable', 'string', 'max:120', 'alpha_dash', Rule::unique('invitations', 'slug')->ignore($invitation->id)],            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'event_end_date' => ['nullable', 'date', 'after_or_equal:event_date'],
            'venue_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'google_maps_url' => ['required', 'url', 'max:2048'],
            'dress_code' => ['nullable', 'string', 'max:120'],
            'dress_code_description' => ['nullable', 'string', 'max:5000'],
            'dress_code_allowed_images' => ['nullable', 'array'],
            'dress_code_allowed_images.*' => ['image', 'max:4096'],
            'dress_code_not_allowed_images' => ['nullable', 'array'],
            'dress_code_not_allowed_images.*' => ['image', 'max:4096'],
            'gifts_message' => ['nullable', 'string', 'max:5000'],
            'bank_alias' => ['nullable', 'string', 'max:255'],
            'drive_photos_url' => ['nullable', 'url', 'max:2048'],
            'spotify_playlist_url' => ['nullable', 'url', 'max:2048'],
            'spotify_iframe_code' => ['nullable', 'string', 'max:5000'],
            'youtube_music_url' => ['nullable', 'url', 'max:2048'],
            'message_wall_enabled' => ['boolean'],
            'rsvp_deadline' => ['nullable', 'date'],
            'rsvp_companions' => ['nullable', 'array'],
            'rsvp_companions.*.name' => ['required_with:rsvp_companions', 'string', 'max:255'],
            'rsvp_companions.*.dietary_restrictions' => ['nullable', 'string', 'max:255'],
            'rsvp_message' => ['nullable', 'string', 'max:2000'],
            'theme_settings' => ['nullable', 'array'],
            'background_portada' => ['nullable', 'image', 'max:4096'],
            'background_cuenta_regresiva' => ['nullable', 'image', 'max:4096'],
            'background_ubicacion' => ['nullable', 'image', 'max:4096'],
            'background_dress_code' => ['nullable', 'image', 'max:4096'],
            'background_regalos' => ['nullable', 'image', 'max:4096'],
            'background_fotos' => ['nullable', 'image', 'max:4096'],
            'background_musica' => ['nullable', 'image', 'max:4096'],
            'background_muro' => ['nullable', 'image', 'max:4096'],
            'background_rsvp' => ['nullable', 'image', 'max:4096'],
        ]);

        $dressCodeDescription = $data['dress_code_description'] ?? $data['dress_code'] ?? null;
        $legacyDressCode = $dressCodeDescription ? Str::limit($dressCodeDescription, 120, '') : null;

        $spotifyPlaylistUrl = null;
        if (! empty($data['spotify_iframe_code']) && preg_match('/src="([^"]+)"/i', $data['spotify_iframe_code'], $matches)) {
            $spotifyPlaylistUrl = $matches[1];
        }

        $resolvedSpotifyPlaylistUrl = $spotifyPlaylistUrl ?? $data['spotify_playlist_url'] ?? null;

        $allowedImages = $invitation->dress_code_allowed_images ?? [];
        if ($request->hasFile('dress_code_allowed_images')) {
            $allowedImages = collect($request->file('dress_code_allowed_images'))
                ->map(fn ($file) => $file->store('invitations/dress-code', 'public'))
                ->values()
                ->all();
        }

        $notAllowedImages = $invitation->dress_code_not_allowed_images ?? [];
        if ($request->hasFile('dress_code_not_allowed_images')) {
            $notAllowedImages = collect($request->file('dress_code_not_allowed_images'))
                ->map(fn ($file) => $file->store('invitations/dress-code', 'public'))
                ->values()
                ->all();
        }

        $sectionBackgrounds = data_get($data, 'theme_settings.section_backgrounds', $invitation->theme_settings['section_backgrounds'] ?? []);
        $backgroundMapping = [
            'background_portada' => 'portada',
            'background_cuenta_regresiva' => 'cuenta_regresiva',
            'background_ubicacion' => 'ubicacion',
            'background_dress_code' => 'dress_code',
            'background_regalos' => 'regalos',
            'background_fotos' => 'fotos',
            'background_musica' => 'musica',
            'background_muro' => 'muro',
            'background_rsvp' => 'rsvp',
        ];

        foreach ($backgroundMapping as $inputField => $sectionKey) {
            if ($request->hasFile($inputField)) {
                $sectionBackgrounds[$sectionKey] = $request->file($inputField)->store('invitations/section-backgrounds', 'public');
            }
        }

        $themeSettings = $data['theme_settings'] ?? $invitation->theme_settings ?? [];
        if (data_get($themeSettings, 'appearance.repeat_background_all_sections')) {
            $sharedBackground = $sectionBackgrounds['portada'] ?? null;

            foreach ($backgroundMapping as $sectionKey) {
                $sectionBackgrounds[$sectionKey] = $sharedBackground;
            }
        }

        $themeSettings['section_backgrounds'] = $sectionBackgrounds;
        $data['slug'] = $data['slug'] ?? $invitation->slug;
        $invitation->update([
            ...$data,
            'dress_code' => $legacyDressCode,
            'dress_code_description' => $dressCodeDescription,
            'dress_code_allowed_images' => $allowedImages,
            'dress_code_not_allowed_images' => $notAllowedImages,
            'spotify_playlist_url' => $resolvedSpotifyPlaylistUrl,
            'theme_settings' => $themeSettings,
        ]);

        return redirect()->route('invitations.show', $invitation)
            ->with('status', 'Invitacion actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invitation $invitation): RedirectResponse
    {
        $invitation = $this->ownedInvitation($invitation);
        $invitation->delete();

       return redirect()->route('dashboard')
    ->with('status', 'Invitacion eliminada correctamente.');
    }

    private function ownedInvitation(Invitation $invitation): Invitation
    {
        abort_unless($invitation->user_id === Auth::id(), 403);

        return $invitation;
    }
}
