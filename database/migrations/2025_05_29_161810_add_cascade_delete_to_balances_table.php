<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('balances', function (Blueprint $table) {
            $table->dropForeign(['insured_person_id']);
            $table->foreign('insured_person_id')->references('id')->on('insured_persons')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('balances', function (Blueprint $table) {
            $table->dropForeign(['insured_person_id']);
            $table->foreign('insured_person_id')->references('id')->on('insured_persons');
        });
    }
};
