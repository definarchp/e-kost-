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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('bill_month', 7);
            $table->unsignedInteger('amount');
            $table->enum('status', ['lunas', 'belum_lunas'])->default('belum_lunas');
            $table->string('va_number')->unique();
            
            // KUNCINYA DI SINI: Menambahkan kolom untuk menyimpan path bukti pembayaran
            $table->text('bukti_pembayaran_path')->nullable(); 
            
            $table->timestamps();

            $table->unique(['tenant_id', 'bill_month']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};