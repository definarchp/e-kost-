<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function create(): View
    {
        return view('tenant.complaints.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'description' => ['required', 'string', 'min:10'],
        ]);

        $tenant = $request->user()->tenant;

        abort_if(! $tenant, 403, 'Profil penyewa belum tersedia.');

        $tenant->complaints()->create([
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        return redirect()
            ->route('tenant.dashboard')
            ->with('status', 'Keluhan berhasil dikirim.');
    }
}
