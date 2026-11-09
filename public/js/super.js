console.log('super.js v5 - Android 16 LOADED');
let currentTab = 'pln';
let jenis = 'masuk';
let saldoAwal = 0;
let selectMode = { pln:false, harian:false };
let pendingBulkType = 'pln';

const $ = (id) => document.getElementById(id);

document.addEventListener('DOMContentLoaded', () => {
  saldoAwal = parseFloat(document.body.dataset.saldo || 0);
  if(localStorage.getItem('tab')==='harian') switchTab('harian');
  const nominal = $('nominalInput');
  if(nominal) nominal.addEventListener('input', updatePreview);
  ['akhirInput','awalInput','hargaInput'].forEach(id=>{
    const el = $(id);
    if(el) el.addEventListener('input', hitungLive);
  });
  document.addEventListener('keydown', (e)=>{
    if(e.key==='Escape'){
      closeModal(); closeModalHarian(); closeDeleteModal(); closeBulkModal();
    }
  });
  initRipple();
  initSwipeSheet();
  hitungLive();
  updatePreview();
});

function switchTab(tab){
  currentTab = tab;
  $('tabPln').classList.toggle('active', tab==='pln');
  $('tabHarian').classList.toggle('active', tab==='harian');
  $('tabBtnPln').classList.toggle('active', tab==='pln');
  $('tabBtnHarian').classList.toggle('active', tab==='harian');
  $('tabBtnHarian').classList.toggle('active-green', tab==='harian');
  const topBtn = $('topBtn');
  const icon = '<svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> ';
  if(tab==='harian'){ topBtn.classList.add('green'); topBtn.innerHTML = icon + 'Transaksi'; }
  else { topBtn.classList.remove('green'); topBtn.innerHTML = icon + 'Input'; }
  if(navigator.vibrate) navigator.vibrate(10);
  localStorage.setItem('tab', tab);
}
function openModalByTab(){ if(currentTab==='harian') openModalHarian(); else openModal(); }
function openModal(){ $('modal').classList.add('open'); document.body.style.overflow='hidden'; setTimeout(()=>$('akhirInput')?.focus(), 300); }
function closeModal(){ $('modal').classList.remove('open'); document.body.style.overflow=''; }
function openModalHarian(){ $('modalHarian').classList.add('open'); document.body.style.overflow='hidden'; updatePreview(); }
function closeModalHarian(){ $('modalHarian').classList.remove('open'); document.body.style.overflow=''; }
function setJenis(j){
  jenis=j;
  $('jenisInput').value=j;
  $('btnMasuk').className = j==='masuk' ? 'on-in' : '';
  $('btnKeluar').className = j==='keluar' ? 'on-out' : '';
  if(navigator.vibrate) navigator.vibrate(10);
  updatePreview();
}
function updatePreview(){
  const el = $('nominalInput'); if(!el) return;
  const nom = parseFloat(el.value)||0;
  const baru = jenis==='masuk' ? saldoAwal + nom : saldoAwal - nom;
  const prev = $('previewSaldo'); if(prev) prev.innerText = 'Saldo baru: Rp ' + baru.toLocaleString('id-ID');
}
function siapkanNominal(){
  const nom = parseFloat($('nominalInput').value)||0;
  $('kreditHidden').value = jenis==='masuk' ? nom : 0;
  $('debitHidden').value = jenis==='keluar' ? nom : 0;
  $('nominalInput').removeAttribute('name');
  if(navigator.vibrate) navigator.vibrate(20);
}
function openDeleteModal(id,tgl,tipe){
  $('deleteText').innerText='Yakin hapus data '+tgl+'?';
  $('deleteForm').action = tipe==='harian' ? '/harian/'+id : '/check/'+id;
  $('deleteModal').classList.add('open');
  if(navigator.vibrate) navigator.vibrate(15);
}
function closeDeleteModal(){ $('deleteModal').classList.remove('open'); }
function hitungLive(){
  const awalEl = $('awalInput'), akhirEl = $('akhirInput'), hargaEl = $('hargaInput'), el = $('liveHitung');
  if(!awalEl || !akhirEl || !el) return;
  const awal=parseFloat(awalEl.value)||0;
  const akhir=parseFloat(akhirEl.value)||0;
  const harga=parseFloat(hargaEl?.value)||1487.2;
  const selisih = akhir - awal;
  if(selisih > 0) el.innerText = selisih.toFixed(2) + ' kWh = Rp ' + Math.round(selisih*harga).toLocaleString('id-ID');
  else el.innerText = '0 kWh = Rp 0';
}
// === BULK ===
function toggleSelectMode(tipe){
  selectMode[tipe] = !selectMode[tipe];
  const btn = $(tipe==='pln' ? 'btnSelectPln' : 'btnSelectHarian');
  if(!btn) return;
  btn.classList.toggle('active', selectMode[tipe] && tipe==='pln');
  btn.classList.toggle('active-green', selectMode[tipe] && tipe==='harian');
  btn.innerText = selectMode[tipe] ? 'Tutup Pilih' : 'Pilih';
  document.querySelectorAll(tipe==='pln' ? '.chk-pln' : '.chk-harian').forEach(c=>c.style.display = selectMode[tipe] ? 'block' : 'none');
  if(!selectMode[tipe]){
    document.querySelectorAll(tipe==='pln' ? '.chk-pln' : '.chk-harian').forEach(c=>c.checked=false);
    updateCount(tipe);
  }
  if(navigator.vibrate) navigator.vibrate(10);
}
function toggleAll(tipe){
  const all = document.querySelectorAll(tipe==='pln' ? '.chk-pln' : '.chk-harian');
  if(all.length===0) return;
  if(!selectMode[tipe]) toggleSelectMode(tipe);
  const isAllChecked = [...all].every(c=>c.checked);
  all.forEach(c=>c.checked = !isAllChecked);
  const btnAll = $(tipe==='pln' ? 'btnAllPln' : 'btnAllHarian');
  if(btnAll) btnAll.innerText = !isAllChecked ? 'Batal Semua' : 'Semua';
  updateCount(tipe);
}
function updateCount(tipe){
  const checked = document.querySelectorAll(tipe==='pln' ? '.chk-pln:checked' : '.chk-harian:checked');
  const bar = $(tipe==='pln' ? 'bulkBarPln' : 'bulkBarHarian');
  const txt = $(tipe==='pln' ? 'bulkTextPln' : 'bulkTextHarian');
  document.querySelectorAll(tipe==='pln' ? '.chk-pln' : '.chk-harian').forEach(c=>{
    const row = $('row-'+tipe+'-'+c.value);
    if(row) row.classList.toggle('selected', c.checked);
  });
  if(txt) txt.innerText = checked.length + ' dipilih';
  if(bar) bar.classList.toggle('show', checked.length > 0);
}
function clearSelection(tipe){
  document.querySelectorAll(tipe==='pln' ? '.chk-pln' : '.chk-harian').forEach(c=>c.checked=false);
  const btnAll = $(tipe==='pln' ? 'btnAllPln' : 'btnAllHarian');
  if(btnAll) btnAll.innerText = 'Semua';
  updateCount(tipe);
  if(selectMode[tipe]) toggleSelectMode(tipe);
}
function openBulkModal(tipe){
  pendingBulkType = tipe;
  const checked = document.querySelectorAll(tipe==='pln' ? '.chk-pln:checked' : '.chk-harian:checked');
  if(checked.length===0) return;
  let html = '';
  checked.forEach(c=>{ html += '• <b>'+c.dataset.label+'</b><br>'; });
  $('bulkList').innerHTML = html + '<div style="margin-top:6px;color:#ffb4ab;font-weight:700">Total: '+checked.length+' data akan dihapus</div>';
  $('bulkModal').classList.add('open');
}
function closeBulkModal(){ $('bulkModal').classList.remove('open'); }
function confirmBulkDelete(){
  const tipe = pendingBulkType;
  const checked = document.querySelectorAll(tipe==='pln' ? '.chk-pln:checked' : '.chk-harian:checked');
  const form = $(tipe==='pln' ? 'formBulkPln' : 'formBulkHarian');
  if(!form || checked.length===0) return;
  form.querySelectorAll('input[name="ids[]"]').forEach(e=>e.remove());
  checked.forEach(c=>{
    const i=document.createElement('input'); i.type='hidden'; i.name='ids[]'; i.value=c.value; form.appendChild(i);
  });
  if(navigator.vibrate) navigator.vibrate([20,50,20]);
  form.submit();
}

