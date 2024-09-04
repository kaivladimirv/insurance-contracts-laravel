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
        Schema::create('persons', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('last_name', 255);
            $table->string('first_name', 255);
            $table->string('middle_name', 255);
            $table->string('email', 255)->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->smallInteger('notifier_type')->nullable();
            $table->foreignId('company_id')->constrained('companies');
            $table->unique(['company_id', 'email']);
            $table->unique(['company_id', 'phone_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persons');
    }
};
