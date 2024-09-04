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
        Schema::create('provided_services', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->dateTime('date_of_service');
            $table->bigInteger('service_id');
            $table->string('service_name', 255);
            $table->smallInteger('limit_type');
            $table->double('quantity');
            $table->double('price');
            $table->double('amount');
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('contract_id')->constrained('contracts');
            $table->foreignId('insured_person_id')->constrained('insured_persons');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provided_services');
    }
};
