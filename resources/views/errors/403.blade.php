<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-lg w-full text-center py-12">
            <!-- Animated 403 -->
            <div class="mb-8">
                <h1 class="text-9xl font-bold bg-gradient-to-r from-yellow-600 to-orange-600 bg-clip-text text-transparent mb-4">
                    403
                </h1>
            </div>

            <!-- Error Details -->
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Akses Ditolak</h2>
                <p class="text-gray-600 text-lg mb-2">
                    Anda tidak memiliki izin untuk mengakses halaman ini.
                </p>
                <p class="text-gray-500 text-sm">
                    Kode Error: 403 Forbidden - Hubungi administrator jika Anda merasa ini adalah kesalahan.
                </p>
            </div>

            <!-- Illustration -->
            <div class="mb-12">
                <svg class="mx-auto w-64 h-64 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>

            <!-- Warning Box -->
            <div class="mb-12 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-yellow-800 text-sm">
                    <strong>⚠️ Peringatan:</strong> Akses ke halaman ini telah dicatat dalam sistem keamanan.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl">
                    ← Kembali ke Dashboard
                </a>
                <a href="{{ url('/') }}" class="px-6 py-3 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                    Kembali ke Beranda
                </a>
            </div>

            <!-- Footer Help -->
            <div class="mt-12 pt-8 border-t border-gray-200">
                <p class="text-gray-500 text-sm mb-4">
                    Rasa ini adalah kesalahan?
                </p>
                <a href="mailto:support@ekost.com?subject=Akses%20Ditolak%20-%20403" class="text-blue-600 hover:text-blue-700 font-medium">
                    Hubungi Administrator
                </a>
            </div>
        </div>
    </div>
</body>
</html>
