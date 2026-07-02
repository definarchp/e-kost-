<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan jika bukti_pembayaran_path terisi, status minimal menunggu_verifikasi.
        // Tujuannya agar UI tenant menampilkan gambar setelah upload.
        DB::table('bills')
            ->whereNotNull('bukti_pembayaran_path')
            ->where('bukti_pembayaran_path', '!=', '')
            ->whereIn('status', ['belum_lunas', 'belum_dibayar', 'belum_dibayar ', ''] )
            ->update(['status' => 'menunggu_verifikasi']);
    }

    public function down(): void
    {
        // no-op
    }
};

