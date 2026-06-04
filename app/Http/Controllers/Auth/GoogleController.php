<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider as SocialiteProvider;
use Throwable;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        $callbackUrl = route('google.callback');

        $provider = Socialite::driver('google');

        if ($provider instanceof SocialiteProvider) {
            $provider->redirectUrl($callbackUrl);
        }

        return $provider->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $callbackUrl = route('google.callback');

            $provider = Socialite::driver('google');

            if ($provider instanceof SocialiteProvider) {
                $provider->redirectUrl($callbackUrl);
            }

            $googleUser = $provider->user();

            $user = User::query()
                ->where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                $user->update([
                    'name' => $user->name ?: $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'google_avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            } else {
                $user = User::create([
                    'name' => $googleUser->getName() ?: 'Google User',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'google_avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(40)),
                ]);
            }

            Auth::login($user, remember: true);
            request()->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false));
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('login')
                ->with('status', 'No se pudo iniciar sesión con Google. Intenta nuevamente.');
        }
    }
}
