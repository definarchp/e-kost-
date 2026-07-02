<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Complaint;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class OwnerDashboardController extends Controller
{
    public function index(): View
    {
        $currentMonth = Carbon::now()->format('Y-m');

        return view('owner.dashboard', [
            'totalRooms' => Room::count(),
            'occupiedRooms' => Room::where('status', 'terisi')->count(),
            'availableRooms' => Room::where('status', 'tersedia')->count(),
            'totalTenants' => Tenant::count(),
            'monthlyRevenue' => Bill::where('status', 'lunas')
                ->where('bill_month', $currentMonth)
                ->sum('amount'),
            'paidBillsThisMonth' => Bill::where('status', 'lunas')
                ->where('bill_month', $currentMonth)
                ->count(),
            'pendingComplaints' => Complaint::where('status', 'pending')->count(),
            'recentBills' => Bill::with(['tenant.user', 'tenant.room'])
                ->latest()
                ->limit(5)
                ->get(),
            'recentComplaints' => Complaint::with(['tenant.user', 'tenant.room'])
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
