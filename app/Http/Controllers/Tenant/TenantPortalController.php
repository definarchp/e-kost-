<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class TenantPortalController extends Controller

{
    public function downloadInvoice(Bill $bill): View
    {
        $bill->load(['tenant.user', 'tenant.room']);

        $tenant = $bill->tenant;

        abort_unless($bill->tenant && $bill->tenant->user_id === request()->user()->id, 403);

        return view('tenant.invoice', [
            'bill' => $bill,
            'tenant' => $tenant,
        ]);
    }

    public function uploadPaymentProof(Request $request, Bill $bill)
    {
        $tenant = $request->user()->tenant;
        abort_unless($tenant && $bill->tenant_id === $tenant->id, 403);

        $validated = $request->validate([
            'bukti_pembayaran' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'bukti_pembayaran.max' => 'Ukuran maksimal 5MB.',
        ]);

        // delete old proof if exists
        if ($bill->bukti_pembayaran_path && \Storage::disk('public')->exists($bill->bukti_pembayaran_path)) {
            \Storage::disk('public')->delete($bill->bukti_pembayaran_path);
        }

        $file = $request->file('bukti_pembayaran');
        $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
        $path = $file->storeAs('bills/bukti_pembayaran', $filename, 'public');

        $bill->update([
            // pastikan path sesuai disk 'public'
            'bukti_pembayaran_path' => $path,
        ]);

        return back()->with('status', 'Bukti pembayaran berhasil diunggah. Mohon tunggu konfirmasi pemilik.');
    }

    public function index(Request $request): View


    {
        $tenant = $request->user()
            ->tenant()
            ->with([
                'room',
                'bills' => fn ($query) => $query->latest(),
                'complaints' => fn ($query) => $query->latest(),
            ])
            ->first();

        $currentMonth = Carbon::now()->format('Y-m');
        $currentBill = $tenant?->bills
            ->firstWhere('bill_month', $currentMonth);

        return view('tenant.dashboard', [
            'tenant' => $tenant,
            'room' => $tenant?->room,
            'currentBill' => $currentBill,
            'bills' => $tenant?->bills ?? collect(),
            'complaints' => $tenant?->complaints ?? collect(),
        ]);
    }

    public function dashboard(Request $request): View
    {
        return $this->index($request);
    }

    public function bills(Request $request): View
    {
        $tenant = $request->user()->tenant;

        return view('tenant.bills', [
            'bills' => $tenant?->bills()->latest()->get() ?? collect(),
        ]);
    }

    public function activeBillStatus(Request $request): \Illuminate\Http\JsonResponse
    {
        $tenant = $request->user()->tenant()->first();

        if (! $tenant) {
            return response()->json([
                'has_active_bill' => false,
                'status' => null,
                'bill_id' => null,
            ]);
        }

        $activeBill = $tenant->bills()
            ->whereIn('status', ['belum_dibayar', 'menunggu_verifikasi'])
            ->orderByDesc('created_at')
            ->first();

        if (! $activeBill) {
            return response()->json([
                'has_active_bill' => false,
                'status' => null,
                'bill_id' => null,
            ]);
        }

        return response()->json([
            'has_active_bill' => $activeBill->status === 'belum_dibayar',
            'status' => $activeBill->status,
            'bill_id' => $activeBill->id,
        ]);
    }
}

