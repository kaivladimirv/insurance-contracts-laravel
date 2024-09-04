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
        Schema::create('balances', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('service_id');
            $table->smallInteger('limit_type');
            $table->double('balance');
            $table->foreignId('contract_id')->constrained('contracts');
            $table->foreignId('insured_person_id')->constrained('insured_persons');
            $table->unique(['insured_person_id', 'service_id', 'limit_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balances');
    }
};
