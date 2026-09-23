<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 h-screen flex items-center justify-center">
    <div class="text-center p-8 max-w-md">
        <div class="w-20 h-20 rounded-full bg-rose-100 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-800">Akses Ditolak</h1>
        <p class="text-slate-500 mt-2">Anda tidak memiliki hak akses untuk halaman ini.</p>
        <p class="text-slate-400 text-sm mt-1">Halaman ini hanya untuk <strong class="text-indigo-600">Owner</strong>.</p>
        <a href="{{ route('dashboard') }}" class="inline-block mt-6 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all">
            Kembali ke Dashboard
        </a>
    </div>
</body>
</html>