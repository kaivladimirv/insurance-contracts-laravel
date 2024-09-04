<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->string('telegram_chat_invite_token')->nullable();
            $table->dateTime('telegram_invite_date_for_chat')->nullable();
            $table->smallInteger('telegram_chat_status')->nullable();
            $table->string('telegram_chat_id', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->dropColumn('telegram_chat_invite_token');
            $table->dropColumn('telegram_invite_date_for_chat');
            $table->dropColumn('telegram_chat_status');
            $table->dropColumn('telegram_chat_id');
        });
    }
};
