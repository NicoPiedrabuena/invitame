<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_rsvps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->boolean('attending');
            $table->string('guest_name', 160);
            $table->unsignedSmallInteger('total_attendees')->default(1);
            $table->json('guests')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();

            $table->index(['invitation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_rsvps');
    }
};
