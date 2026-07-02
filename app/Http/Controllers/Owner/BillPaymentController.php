<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BillPaymentController extends Controller
{
    public function confirm(Request $request, Bill $bill): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:lunas,belum_dibayar,menunggu_verifikasi'],
        ]);

        $bill->update([
            'status' => $validated['status'],
        ]);

        return back()->with('status', 'Status pembayaran berhasil diperbarui.');
    }
}



