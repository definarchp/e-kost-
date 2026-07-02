<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FinancialReportController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('type', 'monthly');
        $order = $request->query('order', 'newest');

        $type = in_array($type, ['monthly', 'yearly'], true) ? $type : 'monthly';
        $order = in_array($order, ['newest', 'oldest'], true) ? $order : 'newest';
        $orderDir = $order === 'oldest' ? 'ASC' : 'DESC';

        // Rekap periode buku (cut-off tanggal 10) hanya untuk bill berstatus 'lunas'
        // Periode buku ditentukan dari bills.updated_at:
        // - jika DAY(updated_at) < 10 => periode = bulan sebelumnya
        // - else => periode = bulan aktual
        if ($type === 'monthly') {
            $recces = $this->monthlySummary($orderDir);
        } else {
            $recces = $this->yearlySummary($orderDir);
        }

        $detail = $this->transactionDetails($type, $orderDir, $request);

        return view('owner.financial-reports.keuangan-kost', [
            'type' => $type,
            'order' => $order,
            'summary' => $recces,
            'detail' => $detail,
        ]);
    }

    private function bookPeriodExpr(string $updatedAtColumn = 'updated_at'): string
    {
        // Menghasilkan YYYY-MM periode buku berdasarkan cut-off tanggal 10.
        // MySQL:
        // CASE WHEN DAY(updated_at) < 10 THEN DATE_FORMAT(DATE_SUB(updated_at, INTERVAL 1 MONTH), '%Y-%m')
        // ELSE DATE_FORMAT(updated_at, '%Y-%m') END
        return "CASE WHEN DAY({$updatedAtColumn}) < 10 "
            . "THEN DATE_FORMAT(DATE_SUB({$updatedAtColumn}, INTERVAL 1 MONTH), '%Y-%m') "
            . "ELSE DATE_FORMAT({$updatedAtColumn}, '%Y-%m') END";
    }

    private function monthlySummary(string $orderDir): array
    {
        $periodExpr = $this->bookPeriodExpr('b.updated_at');

        $rows = Bill::query()
            ->from('bills as b')
            ->selectRaw("{$periodExpr} as periode_buku")
            ->selectRaw('SUM(b.amount) as total_pendapatan')
            ->selectRaw('COUNT(*) as jumlah_invoice')
            ->where('b.status', 'lunas')
            ->groupByRaw("{$periodExpr}")
            ->orderByRaw('periode_buku ' . $orderDir)
            ->get();

        return $rows->map(fn($r) => [
            'periode_buku' => $r->periode_buku,
            'label' => Carbon::parse($r->periode_buku . '-01')->translatedFormat('F Y'),
            'total_pendapatan' => (int) $r->total_pendapatan,
            'jumlah_invoice' => (int) $r->jumlah_invoice,
        ])->all();
    }

    private function yearlySummary(string $orderDir): array
    {
        $periodExpr = $this->bookPeriodExpr('b.updated_at');

        // Tahun dari periode buku (bukan dari bill_month biasa)
        // RIGHT(YYYY-MM, 4) => YEAR(periode buku) cara MySQL:
        $yearExpr = "LEFT({$periodExpr}, 4)";

        $rows = Bill::query()
            ->from('bills as b')
            ->selectRaw("{$yearExpr} as periode_tahun")
            ->selectRaw('SUM(b.amount) as total_pendapatan')
            ->selectRaw('COUNT(*) as jumlah_invoice')
            ->where('b.status', 'lunas')
            ->groupByRaw("{$yearExpr}")
            ->orderByRaw('periode_tahun ' . $orderDir)
            ->get();

        return $rows->map(fn($r) => [
            'periode_tahun' => $r->periode_tahun,
            'label' => (string) $r->periode_tahun,
            'total_pendapatan' => (int) $r->total_pendapatan,
            'jumlah_invoice' => (int) $r->jumlah_invoice,
        ])->all();
    }

    private function transactionDetails(string $type, string $orderDir, Request $request): LengthAwarePaginator
    {
        $periodExpr = $this->bookPeriodExpr('b.updated_at');
        $yearExpr = "LEFT({$periodExpr}, 4)";

        // Pagination + detail transaksi.
        // Untuk filter detail:
        // - monthly: tampilkan transaksi untuk semua periode buku (tidak dibatasi)
        //   (UI hanya ringkasan per periode di grid atas).
        // - yearly: sama, grid atas per tahun.
        // Sorting: berdasarkan waktu periode buku (periode_buku) agar konsisten.

        $base = Bill::query()
            ->from('bills as b')
            ->with(['tenant.user', 'tenant.room'])
            ->where('b.status', 'lunas')
            ->select([
                'b.*',
            ])
            ->selectRaw("{$periodExpr} as periode_buku")
            ->selectRaw("{$yearExpr} as tahun_buku")
            ->orderByRaw('b.updated_at ' . $orderDir);

        // Ambil halaman
        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage > 0 ? $perPage : 10;

        /** @var LengthAwarePaginator $paginator */
        $paginator = $base->paginate($perPage);

        // Pastikan relasi sudah ada; Laravel paginate akan query ulang, namun with sudah ter-include.
        $paginator->getCollection()->transform(function ($bill) {
            $bill->periode_buku = $bill->periode_buku ?? null;
            $bill->tahun_buku = $bill->tahun_buku ?? null;
            return $bill;
        });

        return $paginator;
    }
}

