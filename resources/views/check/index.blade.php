<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Super Apps</title>
<link rel="stylesheet" href="{{ asset('css/super.css') }}?v=8">
</head>
<body data-saldo="{{ $saldoAkhir ?? 0 }}">

<div class="header">
  <div class="logo"><div class="logo-icon"><svg viewBox="0 0 24 24"><path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"/></svg></div><span class="bold">Super Apps</span></div>
  <button type="button" id="topBtn" onclick="openModalByTab()" class="btn-input"><svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Input</button>
</div>

<div class="main">
  @if(session('success'))<div class="alert">{{ session('success') }}</div>@endif

  <div class="tabs">
    <button type="button" id="tabBtnPln" onclick="switchTab('pln')" class="tab"><svg viewBox="0 0 24 24"><path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"/></svg> PLN</button>
    <button type="button" id="tabBtnHarian" onclick="switchTab('harian')" class="tab"><svg viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg> Harian</button>
  </div>

  {{-- ============ TAB PLN ============ --}}
  <div id="tabPln" class="tab-content active">
    <div class="grid3">
      <div class="card"><p class="label">TOTAL KWH</p><p class="big2">{{ $totalKwh ?? 0 }}</p></div>
      <div class="card"><p class="label">BAYAR</p><p class="big2">Rp {{ number_format($totalBayar ?? 0,0,',','.') }}</p></div>
      <div class="card"><p class="label">RATA-RATA</p><p class="big2">{{ number_format($rataKwh ?? 0,0) }}</p></div>
    </div>
    <div class="toolbar">
      <button type="button" onclick="toggleSelectMode('pln')" id="btnSelectPln" class="btn-select">Pilih</button>
      <button type="button" onclick="toggleAll('pln')" id="btnAllPln" class="btn-select">Semua</button>
      <a href="{{ route('check.exportPdf') }}" class="btn-tool">Export PDF</a>
    </div>
    <form id="formBulkPln" action="{{ route('check.bulkDelete') }}" method="POST" style="display:none">@csrf</form>
    <div class="list">
      @forelse($checks as $c)
      <div class="row" id="row-pln-{{ $c->id }}">
        <input type="checkbox" value="{{ $c->id }}" data-label="{{ $c->tanggal->format('d M Y') }} - {{ $c->selisih }} kWh" onchange="updateCount('pln')" class="chk chk-pln" style="display:none">
        <div class="row-check"><div><p class="row-title">{{ $c->tanggal->format('d M Y') }} • {{ $c->selisih }} kWh</p><p class="row-sub">{{ $c->awal }} → {{ $c->akhir }}</p></div></div>
        <div class="row-right"><p class="price">Rp {{ number_format($c->pakai,0,',','.') }}</p>
        </div>
      </div>
      @empty<div class="empty">Belum ada data PLN</div>@endforelse
    </div>
  </div>

  {{-- ============ TAB HARIAN ============ --}}
  <div id="tabHarian" class="tab-content">
    <div class="grid3">
      <div class="card green"><p class="label">SALDO</p><p class="big2" style="color:#4ade80">Rp {{ number_format($saldoAkhir ?? 0,0,',','.') }}</p></div>
      <div class="card"><p class="label">MASUK</p><p class="big2" style="color:#4ade80">Rp {{ number_format($totalKredit ?? 0,0,',','.') }}</p></div>
      <div class="card"><p class="label">KELUAR</p><p class="big2" style="color:#f87171">Rp {{ number_format($totalDebit ?? 0,0,',','.') }}</p></div>
    </div>
    <div class="toolbar">
      <button type="button" onclick="toggleSelectMode('harian')" id="btnSelectHarian" class="btn-select">Pilih</button>
      <button type="button" onclick="toggleAll('harian')" id="btnAllHarian" class="btn-select">Semua</button>
      <a href="{{ route('harian.exportPdf') }}" class="btn-tool">Export PDF</a>
    </div>
    <form id="formBulkHarian" action="{{ route('harian.bulkDelete') }}" method="POST" style="display:none">@csrf</form>
    <div class="list">
      @forelse($harians as $h)
      <div class="row" id="row-harian-{{ $h->id }}">
        <input type="checkbox" value="{{ $h->id }}" data-label="{{ $h->tanggal->format('d M Y') }} - {{ $h->uraian }}" onchange="updateCount('harian')" class="chk chk-green chk-harian" style="display:none">
        <div class="row-check"><div>
          <p class="row-title">{{ $h->tanggal->format('d M Y') }}</p>
          <p class="row-sub">{{ $h->uraian }} @if($h->catatan) • {{ $h->catatan }} @endif</p>
        </div></div>
        <div class="row-right">
          <p class="{{ $h->kredit > 0 ? 'price-green' : 'price-red' }}">Rp {{ number_format($h->kredit > 0 ? $h->kredit : $h->debit,0,',','.') }}</p>
        </div>
      </div>
      @empty<div class="empty">Belum ada transaksi harian</div>@endforelse
    </div>
  </div>
</div>

