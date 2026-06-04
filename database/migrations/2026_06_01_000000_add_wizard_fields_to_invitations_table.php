<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->text('address')->nullable()->after('venue_name');
            $table->text('dress_code_description')->nullable()->after('google_maps_url');
            $table->json('dress_code_allowed_images')->nullable()->after('dress_code_description');
            $table->json('dress_code_not_allowed_images')->nullable()->after('dress_code_allowed_images');
            $table->text('gifts_message')->nullable()->after('dress_code_not_allowed_images');
            $table->longText('spotify_iframe_code')->nullable()->after('drive_photos_url');
            $table->boolean('message_wall_enabled')->default(false)->after('spotify_playlist_url');
            $table->dateTime('rsvp_deadline')->nullable()->after('message_wall_enabled');
            $table->json('rsvp_companions')->nullable()->after('rsvp_deadline');
            $table->text('rsvp_message')->nullable()->after('rsvp_companions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'dress_code_description',
                'dress_code_allowed_images',
                'dress_code_not_allowed_images',
                'gifts_message',
                'spotify_iframe_code',
                'message_wall_enabled',
                'rsvp_deadline',
                'rsvp_companions',
                'rsvp_message',
            ]);
        });
    }
};