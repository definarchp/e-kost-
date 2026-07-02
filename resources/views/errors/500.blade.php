<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Kesalahan Server</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-lg w-full text-center py-12">
            <!-- Animated 500 -->
            <div class="mb-8">
                <h1 class="text-9xl font-bold bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent mb-4">
                    500
                </h1>
            </div>

            <!-- Error Details -->
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Kesalahan Server Internal</h2>
                <p class="text-gray-600 text-lg mb-2">
                    Terjadi kesalahan pada server kami saat memproses permintaan Anda.
                </p>
                <p class="text-gray-500 text-sm">
                    Tim teknis telah diberitahu dan sedang menangani masalah ini. Silakan coba lagi dalam beberapa saat.
                </p>
            </div>

            <!-- Illustration -->
            <div class="mb-12">
                <svg class="mx-auto w-64 h-64 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <!-- Info Box -->
            <div class="mb-12 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-blue-800 text-sm">
                    <strong>Tip:</strong> Cobalah refresh halaman atau kembali nanti. Jika masalah berlanjut, hubungi tim support.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="javascript:location.reload()" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl">
                    🔄 Refresh Halaman
                </a>
                <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                    ← Kembali ke Dashboard
                </a>
            </div>

            <!-- Footer Help -->
            <div class="mt-12 pt-8 border-t border-gray-200">
                <p class="text-gray-500 text-sm mb-4">
                    Masalah tidak teratasi?
                </p>
                <a href="mailto:support@ekost.com?subject=Error%20500%20-%20Server%20Error" class="text-blue-600 hover:text-blue-700 font-medium">
                    Laporkan ke Tim Support
                </a>
            </div>
        </div>
    </div>
</body>
</html>
