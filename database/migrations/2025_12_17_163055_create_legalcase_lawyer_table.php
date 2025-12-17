<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('legalcase_lawyer', function (Blueprint $table) {
            $table->id();
             $table->foreignId('legalcase_id')
            ->constrained('legal_case')
            ->cascadeOnDelete();
            $table->foreignId('lawyer_id')
            ->constrained('lawyers')
            ->cascadeOnDelete();
            $table->timestamps();
           $table->unique(['legalcase_id', 'lawyer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legalcase_lawyer');
    }
};
