<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
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
        ]);
    }
}
