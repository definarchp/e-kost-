<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse; // Kita gunakan import yang benar

class AssetProofController extends Controller
{
    /**
     * Stream bukti pembayaran ke browser secara aman.
     */
    public function proof(Bill $bill): StreamedResponse
    {
        $path = $bill->bukti_pembayaran_path;

        if (! $path) {
            abort(404, 'Path tidak ditemukan di database.');
        }

        // Ambil nama file murni (berjaga-jaga jika format path di DB berbeda)
        $fileName = basename($path);
        
        // Sesuaikan dengan folder fisik Anda di Laragon
        $absolutePath = storage_path('app/public/bills/bukti_pembayaran/' . $fileName);

        if (! is_file($absolutePath)) {
            abort(404, 'File gambar tidak ditemukan secara fisik di folder storage.');
        }

        // Return StreamedResponse yang sah dan dikenali oleh Laravel & Symfony
        return response()->stream(function () use ($absolutePath) {
            readfile($absolutePath);
        }, 200, [
            'Content-Type' => mime_content_type($absolutePath) ?: 'application/octet-stream',
        ]);
    }
}