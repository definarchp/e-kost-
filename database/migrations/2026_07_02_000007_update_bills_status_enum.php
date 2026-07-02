<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // For MySQL: modify enum by recreating column.
        // Mapping:
        // - belum_lunas -> belum_dibayar
        // - lunas -> lunas
        $driver = DB::connection()->getDriverName();
        if ($driver !== 'mysql') {
            // Fallback: still attempt to alter if supported.
        }

        Schema::table('bills', function (Blueprint $table) {
            // Rename old column then recreate.
            if (Schema::hasColumn('bills', 'status')) {
                $table->renameColumn('status', 'status_old');
            }
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->enum('status', ['belum_dibayar', 'menunggu_verifikasi', 'lunas'])
                ->default('belum_dibayar')
                ->after('amount');
        });

        // Data migration
        DB::statement("UPDATE bills SET status = 'belum_dibayar' WHERE status_old = 'belum_lunas'");
        DB::statement("UPDATE bills SET status = 'lunas' WHERE status_old = 'lunas'");
        DB::statement("UPDATE bills SET status = 'belum_dibayar' WHERE status IS NULL");

        Schema::table('bills', function (Blueprint $table) {
            if (Schema::hasColumn('bills', 'status_old')) {
                $table->dropColumn('status_old');
            }
        });
    }

    public function down(): void
    {
        // Reverse mapping (best effort)
        Schema::table('bills', function (Blueprint $table) {
            if (Schema::hasColumn('bills', 'status')) {
                $table->renameColumn('status', 'status_new');
            }
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->enum('status', ['lunas', 'belum_lunas'])->default('belum_lunas')->after('amount');
        });

        DB::statement("UPDATE bills SET status = 'belum_lunas' WHERE status_new = 'belum_dibayar' OR status_new = 'menunggu_verifikasi'");
        DB::statement("UPDATE bills SET status = 'lunas' WHERE status_new = 'lunas'");
        DB::statement("UPDATE bills SET status = 'belum_lunas' WHERE status IS NULL");

        Schema::table('bills', function (Blueprint $table) {
            if (Schema::hasColumn('bills', 'status_new')) {
                $table->dropColumn('status_new');
            }
        });
    }
};

