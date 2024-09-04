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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name', 255)->unique();
            $table->string('email', 255)->unique();
            $table->string('password_hash', 255);
            $table->string('email_confirm_token', 255)->nullable();
            $table->string('new_email', 255)->nullable();
            $table->string('new_email_confirm_token', 255)->nullable();
            $table->boolean('is_email_confirmed')->default(true);
            $table->boolean('is_deleted')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
