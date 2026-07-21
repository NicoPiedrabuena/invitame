<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitation_messages', function (Blueprint $table) {
            $table->string('category', 30)->default('otros')->after('guest_name');
        });
    }

    public function down(): void
    {
        Schema::table('invitation_messages', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
