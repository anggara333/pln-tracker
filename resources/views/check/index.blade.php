<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Super Apps</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#0a0e1a;color:#fff;font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,"Liberation Mono",monospace;min-height:100vh;display:flex;flex-direction:column}
button,input,select,textarea{font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,"Liberation Mono",monospace}
.header{padding:24px 24px 0;display:flex;justify-content:space-between;align-items:center}
.logo{display:flex;align-items:center;gap:12px}
.logo-icon{width:40px;height:40px;background:#facc15;border-radius:12px;display:flex;align-items:center;justify-content:center}
.logo-icon svg{width:22px;height:22px;fill:#000}
.bold{font-weight:700;letter-spacing:-.3px}
.btn-input{background:#facc15;color:#000;font-weight:700;padding:8px 16px;border-radius:12px;font-size:13px;border:none;cursor:pointer;display:flex;align-items:center;gap:6px}
.btn-input svg{width:13px;height:13px;fill:#000}
.btn-input.green{background:#22c55e;color:#fff}
.btn-input.green svg{fill:#fff}
.main{flex:1;padding:0 24px 40px}
.tabs{display:flex;gap:8px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);padding:6px;border-radius:16px;margin:20px 0}
.tab{flex:1;padding:12px;border-radius:12px;border:none;background:transparent;color:#94a3b8;font-weight:700;font-size:13px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px}
.tab svg{width:16px;height:16px;fill:currentColor}
.tab.active{background:#facc15;color:#000}
.tab.active-green{background:#22c55e;color:#fff}
.tab-content{display:none}
.tab-content.active{display:block}
.alert{background:rgba(34,197,94,.2);border:1px solid rgba(34,197,94,.3);color:#4ade80;padding:12px;border-radius:12px;font-size:13px;margin-bottom:16px}
.grid3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-bottom:16px}
.card{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:8px 10px}
.card.green{border-color:rgba(34,197,94,.3)}
.label{font-size:9px;color:#94a3b8;letter-spacing:.8px}
.big{font-size:15px;font-weight:700;color:#facc15;margin-top:2px;letter-spacing:-.3px}
.big-green{font-size:15px;font-weight:700;color:#4ade80;margin-top:2px;letter-spacing:-.3px}
.big-red{font-size:15px;font-weight:700;color:#f87171;margin-top:2px;letter-spacing:-.3px}
.big2{font-size:12px;font-weight:600;margin-top:2px;word-break:break-all;letter-spacing:-.3px}
.list{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:16px;overflow:hidden}
.row{padding:14px 16px;display:flex;justify-content:space-between;align-items:center;gap:10px;border-bottom:1px solid rgba(255,255,255,.05)}
.row:last-child{border-bottom:none}
.row-title{font-weight:700;font-size:13px;letter-spacing:-.2px}
.row-sub{font-size:11px;color:#94a3b8;margin-top:2px}
.row-right{display:flex;align-items:center;gap:12px;flex-shrink:0}
.row-check{display:flex;align-items:center;gap:12px;flex:1;min-width:0}
.price{font-weight:700;font-size:13px;color:#facc15;white-space:nowrap}
.price-green{font-weight:700;font-size:13px;color:#4ade80;white-space:nowrap}
.price-red{font-weight:700;font-size:13px;color:#f87171;white-space:nowrap}
.badge{font-size:10px;padding:3px 8px;border-radius:999px;font-weight:700}
.badge-in{background:rgba(34,197,94,.2);color:#4ade80}
.badge-out{background:rgba(239,68,68,.2);color:#f87171}
.btn-del{width:30px;height:30px;background:rgba(239,68,68,.2);border:1px solid rgba(239,68,68,.3);border-radius:999px;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0}
.btn-del svg{width:14px;height:14px;fill:#f87171}
.empty{padding:24px;text-align:center;color:#94a3b8;font-size:13px}
.toolbar{display:flex;gap:8px;margin-bottom:10px;align-items:center}
.btn-tool{flex:1;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);color:#fff;padding:10px;border-radius:12px;font-weight:700;font-size:12px;cursor:pointer;text-decoration:none;text-align:center;display:block}
.btn-tool.red{background:rgba(239,68,68,.2);border-color:rgba(239,68,68,.4);color:#f87171}
.btn-tool:disabled{opacity:.4;cursor:not-allowed}
.chk{width:18px;height:18px;accent-color:#facc15;cursor:pointer;flex-shrink:0}
.chk-green{accent-color:#22c55e}
.modal{position:fixed;inset:0;background:rgba(0,0,0,.8);display:none;align-items:center;justify-content:center;padding:16px;z-index:50}
.modal.open{display:flex}
.modal-box{background:#151a2c;border:1px solid rgba(255,255,255,.1);border-radius:24px;width:100%;max-width:384px;padding:24px;max-height:90vh;overflow-y:auto}
.modal-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.btn-x{width:32px;height:32px;background:rgba(255,255,255,.1);border:none;border-radius:999px;cursor:pointer;display:flex;align-items:center;justify-content:center}
.btn-x svg{width:12px;height:12px;fill:#fff}
.form{display:flex;flex-direction:column;gap:16px}
.input,.select,.textarea{width:100%;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:12px;margin-top:4px;color:#fff;font-size:13px;outline:none}
.select option{color:#000}
.input-yellow{background:rgba(255,255,255,.1);border-color:rgba(250,204,21,.5);color:#facc15;font-weight:700}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.label-y{font-size:10px;color:#facc15}
.label-g{font-size:10px;color:#4ade80}
.flex-gap{display:flex;gap:8px;margin-top:4px}
.live{flex:1;background:rgba(250,204,21,.1);border:1px solid rgba(250,204,21,.2);border-radius:12px;padding:12px;font-size:12px;color:#facc15;font-weight:700;text-align:right;display:flex;align-items:center;justify-content:flex-end}
.btn-save{width:100%;background:#facc15;color:#000;font-weight:700;padding:16px;border-radius:12px;border:none;cursor:pointer;font-size:14px}
.btn-save.green{background:#22c55e;color:#fff}
.del-box{text-align:center}
.del-icon{width:56px;height:56px;background:rgba(239,68,68,.2);border:1px solid rgba(239,68,68,.3);border-radius:999px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px}
.del-icon svg{width:26px;height:26px;fill:#f87171}
.del-text{font-size:13px;color:#94a3b8;margin-top:8px}
.del-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:24px}
.btn-cancel{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.1);padding:12px;border-radius:12px;font-weight:700;font-size:13px;color:#fff;cursor:pointer}
.btn-confirm{background:#ef4444;color:#fff;padding:12px;border-radius:12px;font-weight:700;font-size:13px;border:none;cursor:pointer}
.toggle{display:grid;grid-template-columns:1fr 1fr;gap:8px;background:rgba(255,255,255,.05);padding:6px;border-radius:12px}
.toggle button{padding:10px;border:none;border-radius:9px;font-weight:700;font-size:13px;cursor:pointer;background:transparent;color:#94a3b8}
.toggle button.on-in{background:#22c55e;color:#fff}
.toggle button.on-out{background:#ef4444;color:#fff}
</style>
</head>
<body>

<div class="header">
  <div class="logo"><div class="logo-icon"><svg viewBox="0 0 24 24"><path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"/></svg></div><span class="bold">Super Apps</span></div>
  <button id="topBtn" onclick="openModalByTab()" class="btn-input"><svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Input</button>
</div>

<div class="main">
  @if(session('success'))<div class="alert">{{ session('success') }}</div>@endif

  <div class="tabs">
    <button id="tabBtnPln" onclick="switchTab('pln')" class="tab active"><svg viewBox="0 0 24 24"><path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"/></svg> PLN</button>
    <button id="tabBtnHarian" onclick="switchTab('harian')" class="tab"><svg viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg> Harian</button>
  </div>

  <div id="tabPln" class="tab-content active">
    <div class="grid3">
      <div class="card"><p class="label">TOTAL KWH</p><p class="big">{{ $totalKwh ?? 0 }}</p></div>
      <div class="card"><p class="label">BAYAR</p><p class="big2">Rp {{ number_format($totalBayar ?? 0,0,',','.') }}</p></div>
      <div class="card"><p class="label">RATA-RATA</p><p class="big">{{ number_format($rataKwh ?? 0,0) }}</p></div>
    </div>
    <div class="toolbar">
      <label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer;flex-shrink:0"><input type="checkbox" id="checkAllPln" onclick="toggleAll('pln')" class="chk"> Semua</label>
      <button onclick="bulkDelete('pln')" id="btnBulkPln" class="btn-tool red" disabled>Hapus (<span id="countPln">0</span>)</button>
      <a href="{{ route('check.exportPdf') }}" class="btn-tool">Export PDF</a>
    </div>
    <form id="formBulkPln" action="{{ route('check.bulkDelete') }}" method="POST" style="display:none">@csrf</form>
    <div class="list">
      @forelse($checks as $c)
      <div class="row">
        <input type="checkbox" value="{{ $c->id }}" onchange="updateCount('pln')" class="chk chk-pln">
        <div class="row-check"><div><p class="row-title">{{ $c->tanggal->format('d M Y') }} • {{ $c->selisih }} kWh</p><p class="row-sub">{{ $c->awal }} → {{ $c->akhir }}</p></div></div>
        <div class="row-right"><p class="price">Rp {{ number_format($c->pakai,0,',','.') }}</p>
        <button onclick="openDeleteModal('{{ $c->id }}','{{ $c->tanggal->format('d M Y') }}','pln')" class="btn-del"><svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg></button></div>
      </div>
      @empty<div class="empty">Belum ada data PLN - Input 4271 dulu</div>@endforelse
    </div>
  </div>

  <div id="tabHarian" class="tab-content">
    <div class="grid3">
      <div class="card green"><p class="label">SALDO</p><p class="big2" style="color:#4ade80">Rp {{ number_format($saldoAkhir ?? 0,0,',','.') }}</p></div>
      <div class="card"><p class="label">MASUK</p><p class="big2" style="color:#4ade80">+Rp {{ number_format($totalKredit ?? 0,0,',','.') }}</p></div>
      <div class="card"><p class="label">KELUAR</p><p class="big2" style="color:#f87171">-Rp {{ number_format($totalDebit ?? 0,0,',','.') }}</p></div>
    </div>
    <div class="toolbar">
      <label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer;flex-shrink:0"><input type="checkbox" id="checkAllHarian" onclick="toggleAll('harian')" class="chk chk-green"> Semua</label>
      <button onclick="bulkDelete('harian')" id="btnBulkHarian" class="btn-tool red" disabled>Hapus (<span id="countHarian">0</span>)</button>
      <a href="{{ route('harian.exportPdf') }}" class="btn-tool">Export PDF</a>
    </div>
    <form id="formBulkHarian" action="{{ route('harian.bulkDelete') }}" method="POST" style="display:none">@csrf</form>
    <div class="list">
      @forelse($harians as $h)
      <div class="row">
        <input type="checkbox" value="{{ $h->id }}" onchange="updateCount('harian')" class="chk chk-green chk-harian">
        <div class="row-check"><div>
          <p class="row-title">{{ $h->tanggal->format('d M Y') }} <span class="badge {{ $h->kredit > 0 ? 'badge-in' : 'badge-out' }}">{{ $h->kredit > 0 ? 'MASUK' : 'KELUAR' }}</span></p>
          <p class="row-sub">{{ $h->uraian }} @if($h->catatan) • {{ $h->catatan }} @endif</p>
        </div></div>
        <div class="row-right">
          <p class="{{ $h->kredit > 0 ? 'price-green' : 'price-red' }}">{{ $h->kredit > 0 ? '+' : '-' }} Rp {{ number_format($h->kredit > 0 ? $h->kredit : $h->debit,0,',','.') }}</p>
          <button onclick="openDeleteModal('{{ $h->id }}','{{ $h->uraian }}','harian')" class="btn-del"><svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg></button>
        </div>
      </div>
      @empty<div class="empty">Belum ada transaksi harian</div>@endforelse
    </div>
  </div>
</div>

<div id="modal" class="modal">
  <div class="modal-box">
    <div class="modal-head"><h2 class="bold">Input PLN</h2><button onclick="closeModal()" class="btn-x"><svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button></div>
    <form action="{{ route('check.store') }}" method="POST" class="form">
      @csrf
      <div><label class="label">TANGGAL</label><input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="input" required></div>
      <div class="grid2">
        <div><label class="label">METER AWAL</label><input id="awalInput" type="number" step="0.01" name="awal" value="{{ $checks->first()->akhir ?? '' }}" class="input" required></div>
        <div><label class="label-y">METER AKHIR</label><input id="akhirInput" type="number" step="0.01" name="akhir" class="input input-yellow" required autofocus></div>
      </div>
      <div><label class="label">HARGA /KWH</label><div class="flex-gap"><input id="hargaInput" type="number" step="0.01" name="harga" value="1487.20" class="input" style="margin:0;flex:1"><div id="liveHitung" class="live">0 kWh = Rp 0</div></div></div>
      <button type="submit" class="btn-save">Simpan PLN</button>
    </form>
  </div>
</div>

<div id="modalHarian" class="modal">
  <div class="modal-box">
    <div class="modal-head"><h2 class="bold">Transaksi Harian</h2><button onclick="closeModalHarian()" class="btn-x"><svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button></div>
    <form action="{{ route('harian.store') }}" method="POST" class="form">
      @csrf
      <div><label class="label">TANGGAL</label><input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="input" required></div>
      <div><label class="label">URAIAN</label><input type="text" name="uraian" placeholder="cth: Gaji, Beli pulsa, Jajan" class="input" required></div>
      <div>
        <label class="label">JENIS</label>
        <div class="toggle" style="margin-top:4px">
          <button type="button" id="btnMasuk" onclick="setJenis('masuk')" class="on-in">+ Masuk</button>
          <button type="button" id="btnKeluar" onclick="setJenis('keluar')">- Keluar</button>
        </div>
        <input type="hidden" id="jenisInput" value="masuk">
      </div>
      <div><label class="label">NOMINAL</label><input id="nominalInput" type="number" step="0.01" name="kredit" placeholder="0" class="input" required></div>
      <input type="hidden" id="kreditHidden" name="kredit" value="0">
      <input type="hidden" id="debitHidden" name="debit" value="0">
      <div><label class="label">CATATAN</label><input type="text" name="catatan" placeholder="cth: buat bayar listrik" class="input"></div>
      <div id="previewSaldo" class="live" style="justify-content:space-between">Saldo baru: Rp 0</div>
      <button type="submit" onclick="siapkanNominal()" class="btn-save green">Simpan Transaksi</button>
    </form>
  </div>
</div>

<div id="deleteModal" class="modal" style="z-index:60">
  <div class="modal-box del-box">
    <div class="del-icon"><svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg></div>
    <h3 class="bold" style="font-size:18px">Peringatan..!</h3><p id="deleteText" class="del-text"></p>
    <div class="del-grid">
      <button onclick="closeDeleteModal()" class="btn-cancel">Batal</button>
      <form id="deleteForm" method="POST" style="display:contents">@csrf @method('DELETE')<button type="submit" class="btn-confirm">Ya, Hapus</button></form>
    </div>
  </div>
</div>

<script>
let currentTab = 'pln';
let jenis = 'masuk';
let saldoAwal = {{ $saldoAkhir ?? 0 }};
function switchTab(tab){
  currentTab = tab;
  document.getElementById('tabPln').classList.toggle('active', tab==='pln');
  document.getElementById('tabHarian').classList.toggle('active', tab==='harian');
  document.getElementById('tabBtnPln').classList.toggle('active', tab==='pln');
  document.getElementById('tabBtnHarian').classList.toggle('active', tab==='harian');
  document.getElementById('tabBtnHarian').classList.toggle('active-green', tab==='harian');
  const topBtn = document.getElementById('topBtn');
  if(tab==='harian'){ topBtn.classList.add('green'); topBtn.innerHTML='<svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Transaksi'; }
  else { topBtn.classList.remove('green'); topBtn.innerHTML='<svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Input'; }
  localStorage.setItem('tab', tab);
}
if(localStorage.getItem('tab')==='harian'){ switchTab('harian'); }
function openModalByTab(){ if(currentTab==='harian') openModalHarian(); else openModal(); }
function openModal(){ document.getElementById('modal').classList.add('open'); }
function closeModal(){ document.getElementById('modal').classList.remove('open'); }
function openModalHarian(){ document.getElementById('modalHarian').classList.add('open'); updatePreview(); }
function closeModalHarian(){ document.getElementById('modalHarian').classList.remove('open'); }
function setJenis(j){
  jenis=j;
  document.getElementById('jenisInput').value=j;
  document.getElementById('btnMasuk').className = j==='masuk' ? 'on-in' : '';
  document.getElementById('btnKeluar').className = j==='keluar' ? 'on-out' : '';
  updatePreview();
}
function updatePreview(){
  const nom = parseFloat(document.getElementById('nominalInput').value)||0;
  const baru = jenis==='masuk' ? saldoAwal + nom : saldoAwal - nom;
  document.getElementById('previewSaldo').innerText = 'Saldo baru: Rp ' + baru.toLocaleString('id-ID');
}
function siapkanNominal(){
  const nom = parseFloat(document.getElementById('nominalInput').value)||0;
  document.getElementById('kreditHidden').value = jenis==='masuk' ? nom : 0;
  document.getElementById('debitHidden').value = jenis==='keluar' ? nom : 0;
  document.getElementById('nominalInput').removeAttribute('name');
}
document.getElementById('nominalInput').addEventListener('input', updatePreview);
function openDeleteModal(id,tgl,tipe){
  document.getElementById('deleteText').innerText='Yakin hapus data '+tgl+'?';
  document.getElementById('deleteForm').action = tipe==='harian' ? '/harian/'+id : '/check/'+id;
  document.getElementById('deleteModal').classList.add('open');
}
function closeDeleteModal(){ document.getElementById('deleteModal').classList.remove('open'); }
function hitungLive(){ const awal=parseFloat(document.getElementById('awalInput').value)||0, akhir=parseFloat(document.getElementById('akhirInput').value)||0, harga=parseFloat(document.getElementById('hargaInput').value)||1487.2; const el=document.getElementById('liveHitung'); if(akhir>awal){ el.innerText=(akhir-awal)+' kWh = Rp '+Math.round((akhir-awal)*harga).toLocaleString('id-ID'); } else { el.innerText='0 kWh = Rp 0'; } }
document.getElementById('akhirInput').addEventListener('input', hitungLive);
document.getElementById('awalInput').addEventListener('input', hitungLive);
document.getElementById('hargaInput').addEventListener('input', hitungLive);
function toggleAll(tipe){
  const all = document.getElementById(tipe==='pln' ? 'checkAllPln' : 'checkAllHarian').checked;
  document.querySelectorAll(tipe==='pln' ? '.chk-pln' : '.chk-harian').forEach(c=>c.checked=all);
  updateCount(tipe);
}
function updateCount(tipe){
  const checked = document.querySelectorAll(tipe==='pln' ? '.chk-pln:checked' : '.chk-harian:checked').length;
  document.getElementById(tipe==='pln' ? 'countPln' : 'countHarian').innerText = checked;
  document.getElementById(tipe==='pln' ? 'btnBulkPln' : 'btnBulkHarian').disabled = checked===0;
}
function bulkDelete(tipe){
  const checked = document.querySelectorAll(tipe==='pln' ? '.chk-pln:checked' : '.chk-harian:checked');
  if(checked.length===0) return;
  if(!confirm('Yakin hapus '+checkeid.length+' data terpilih?')) return;
  const form = document.getElementById(tipe==='pln' ? 'formBulkPln' : 'formBulkHarian');
  checked.forEach(c=>{
    const i=document.createElement('input'); i.type='hidden'; i.name='ids[]'; i.value=c.value; form.appendChild(i);
  });
  form.submit();
}
</script>
</body>
</html>

