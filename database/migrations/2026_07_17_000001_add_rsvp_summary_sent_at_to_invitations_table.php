<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->timestamp('rsvp_summary_sent_at')->nullable()->after('rsvp_deadline');
            $table->index(['rsvp_deadline', 'rsvp_summary_sent_at']);
        });
    }

    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropIndex(['rsvp_deadline', 'rsvp_summary_sent_at']);
            $table->dropColumn('rsvp_summary_sent_at');
        });
    }
};
