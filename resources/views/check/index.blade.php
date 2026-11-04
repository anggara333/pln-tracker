<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLN Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-[#0a0e1a] text-white min-h-screen flex flex-col">

    <div class="p-6 flex justify-between items-center">
        <div class="flex items-center gap-3"><div class="w-10 h-10 bg-yellow-400 rounded-xl flex items-center justify-center"><i class="fas fa-bolt text-black"></i></div><span class="font-bold">PLN Tracker</span></div>
        <button onclick="openModal()" class="bg-yellow-400 text-black font-bold px-4 py-2 rounded-xl text-sm"><i class="fas fa-plus mr-1"></i> Input</button>
    </div>

    <div class="flex-1 px-6 pb-10">
        @if(session('success'))<div class="bg-green-500/20 border border-green-500/30 text-green-400 p-3 rounded-xl text-sm mb-4">{{ session('success') }}</div>@endif
        <div class="grid grid-cols-3 gap-2 mb-6">
            <div class="bg-white/5 border border-white/10 rounded-2xl p-3"><p class="text-[10px] text-slate-400">TOTAL KWH</p><p class="text-xl font-bold text-yellow-400">{{ $totalKwh }}</p></div>
            <div class="bg-white/5 border border-white/10 rounded-2xl p-3"><p class="text-[10px] text-slate-400">BAYAR</p><p class="text-sm font-bold">Rp {{ number_format($totalBayar,0,',','.') }}</p></div>
            <div class="bg-white/5 border border-white/10 rounded-2xl p-3"><p class="text-[10px] text-slate-400">RATA-RATA</p><p class="text-xl font-bold">{{ number_format($rataKwh,0) }}</p></div>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
            @forelse($checks as $c)
            <div class="p-4 flex justify-between items-center border-b border-white/5 last:border-0">
                <div><p class="font-bold text-sm">{{ $c->tanggal->format('d M Y') }} • {{ $c->selisih }} kWh</p><p class="text-xs text-slate-400">{{ $c->awal }} → {{ $c->akhir }}</p></div>
                <div class="flex items-center gap-3">
                    <p class="font-bold text-sm text-yellow-400">Rp {{ number_format($c->pakai,0,',','.') }}</p>
                    <button onclick="openDeleteModal('{{ $c->id }}','{{ $c->tanggal->format('d M Y') }}')" class="w-8 h-8 bg-red-500/20 border border-red-500/30 text-red-400 rounded-full flex items-center justify-center"><i class="fas fa-trash text-[11px]"></i></button>
                </div>
            </div>
            @empty<div class="p-6 text-center text-slate-400 text-sm">Belum ada data - Input 4271 dulu</div>@endforelse
        </div>
    </div>

    <!-- MODAL INPUT -->
    <div id="modal" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
    <div class="bg-[#151a2c] border border-white/10 rounded-[24px] w-full max-w-sm p-6">
        <div class="flex justify-between items-center mb-4"><h2 class="font-bold">Input Manual</h2><button onclick="closeModal()" class="w-8 h-8 bg-white/10 rounded-full"><i class="fas fa-times text-xs"></i></button></div>
        <form action="{{ route('check.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[10px] text-slate-400">TANGGAL</label>
                <input type="date" name="tanggal" value="{{ date('d M Y') }}" class="w-full bg-white/5 border border-white/10 rounded-xl p-3 mt-1 text-sm" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-[10px] text-slate-400">METER AWAL</label>
                    <input id="awalInput" type="number" step="0.01" name="awal" value="{{ $checks->first()->akhir ?? '' }}" class="w-full bg-white/5 border border-white/10 rounded-xl p-3 mt-1 text-sm" required>
                </div>
                <div>
                    <label class="text-[10px] text-yellow-400">METER AKHIR</label>
                    <input id="akhirInput" type="number" step="0.01" name="akhir" class="w-full bg-white/10 border border-yellow-400/50 rounded-xl p-3 mt-1 text-sm font-bold text-yellow-400" required autofocus>
                </div>
            </div>
            <div>
                <label class="text-[10px] text-slate-400">HARGA /KWH</label>
                <div class="flex gap-2 mt-1"><input id="hargaInput" type="number" step="0.01" name="harga" value="1487.20" class="flex-1 bg-white/5 border border-white/10 rounded-xl p-3 text-sm"><div id="liveHitung" class="flex-1 bg-yellow-400/10 border border-yellow-400/20 rounded-xl p-3 text-xs text-yellow-400 font-bold text-right">0 kWh = Rp 0</div></div>
            </div>
            <button type="submit" class="w-full bg-yellow-400 text-black font-bold py-4 rounded-xl">Simpan</button>
        </form>
    </div>
    </div>

    <!-- MODAL DELETE -->
    <div id="deleteModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4 z-[60]">
        <div class="bg-[#151a2c] border border-white/10 rounded-[24px] w-full max-w-sm p-6 text-center">
            <div class="w-14 h-14 bg-red-500/20 border border-red-500/30 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-trash text-red-400 text-xl"></i></div>
            <h3 class="font-bold text-lg">Peringatan..!</h3><p id="deleteText" class="text-sm text-slate-400 mt-2"></p>
            <div class="grid grid-cols-2 gap-3 mt-6">
                <button onclick="closeDeleteModal()" class="bg-white/10 border border-white/10 py-3 rounded-xl font-bold text-sm">Batal</button>
                <form id="deleteForm" method="POST" class="contents">@csrf @method('DELETE')<button type="submit" class="bg-red-500 text-white py-3 rounded-xl font-bold text-sm">Ya, Hapus</button></form>
            </div>
        </div>
    </div>

<script>
function openModal(){ document.getElementById('modal').classList.remove('hidden'); document.getElementById('modal').classList.add('flex'); }
function closeModal(){ const m=document.getElementById('modal'); m.classList.add('hidden'); m.classList.remove('flex'); }
function openDeleteModal(id,tgl){ document.getElementById('deleteText').innerText='Yakin hapus data '+tgl+'?'; document.getElementById('deleteForm').action='/check/'+id; const dm=document.getElementById('deleteModal'); dm.classList.remove('hidden'); dm.classList.add('flex'); }
function closeDeleteModal(){ const dm=document.getElementById('deleteModal'); dm.classList.add('hidden'); dm.classList.remove('flex'); }
function hitungLive(){ const awal=parseFloat(document.getElementById('awalInput').value)||0, akhir=parseFloat(document.getElementById('akhirInput').value)||0, harga=parseFloat(document.getElementById('hargaInput').value)||1487.2; const el=document.getElementById('liveHitung'); if(akhir>awal){ el.innerText=(akhir-awal)+' kWh = Rp '+Math.round((akhir-awal)*harga).toLocaleString('id-ID'); } else { el.innerText='0 kWh = Rp 0'; } }
document.getElementById('akhirInput').addEventListener('input', hitungLive);
document.getElementById('awalInput').addEventListener('input', hitungLive);
document.getElementById('hargaInput').addEventListener('input', hitungLive);
</script>
</body>
</html>