{{-- ============ MODAL PLN ============ --}}
<div id="modal" class="modal" onclick="if(event.target===this) closeModal()"><div class="modal-box">
  <div class="grabber"></div>
  <div class="modal-head"><h2 class="bold">Input PLN</h2><button type="button" onclick="closeModal()" class="btn-x"><svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button></div>
  <p class="modal-sub">Catat meter listrik hari ini</p>
  <form action="{{ route('check.store') }}" method="POST" class="form">@csrf
    <div><label class="label">TANGGAL</label><input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="input" required></div>
    <div class="grid2">
      <div><label class="label">METER AWAL</label><input id="awalInput" type="number" step="0.01" name="awal" value="{{ $checks->first()->akhir ?? '' }}" class="input" required></div>
      <div><label class="label-y">METER AKHIR</label><input id="akhirInput" type="number" step="0.01" name="akhir" class="input input-yellow" required autofocus></div>
    </div>
    <div><label class="label">HARGA /KWH</label><div class="flex-gap"><input id="hargaInput" type="number" step="0.01" name="harga" value="1487.20" class="input" style="margin:0;flex:1"><div id="liveHitung" class="live">0 kWh = Rp 0</div></div></div>
    <button type="submit" class="btn-save">Simpan PLN</button>
  </form></div>
</div>

{{-- ============ MODAL HARIAN ============ --}}
<div id="modalHarian" class="modal" onclick="if(event.target===this) closeModalHarian()"><div class="modal-box">
  <div class="grabber"></div>
  <div class="modal-head"><h2 class="bold">Transaksi Harian</h2><button type="button" onclick="closeModalHarian()" class="btn-x"><svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button></div>
  <p class="modal-sub">Catat uang masuk / keluar</p>
  <form action="{{ route('harian.store') }}" method="POST" class="form">@csrf
    <div><label class="label">TANGGAL</label><input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="input" required></div>
    <div><label class="label">URAIAN</label><input type="text" name="uraian" placeholder="cth: Gaji, Beli pulsa, Jajan" class="input" required></div>
    <div><label class="label">JENIS</label><div class="toggle" style="margin-top:6px">
      <button type="button" id="btnMasuk" onclick="setJenis('masuk')" class="on-in">Masuk</button>
      <button type="button" id="btnKeluar" onclick="setJenis('keluar')">Keluar</button></div>
      <input type="hidden" id="jenisInput" value="masuk"></div>
    <div><label class="label">NOMINAL</label><input id="nominalInput" type="number" step="0.01" name="kredit" placeholder="0" class="input" style="font-size:18px;font-weight:700" required></div>
    <input type="hidden" id="kreditHidden" name="kredit" value="0">
    <input type="hidden" id="debitHidden" name="debit" value="0">
    <div><label class="label">CATATAN</label><input type="text" name="catatan" placeholder="cth: buat bayar listrik" class="input"></div>
    <div id="previewSaldo" class="live" style="justify-content:space-between">Saldo baru: Rp 0</div>
    <button type="submit" onclick="siapkanNominal()" class="btn-save green">Simpan Transaksi</button>
  </form></div>
</div>

{{-- ============ MODAL HAPUS SATU ============ --}}
<div id="deleteModal" class="modal" style="z-index:60" onclick="if(event.target===this) closeDeleteModal()"><div class="modal-box del-box">
  <div class="del-icon"><svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg></div>
  <h3 class="bold" style="font-size:18px">Peringatan..!</h3><p id="deleteText" class="del-text"></p>
  <div class="del-grid"><button type="button" onclick="closeDeleteModal()" class="btn-cancel">Batal</button>
  <form id="deleteForm" method="POST" style="display:contents">@csrf @method('DELETE')<button type="submit" class="btn-confirm">Ya, Hapus</button></form></div>
</div></div>

{{-- ============ BULK BAR ============ --}}
<div id="bulkBarPln" class="bulk-bar"><div class="bulk-info"><span id="bulkTextPln">0 dipilih</span><small>Tap Hapus untuk menghapus massal</small></div><div class="bulk-actions"><button type="button" onclick="clearSelection('pln')" class="btn-bulk-cancel">Batal</button><button type="button" onclick="openBulkModal('pln')" class="btn-bulk-del">Hapus</button></div></div>
<div id="bulkBarHarian" class="bulk-bar"><div class="bulk-info"><span id="bulkTextHarian">0 dipilih</span><small>Tap Hapus untuk menghapus massal</small></div><div class="bulk-actions"><button type="button" onclick="clearSelection('harian')" class="btn-bulk-cancel">Batal</button><button type="button" onclick="openBulkModal('harian')" class="btn-bulk-del">Hapus</button></div></div>

{{-- ============ MODAL HAPUS MASSAL ============ --}}
<div id="bulkModal" class="modal" style="z-index:60" onclick="if(event.target===this) closeBulkModal()"><div class="modal-box del-box">
  <div class="del-icon"><svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg></div>
  <h3 class="bold" style="font-size:18px">Hapus data?</h3><p class="del-text">Data yang dihapus tidak bisa dikembalikan.</p>
  <div id="bulkList" class="bulk-modal-list"></div>
  <div class="del-grid"><button type="button" onclick="closeBulkModal()" class="btn-cancel">Batal</button><button type="button" onclick="confirmBulkDelete()" class="btn-confirm">Ya, Hapus Semua</button></div>
</div></div>

<script src="{{ asset('js/super.js') }}?v=5"></script>
</body>
</html>