// === ANDROID 16 - RIPPLE ===
function initRipple(){
  document.addEventListener('click', (e)=>{
    const btn = e.target.closest('button, .row, .tab, .btn-tool');
    if(!btn) return;
    if(btn.classList.contains('chk')) return;
    const rect = btn.getBoundingClientRect();
    const ripple = document.createElement('span');
    ripple.className = 'ripple';
    const size = Math.max(rect.width, rect.height);
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = (e.clientX - rect.left - size/2) + 'px';
    ripple.style.top = (e.clientY - rect.top - size/2) + 'px';
    const oldPos = btn.style.position;
    btn.style.position = 'relative';
    btn.style.overflow = 'hidden';
    btn.appendChild(ripple);
    setTimeout(()=>{ ripple.remove(); btn.style.position = oldPos; }, 500);
  });
}

// === ANDROID 16 - SWIPE DOWN NUTUP SHEET ===
function initSwipeSheet(){
  let startY = 0, dragging = false;
  document.querySelectorAll('.modal-box').forEach(box=>{
    box.addEventListener('touchstart', e=>{
      if(e.target.closest('.input, textarea, select')) return;
      startY = e.touches[0].clientY; dragging = true;
    }, {passive:true});
    box.addEventListener('touchmove', e=>{
      if(!dragging) return;
      const dy = e.touches[0].clientY - startY;
      if(dy > 0) box.style.transform = `translateY(${dy}px)`;
    }, {passive:true});
    box.addEventListener('touchend', e=>{
      if(!dragging) return;
      dragging = false;
      const dy = e.changedTouches[0].clientY - startY;
      box.style.transform = '';
      if(dy > 110){
        box.closest('.modal').classList.remove('open');
        document.body.style.overflow = '';
        if(navigator.vibrate) navigator.vibrate(10);
      }
    });
  });
}

