<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class SpotifyService
{
    public function searchTracks(string $query, int $limit = 8): array
    {
        $accessToken = $this->accessToken();
        $response = Http::withToken($accessToken)
            ->get('https://api.spotify.com/v1/search', [
                'q' => $query,
                'type' => 'track',
                'limit' => $limit,
            ]);

        if ($response->failed()) {
            throw ValidationException::withMessages([
                'song_query' => 'No se pudo buscar en Spotify.',
            ]);
        }

        return collect($response->json('tracks.items', []))
            ->map(fn ($track) => $this->formatTrack($track))
            ->filter(fn ($track) => ! empty($track['uri']))
            ->values()
            ->all();
    }

    public function addTrackUriToPlaylist(string $playlistUrl, string $trackUri): void
    {
        $playlistId = $this->extractPlaylistId($playlistUrl);

        if (! $playlistId) {
            throw ValidationException::withMessages([
                'track_uri' => 'No se pudo identificar la playlist de Spotify.',
            ]);
        }

        if (! str_starts_with($trackUri, 'spotify:track:')) {
            throw ValidationException::withMessages([
                'track_uri' => 'La cancion seleccionada no es valida.',
            ]);
        }

        $response = Http::withToken($this->accessToken())
            ->asJson()
            ->post("https://api.spotify.com/v1/playlists/{$playlistId}/tracks", [
                'uris' => [$trackUri],
            ]);

        if ($response->failed()) {
            throw ValidationException::withMessages([
                'track_uri' => 'Spotify no pudo agregar la cancion a la playlist.',
            ]);
        }
    }

    private function accessToken(): string
    {
        $clientId = config('services.spotify.client_id');
        $clientSecret = config('services.spotify.client_secret');
        $refreshToken = config('services.spotify.refresh_token');

        if (! $clientId || ! $clientSecret || ! $refreshToken) {
            throw ValidationException::withMessages([
                'song_query' => 'Faltan las credenciales de Spotify para agregar canciones.',
            ]);
        }

        $response = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]);

        if ($response->failed() || ! $response->json('access_token')) {
            throw ValidationException::withMessages([
                'song_query' => 'No se pudo conectar con Spotify.',
            ]);
        }

        return $response->json('access_token');
    }

    private function formatTrack(array $track): array
    {
        return [
            'id' => $track['id'] ?? null,
            'uri' => $track['uri'] ?? null,
            'name' => $track['name'] ?? '',
            'artist' => collect($track['artists'] ?? [])->pluck('name')->join(', '),
            'image_url' => $track['album']['images'][2]['url']
                ?? $track['album']['images'][1]['url']
                ?? $track['album']['images'][0]['url']
                ?? null,
        ];
    }

    private function extractPlaylistId(string $playlistUrl): ?string
    {
        if (preg_match('~spotify:playlist:([A-Za-z0-9]+)~', $playlistUrl, $matches)) {
            return $matches[1];
        }

        if (preg_match('~open\.spotify\.com/(?:embed/)?playlist/([A-Za-z0-9]+)~', $playlistUrl, $matches)) {
            return $matches[1];
        }

        return null;
    }

}
