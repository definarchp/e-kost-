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
        Schema::table('tenants', function (Blueprint $table) {
            // Catatan: proyek sudah memakai kolom `ktp_path`.
            // Anda minta menambah "database gambar KTP"; maka dibuat kolom tambahan
            // agar tetap kompatibel tanpa merusak existing flow.
            if (!Schema::hasColumn('tenants', 'ktp_images_path')) {
                $table->json('ktp_images_path')->nullable()->after('ktp_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'ktp_images_path')) {
                $table->dropColumn('ktp_images_path');
            }
        });
    }
};

