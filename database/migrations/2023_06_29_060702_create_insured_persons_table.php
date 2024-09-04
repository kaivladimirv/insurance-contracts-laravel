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
        Schema::create('insured_persons', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('policy_number', 30);
            $table->boolean('is_allowed_to_exceed_limit')->default(false);
            $table->foreignId('contract_id')->constrained('contracts');
            $table->foreignId('person_id')->constrained('persons');
            $table->unique(['contract_id', 'person_id']);
            $table->unique(['contract_id', 'policy_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insured_persons');
    }
};
