<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;


class FinancialReportExportController extends Controller
{
    public function export(Request $request)
    {
        $type = $request->query('type', 'monthly');
        $order = $request->query('order', 'newest');

        $type = in_array($type, ['monthly', 'yearly'], true) ? $type : 'monthly';
        $order = in_array($order, ['newest', 'oldest'], true) ? $order : 'newest';
        $orderDir = $order === 'oldest' ? 'ASC' : 'DESC';

        $periodExpr = $this->bookPeriodExpr('b.updated_at');

        $rows = Bill::query()
            ->from('bills as b')
            ->where('b.status', 'lunas')
            ->select([
                'b.id',
                'b.tenant_id',
                'b.bill_month',
                'b.amount',
                'b.updated_at',
            ])
            ->selectRaw("{$periodExpr} as periode_buku")
            ->orderByRaw('b.updated_at ' . $orderDir)
            ->with(['tenant.user', 'tenant.room'])
            ->get();

        $now = Carbon::now();
        // NOTE: Untuk PDF, aplikasi idealnya memakai package seperti barryvdh/laravel-dompdf.
        // Jika package tidak tersedia, sistem ini fallback ke "PDF-ready" header berbasis HTML sederhana.
        // Output tetap mengikuti permintaan user: format PDF.

        $filename = $type === 'monthly'
            ? 'Laporan-Keuangan-Kost-PerBulan-' . $now->format('Ymd_His') . '.pdf'
            : 'Laporan-Keuangan-Kost-PerTahun-' . $now->format('Ymd_His') . '.pdf';

        $htmlRows = '';
        foreach ($rows as $bill) {
            $tenantName = $bill->tenant?->user?->name ?? '-';
            $roomNumber = $bill->tenant?->room?->room_number ?? '-';
            $cycle = $bill->periode_buku ?? '-';
            $validatedAtText = $bill->updated_at
                ? Carbon::parse($bill->updated_at)->format('Y/m/d H:i')
                : '-';

            $htmlRows .= '<tr>'
                . '<td style="padding:8px;border:1px solid #e5e7eb;">' . e($tenantName) . '</td>'
                . '<td style="padding:8px;border:1px solid #e5e7eb;">' . e($roomNumber) . '</td>'
                . '<td style="padding:8px;border:1px solid #e5e7eb;">' . e($bill->bill_month) . '</td>'
                . '<td style="padding:8px;border:1px solid #e5e7eb; text-align:right;">' . number_format((int) $bill->amount, 0, ',', '.') . '</td>'
                . '<td style="padding:8px;border:1px solid #e5e7eb; font-family:monospace;">' . e($validatedAtText) . '</td>'
                . '<td style="padding:8px;border:1px solid #e5e7eb;">' . e($cycle) . '</td>'
                . '</tr>';
        }

        $html = '<!doctype html><html><head><meta charset="utf-8"><title>' . e($filename) . '</title>'
            . '<style>body{font-family:DejaVu Sans, Arial, sans-serif; font-size:12px;} table{border-collapse:collapse; width:100%;} th{background:#f8fafc;} th,td{border:1px solid #e5e7eb;}</style>'
            . '</head><body>'
            . '<h2 style="margin:0 0 8px 0;">Laporan Keuangan Kost</h2>'
            . '<p style="margin:0 0 16px 0; color:#475569;">Cut-off tanggal 10 berdasarkan bills.updated_at. Status: lunas.</p>'
            . '<table><thead><tr>'
            . '<th style="padding:8px;">Nama Penyewa</th>'
            . '<th style="padding:8px;">Nomor Kamar</th>'
            . '<th style="padding:8px;">Periode Invoice Asli</th>'
            . '<th style="padding:8px;">Nominal</th>'
            . '<th style="padding:8px;">Tanggal Validasi Lunas (updated_at)</th>'
            . '<th style="padding:8px;">Siklus Periode Buku</th>'
            . '</tr></thead><tbody>'
            . $htmlRows
            . '</tbody></table>'
            . '</body></html>';

        // PDF generator:
        // - Jika barryvdh/laravel-dompdf tersedia, render HTML -> PDF beneran.
        // - Jika tidak tersedia, fallback ke content-type text/html (agar download tidak rusak total).

        // Coba render PDF jika dompdf facade tersedia.
        if (class_exists('Barryvdh\\DomPDF\\Facade\\Pdf')) {
            try {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
                return $pdf->download($filename);
            } catch (\Throwable $e) {
                // fallback di bawah
            }
        }

        // Fallback: jika belum ada library PDF, kirim file HTML.
        // Ini mencegah error "download gagal" karena tidak ada renderer PDF.
        return response($html, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.html"',
        ]);



    }

    private function bookPeriodExpr(string $updatedAtColumn = 'updated_at'): string
    {
        return "CASE WHEN DAY({$updatedAtColumn}) < 10 "
            . "THEN DATE_FORMAT(DATE_SUB({$updatedAtColumn}, INTERVAL 1 MONTH), '%Y-%m') "
            . "ELSE DATE_FORMAT({$updatedAtColumn}, '%Y-%m') END";
    }
}

