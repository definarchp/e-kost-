<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Invoice {{ $bill->va_number }}</title>
    <style>
        body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 0; padding: 0; color: #0f172a; }
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        .page { width: 210mm; min-height: 297mm; padding: 24mm 12mm; box-sizing: border-box; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #cbd5e1; padding-bottom: 12mm; }
        .title { font-size: 28px; font-weight: 800; margin: 0; }
        .subtitle { font-size: 12px; color: #64748b; margin: 6px 0 0; }
        .right { text-align: right; }
        .invoice { font-size: 18px; font-weight: 800; margin: 0; }
        .meta { margin-top: 8mm; display: grid; grid-template-columns: 1fr 1fr; gap: 10mm; }
        .label { font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; }
        .value { font-size: 13px; font-weight: 700; margin-top: 2mm; }
        .small { font-size: 12px; color: #475569; margin-top: 1.5mm; }
        table { width: 100%; border-collapse: collapse; margin-top: 10mm; }
        th, td { padding: 3mm 2mm; font-size: 12px; }
        thead th { border-top: 2px solid #cbd5e1; border-bottom: 2px solid #cbd5e1; text-align: left; }
        th:last-child, td:last-child { text-align: right; }
        tfoot td { border-top: 2px solid #cbd5e1; }
        .total { font-size: 16px; font-weight: 900; }
        .box { margin-top: 10mm; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 6mm 4mm; }
        .row { display: flex; justify-content: space-between; margin: 2mm 0; font-size: 12px; }
        .row span:first-child { color: #475569; }
        .footer { margin-top: 14mm; border-top: 2px solid #cbd5e1; padding-top: 10mm; text-align: center; font-size: 10px; color: #64748b; }
    </style>
</head>
<body>
    <div class="page">
        <div class="no-print" style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; position: fixed; top: 0; left:0; right:0; background:#fff; z-index:9999;">
            <button onclick="window.print()" style="cursor:pointer; padding:8px 12px; border-radius:10px; background:#10b981; color:#fff; border:0; font-weight:700;">Cetak / Simpan PDF</button>
        </div>

        <div class="header" style="margin-top: 20mm;">
            <div>
                <p class="title">D'BRISSEL</p>
                <p class="subtitle">Sistem Manajemen Kost Digital</p>
            </div>
            <div class="right">
                <p class="invoice">INVOICE</p>
                <p class="small" style="margin:6px 0 0;">#{{ $bill->va_number }}</p>
            </div>
        </div>

        <div class="meta">
            <div>
                <div class="label">Tenant</div>
                <div class="value">{{ $tenant->user->name }}</div>
                <div class="small">{{ 'Kamar ' . ($tenant->room?->room_number ?? '-') }}</div>
                <div class="small">{{ $tenant->phone_number }}</div>
            </div>
            <div class="right">
                <div class="label">Invoice</div>
                <div class="value">{{ now()->translatedFormat('d F Y') }}</div>
                <div class="small">{{ $bill->bill_month }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th>Nominal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Tagihan Sewa Kamar Bulanan</td>
                    <td>{{ 'Rp ' . number_format((int)$bill->amount, 0, ',', '.') }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td class="total">TOTAL</td>
                    <td class="total">{{ 'Rp ' . number_format((int)$bill->amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="box">
            <div class="label" style="margin-bottom: 6mm;">Informasi Pembayaran</div>
            <div class="row"><span>Virtual Account:</span><span style="font-weight:900;">{{ $bill->va_number }}</span></div>
            <div class="row"><span>Jatuh Tempo:</span><span style="font-weight:900;">{{ $tenant->due_date->format('d M Y') }}</span></div>
                <div class="row"><span>Status:</span><span style="font-weight:900;">{{ $bill->status === 'lunas' ? 'Lunas' : ($bill->status === 'menunggu_verifikasi' ? 'Menunggu verifikasi' : 'Belum dibayar') }}</span></div>
        </div>

        <div class="footer">
            <p>Terima kasih telah membayar tepat waktu.</p>
            <p style="margin-top:4mm;">Sistem E-Kost D'Brissel - {{ now()->year }}</p>
        </div>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>

