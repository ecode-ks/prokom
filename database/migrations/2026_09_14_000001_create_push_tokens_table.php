<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('push_tokens')) {
            return;
        }

        Schema::create('push_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('recipient_type', 20);
            $table->unsignedBigInteger('recipient_id');
            $table->string('expo_push_token', 191)->unique();
            $table->string('platform', 20)->nullable();
            $table->timestamps();

            $table->index(['company_id', 'recipient_type', 'recipient_id'], 'push_tokens_recipient_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_tokens');
    }
};
