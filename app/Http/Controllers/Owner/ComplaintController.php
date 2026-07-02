<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;


class ComplaintController extends Controller
{
    public function index(): View
    {
        $complaints = Complaint::query()
            ->whereIn('status', ['pending', 'diproses'])
            ->latest()
            ->with(['tenant.user', 'tenant.room'])
            ->get();

        return view('owner.complaints.index', [
            'complaints' => $complaints,
        ]);
    }


    public function update(Request $request, Complaint $complaint): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,diproses,selesai'],
            'owner_response' => ['nullable', 'string'],
        ]);

        $complaint->update($validated);

        return back()->with('status', 'Keluhan berhasil diperbarui.');
    }
}

