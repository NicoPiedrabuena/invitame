<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Services\SpotifyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicInvitationController extends Controller
{
    public function show(string $slug): Response
    {
        $invitation = Invitation::query()
            ->where('slug', $slug)
            ->firstOrFail();

        return Inertia::render('Public/Invitation', [
            'invitation' => $invitation,
            'messages' => $invitation->messages()
                ->latest()
                ->get(['id', 'guest_name', 'category', 'message', 'created_at']),
        ]);
    }

    public function storeMessage(Request $request, string $slug): RedirectResponse
    {
        $invitation = Invitation::query()
            ->where('slug', $slug)
            ->firstOrFail();

        abort_unless($invitation->message_wall_enabled, 404);

        $data = $request->validate([
            'guest_name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'in:familia,amigos,otros'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $invitation->messages()->create($data);

        return back()->with('status', 'Mensaje publicado correctamente.');
    }

    public function storeRsvp(Request $request, string $slug): RedirectResponse
    {
        $invitation = Invitation::query()
            ->where('slug', $slug)
            ->firstOrFail();

        $data = $request->validate([
            'attending' => ['required', 'boolean'],
            'guest_name' => ['required', 'string', 'max:160'],
            'guests' => ['required_if:attending,true', 'array', 'max:20'],
            'guests.*.name' => ['required_if:attending,true', 'nullable', 'string', 'max:160'],
            'guests.*.dietary_restriction' => ['nullable', 'string', 'in:ninguna,vegetariano,vegano,celiaco,diabetico,otro'],
            'guests.*.dietary_comment' => ['nullable', 'string', 'max:500'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $guests = collect($data['guests'] ?? [])
            ->map(fn ($guest) => [
                'name' => $guest['name'] ?? '',
                'dietary_restriction' => $guest['dietary_restriction'] ?? 'ninguna',
                'dietary_comment' => $guest['dietary_comment'] ?? null,
            ])
            ->values()
            ->all();

        $invitation->rsvps()->create([
            'attending' => $data['attending'],
            'guest_name' => $data['guest_name'],
            'total_attendees' => $data['attending'] ? count($guests) : 0,
            'guests' => $data['attending'] ? $guests : [],
            'message' => $data['message'] ?? null,
        ]);

        return back()->with('status', 'Asistencia confirmada correctamente.');
    }

    public function searchSpotifyTracks(Request $request, string $slug, SpotifyService $spotify): JsonResponse
    {
        $invitation = Invitation::query()
            ->where('slug', $slug)
            ->firstOrFail();

        abort_unless($invitation->spotify_playlist_url, 404);

        $data = $request->validate([
            'query' => ['required', 'string', 'max:255'],
        ]);

        return response()->json([
            'tracks' => $spotify->searchTracks($data['query']),
        ]);
    }

    public function addSpotifyTrack(Request $request, string $slug, SpotifyService $spotify): JsonResponse
    {
        $invitation = Invitation::query()
            ->where('slug', $slug)
            ->firstOrFail();

        abort_unless($invitation->spotify_playlist_url, 404);

        $data = $request->validate([
            'track_uri' => ['required', 'string', 'max:255'],
        ]);

        $spotify->addTrackUriToPlaylist($invitation->spotify_playlist_url, $data['track_uri']);

        return response()->json([
            'message' => 'Cancion agregada a la playlist.',
        ]);
    }
}
