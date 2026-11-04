<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLN Tracker - Server HP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-[#0a0e1a] text-white min-h-screen flex flex-col">
    <!-- Header -->
    <div class="p-6">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-400 rounded-xl flex items-center justify-center">
                    <i class="fas fa-bolt text-black text-lg"></i>
                </div>
                <span class="font-bold text-lg">PLN Tracker</span>
            </div>
            <div class="text-xs bg-green-500/20 text-green-400 px-3 py-1 rounded-full border border-green-500/30">
                <span class="w-2 h-2 bg-green-400 rounded-full inline-block animate-pulse mr-1"></span>
                Server: {{ $_SERVER['SERVER_ADDR'] ?? '0.0.0.0' }}:8000
            </div>
        </div>
    </div>

    <!-- Main -->
    <div class="flex-1 flex flex-col justify-center px-6 pb-10">
        <div class="mb-8">
            <h1 class="text-4xl font-black leading-tight mb-3">
                Monitoring<br>
                <span class="text-yellow-400">Lapangan PLN</span><br>
                Jadi Mudah
            </h1>
            <p class="text-slate-400 text-sm">Server berjalan di HP-A via Bluetooth Tethering<br>IP: 10.65.246.71 | Akses dari HP-B bisa</p>
        </div>

        <div class="grid grid-cols-2 gap-3 mb-8">
            <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                <i class="fas fa-mobile-alt text-yellow-400 mb-2"></i>
                <p class="text-2xl font-bold">2 HP</p>
                <p class="text-xs text-slate-400">Terhubung</p>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                <i class="fas fa-wifi text-blue-400 mb-2"></i>
                <p class="text-2xl font-bold">BT-PAN</p>
                <p class="text-xs text-slate-400">Mode Tethering</p>
            </div>
        </div>

        <div class="space-y-3">
            <a href="{{ route(check.index) }}" class="w-full bg-yellow-400 text-black font-bold py-4 rounded-2xl flex items-center justify-center gap-2">
                <i class="fas fa-rocket"></i> Buka Dashboard
            </a>
            <a href="http://10.65.246.71:8000/login" class="w-full bg-white/10 border border-white/10 font-semibold py-4 rounded-2xl flex items-center justify-center gap-2">
                <i class="fas fa-link"></i> Buka dari HP-B
            </a>
            <div class="flex gap-3">
                <a href="/ip.php" class="flex-1 bg-white/5 text-center py-3 rounded-xl text-sm">Cek IP</a>
                <a href="/register" class="flex-1 bg-white/5 text-center py-3 rounded-xl text-sm">Daftar</a>
            </div>
        </div>

        <div class="mt-8 bg-blue-500/10 border border-blue-500/20 rounded-xl p-3 text-xs text-blue-300">
            <i class="fas fa-info-circle mr-1"></i> Jika di HP-B tidak bisa dibuka, ganti ke WiFi Hotspot, jangan Bluetooth. Lalu akses IP baru yang muncul.
        </div>
    </div>
</body>
</html>
