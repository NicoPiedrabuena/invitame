<?php

use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicInvitationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

    Route::get('/dashboard', [InvitationController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('invitations', InvitationController::class)->except(['index']);
});

require __DIR__.'/auth.php';

Route::post('/{slug}/messages', [PublicInvitationController::class, 'storeMessage'])
    ->where('slug', '^(?!login|register|dashboard|profile|forgot-password|reset-password|verify-email|auth|password|invitations).*$')
    ->name('invitations.public.messages.store');

Route::post('/{slug}/rsvp', [PublicInvitationController::class, 'storeRsvp'])
    ->where('slug', '^(?!login|register|dashboard|profile|forgot-password|reset-password|verify-email|auth|password|invitations).*$')
    ->name('invitations.public.rsvp.store');

Route::get('/{slug}/spotify/tracks/search', [PublicInvitationController::class, 'searchSpotifyTracks'])
    ->where('slug', '^(?!login|register|dashboard|profile|forgot-password|reset-password|verify-email|auth|password|invitations).*$')
    ->name('invitations.public.spotify.tracks.search');

Route::post('/{slug}/spotify/tracks/add', [PublicInvitationController::class, 'addSpotifyTrack'])
    ->where('slug', '^(?!login|register|dashboard|profile|forgot-password|reset-password|verify-email|auth|password|invitations).*$')
    ->name('invitations.public.spotify.tracks.add');

Route::get('/{slug}', [PublicInvitationController::class, 'show'])
    ->where('slug', '^(?!login|register|dashboard|profile|forgot-password|reset-password|verify-email|auth|password|invitations).*$')
    ->name('invitations.public.show');
