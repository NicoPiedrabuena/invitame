<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SpotifyRefreshTokenCommand extends Command
{
    protected $signature = 'spotify:refresh-token
        {code? : Codigo recibido en el callback de Spotify}
        {--redirect-uri= : Redirect URI registrado en Spotify}
        {--show-url : Solo muestra la URL de autorizacion}';

    protected $description = 'Obtiene el refresh token de Spotify para modificar playlists';

    public function handle(): int
    {
        $clientId = config('services.spotify.client_id');
        $clientSecret = config('services.spotify.client_secret');
        $redirectUri = $this->option('redirect-uri') ?: config('services.spotify.redirect_uri');

        if (! $clientId || ! $clientSecret) {
            $this->error('Faltan SPOTIFY_CLIENT_ID y/o SPOTIFY_CLIENT_SECRET en el .env.');

            return self::FAILURE;
        }

        $authorizationUrl = 'https://accounts.spotify.com/authorize?'.http_build_query([
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'scope' => 'playlist-modify-public playlist-modify-private',
        ], '', '&', PHP_QUERY_RFC3986);

        $this->info('1) Abri esta URL, acepta con la cuenta dueña de la playlist:');
        $this->line($authorizationUrl);

        if ($this->option('show-url')) {
            return self::SUCCESS;
        }

        $code = $this->argument('code') ?: $this->ask('2) Pega el code, o la URL completa del callback');
        $code = trim((string) $code);

        if (str_contains($code, '?')) {
            parse_str(parse_url($code, PHP_URL_QUERY) ?: '', $query);
            $code = $query['code'] ?? '';
        }

        if ($code === '') {
            $this->error('No se encontro ningun code para intercambiar.');

            return self::FAILURE;
        }

        $response = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirectUri,
            ]);

        if ($response->failed()) {
            $this->error('Spotify rechazo el intercambio del code.');
            $this->line($response->body());

            return self::FAILURE;
        }

        $refreshToken = $response->json('refresh_token');

        if (! $refreshToken) {
            $this->error('Spotify no devolvio refresh_token.');
            $this->line($response->body());

            return self::FAILURE;
        }

        $this->info('Listo. Agrega esto a tu .env:');
        $this->line('SPOTIFY_REFRESH_TOKEN='.$refreshToken);
        $this->newLine();
        $this->comment('Despues ejecuta: php artisan config:clear');

        return self::SUCCESS;
    }
}
