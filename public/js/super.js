console.log('super.js LOADED');
let currentTab = 'pln';
let jenis = 'masuk';
let saldoAwal = 0;
let selectMode = { pln:false, harian:false };
let pendingBulkType = 'pln';

document.addEventListener('DOMContentLoaded', () => {
  saldoAwal = parseFloat(document.body.dataset.saldo || 0);
  if(localStorage.getItem('tab')==='harian') switchTab('harian');
  const nominal = document.getElementById('nominalInput');
  if(nominal) nominal.addEventListener('input', updatePreview);
  ['akhirInput','awalInput','hargaInput'].forEach(id=>{
    const el = document.getElementById(id);
    if(el) el.addEventListener('input', hitungLive);
  });
});

function switchTab(tab){
  currentTab = tab;
  document.getElementById('tabPln').classList.toggle('active', tab==='pln');
  document.getElementById('tabHarian').classList.toggle('active', tab==='harian');
  document.getElementById('tabBtnPln').classList.toggle('active', tab==='pln');
  document.getElementById('tabBtnHarian').classList.toggle('active', tab==='harian');
  document.getElementById('tabBtnHarian').classList.toggle('active-green', tab==='harian');
  const topBtn = document.getElementById('topBtn');
  if(tab==='harian'){ topBtn.classList.add('green'); topBtn.innerHTML='Transaksi'; }
  else { topBtn.classList.remove('green'); topBtn.innerHTML='Input'; }
  localStorage.setItem('tab', tab);
}
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
function openDeleteModal(id,tgl,tipe){
  document.getElementById('deleteText').innerText='Yakin hapus data '+tgl+'?';
  document.getElementById('deleteForm').action = tipe==='harian' ? '/harian/'+id : '/check/'+id;
  document.getElementById('deleteModal').classList.add('open');
}
function closeDeleteModal(){ document.getElementById('deleteModal').classList.remove('open'); }
function hitungLive(){
  const awal=parseFloat(document.getElementById('awalInput').value)||0;
  const akhir=parseFloat(document.getElementById('akhirInput').value)||0;
  const harga=parseFloat(document.getElementById('hargaInput').value)||1487.2;
  const el=document.getElementById('liveHitung');
  if(akhir>awal) el.innerText=(akhir-awal)+' kWh = Rp '+Math.round((akhir-awal)*harga).toLocaleString('id-ID');
  else el.innerText='0 kWh = Rp 0';
}
function toggleSelectMode(tipe){
  selectMode[tipe] = !selectMode[tipe];
  const btn = document.getElementById(tipe==='pln' ? 'btnSelectPln' : 'btnSelectHarian');
  btn.classList.toggle('active', selectMode[tipe] && tipe==='pln');
  btn.classList.toggle('active-green', selectMode[tipe] && tipe==='harian');
  btn.innerText = selectMode[tipe] ? 'Tutup Pilih' : 'Pilih';
  document.querySelectorAll(tipe==='pln' ? '.chk-pln' : '.chk-harian').forEach(c=>c.style.display = selectMode[tipe] ? 'block' : 'none');
  if(!selectMode[tipe]) clearSelection(tipe, true);
}
function toggleAll(tipe){
  if(!selectMode[tipe]) toggleSelectMode(tipe);
  const all = document.querySelectorAll(tipe==='pln' ? '.chk-pln' : '.chk-harian');
  const isAllChecked = [...all].every(c=>c.checked);
  all.forEach(c=>c.checked = !isAllChecked);
  document.getElementById(tipe==='pln' ? 'btnAllPln' : 'btnAllHarian').innerText = !isAllChecked ? 'Batal Semua' : 'Semua';
  updateCount(tipe);
}
function updateCount(tipe){
  const checked = document.querySelectorAll(tipe==='pln' ? '.chk-pln:checked' : '.chk-harian:checked');
  const bar = document.getElementById(tipe==='pln' ? 'bulkBarPln' : 'bulkBarHarian');
  const txt = document.getElementById(tipe==='pln' ? 'bulkTextPln' : 'bulkTextHarian');
  document.querySelectorAll(tipe==='pln' ? '.chk-pln' : '.chk-harian').forEach(c=>{
    const row = document.getElementById('row-'+tipe+'-'+c.value);
    if(row) row.classList.toggle('selected', c.checked);
  });
  txt.innerText = checked.length + ' dipilih';
  bar.classList.toggle('show', checked.length > 0);
}
function clearSelection(tipe, silent=false){
  document.querySelectorAll(tipe==='pln' ? '.chk-pln' : '.chk-harian').forEach(c=>c.checked=false);
  const btnAll = document.getElementById(tipe==='pln' ? 'btnAllPln' : 'btnAllHarian');
  if(btnAll) btnAll.innerText = 'Semua';
  updateCount(tipe);
  if(!silent) toggleSelectMode(tipe);
}
function openBulkModal(tipe){
  pendingBulkType = tipe;
  const checked = document.querySelectorAll(tipe==='pln' ? '.chk-pln:checked' : '.chk-harian:checked');
  if(checked.length===0) return;
  let html = '';
  checked.forEach(c=>{ html += '• <b>'+c.dataset.label+'</b><br>'; });
  document.getElementById('bulkList').innerHTML = html + '<div style="margin-top:6px;color:#f87171;font-weight:700">Total: '+checked.length+' data akan dihapus</div>';
  document.getElementById('bulkModal').classList.add('open');
}
function closeBulkModal(){ document.getElementById('bulkModal').classList.remove('open'); }
function confirmBulkDelete(){
  const tipe = pendingBulkType;
  const checked = document.querySelectorAll(tipe==='pln' ? '.chk-pln:checked' : '.chk-harian:checked');
  const form = document.getElementById(tipe==='pln' ? 'formBulkPln' : 'formBulkHarian');
  form.querySelectorAll('input[name="ids[]"]').forEach(e=>e.remove());
  checked.forEach(c=>{
    const i=document.createElement('input'); i.type='hidden'; i.name='ids[]'; i.value=c.value; form.appendChild(i);
  });
  form.submit();
}

