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
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique()->index();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->dateTime('event_date');
            $table->string('venue_name');
            $table->text('google_maps_url');
            $table->string('dress_code')->nullable();
            $table->string('bank_alias')->nullable();
            $table->text('drive_photos_url')->nullable();
            $table->text('spotify_playlist_url')->nullable();
            $table->json('theme_settings')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
