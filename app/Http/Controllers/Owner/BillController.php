<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BillController extends Controller
{
    public function index(): View
    {
        return view('owner.bills.index', [
            'bills' => Bill::with('tenant.user', 'tenant.room')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('owner.bills.create', [
            'tenants' => Tenant::with('user', 'room')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['single', 'bulk'])],
            'tenant_id' => ['required_if:type,single', 'nullable', 'exists:tenants,id'],
            'bill_month' => ['required', 'date_format:Y-m'],
            'amount' => ['required_if:type,single', 'nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['lunas', 'belum_dibayar', 'menunggu_verifikasi'])],
        ]);


        if ($validated['type'] === 'single') {
            $this->createSingleBill($validated);
            $message = 'Tagihan berhasil dibuat.';
        } else {
            $this->createBulkBills($validated);
            $message = 'Tagihan massal berhasil dibuat.';
        }

        return redirect()->route('owner.bills.index')->with('status', $message);
    }

    private function createSingleBill(array $data): void
    {
        $tenant = Tenant::findOrFail($data['tenant_id']);

        $vaNumber = $this->generateVANumber($tenant->id, $data['bill_month']);

        Bill::create([
            'tenant_id' => $data['tenant_id'],
            'bill_month' => $data['bill_month'],
            'amount' => $data['amount'],
            'status' => $data['status'],
            'va_number' => $vaNumber,
        ]);
    }

    private function createBulkBills(array $data): void
    {
        $tenants = Tenant::with('room')->get();

        foreach ($tenants as $tenant) {
            // Skip if bill already exists for this month
            $existingBill = Bill::where('tenant_id', $tenant->id)
                ->where('bill_month', $data['bill_month'])
                ->exists();

            if ($existingBill) {
                continue;
            }

            $vaNumber = $this->generateVANumber($tenant->id, $data['bill_month']);

            Bill::create([
                'tenant_id' => $tenant->id,
                'bill_month' => $data['bill_month'],
                'amount' => $tenant->room?->price ?? 0,
                'status' => $data['status'],
                'va_number' => $vaNumber,
            ]);
        }
    }

    private function generateVANumber(int $tenantId, string $billMonth): string
    {
        $tenantCode = str_pad($tenantId, 5, '0', STR_PAD_LEFT);
        $monthCode = str_replace('-', '', $billMonth);
        $randomCode = str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT);

        return "{$tenantCode}{$monthCode}{$randomCode}";
    }

    public function show(Bill $bill): View
    {
        return view('owner.bills.show', [
            'bill' => $bill->load('tenant.user', 'tenant.room'),
        ]);
    }

    public function edit(Bill $bill): View
    {
        return view('owner.bills.edit', [
            'bill' => $bill->load('tenant.user', 'tenant.room'),
            'tenants' => Tenant::all(),
        ]);
    }

    public function update(Request $request, Bill $bill): RedirectResponse
    {
        $validated = $request->validate([
            'tenant_id' => ['required', 'exists:tenants,id'],
            'bill_month' => ['required', 'date_format:Y-m'],
            'amount' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['lunas', 'belum_dibayar', 'menunggu_verifikasi'])],
            'va_number' => ['required', 'string', 'max:255', 'unique:bills,va_number,'.$bill->id],
        ]);

        $bill->update($validated);

        return redirect()->route('owner.bills.index')->with('status', 'Tagihan berhasil diperbarui.');
    }

    public function destroy(Bill $bill): RedirectResponse
    {
        $bill->delete();

        return redirect()->route('owner.bills.index')->with('status', 'Tagihan berhasil dihapus.');
    }
}
