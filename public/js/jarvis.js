/* YUNDA v11 - BAGIAN 1 DARI 2 */
let yundaRec = null, yundaOn = false, yundaStep = 'idle';
let yundaData = { jenis: 'keluar', nominal: 0, uraian: '', meter: '' };
let isSpeaking = false;
let idleTimer = null, tutupTimer = null;
let sudahDitanyaIdle = false;
let terakhirSelesaiNgomong = 0;
let kalimatTerakhirYunda = '';

function getYundaVoice(){
  let v = speechSynthesis.getVoices();
  if(!v.length) return null;
  return v.find(x => x.name === 'Google Bahasa Indonesia') ||
         v.find(x => x.name.includes('Google Bahasa Indonesia')) ||
         v.find(x => x.name.toLowerCase().includes('google') && x.lang.includes('id')) ||
         v.find(x => x.name.includes('Microsoft Andika')) ||
         v.find(x => x.name.includes('Microsoft Gadis')) ||
         v.find(x => x.lang === 'id-ID') ||
         v.find(x => x.lang && x.lang.includes('id')) ||
         v[0];
}
function speakViaMp3(txt, cb){
  try{
    let old = document.getElementById('yundaAudio'); if(old) old.remove();
    let a = document.createElement('audio');
    a.id = 'yundaAudio'; a.autoplay = true;
    a.src = 'https://translate.google.com/translate_tts?ie=UTF-8&client=tw-ob&q='+encodeURIComponent(txt.substring(0,180))+'&tl=id';
    a.onended = ()=>{ isSpeaking = false; terakhirSelesaiNgomong = Date.now(); if(cb) cb(); resetIdleTimer(); };
    a.onerror = ()=>{ isSpeaking = false; terakhirSelesaiNgomong = Date.now(); if(cb) cb(); resetIdleTimer(); };
    document.body.appendChild(a);
    a.play().catch(()=>{});
    setTimeout(()=>{ if(isSpeaking){ isSpeaking=false; terakhirSelesaiNgomong=Date.now(); if(cb) cb(); resetIdleTimer(); } }, 6000);
  }catch(e){ isSpeaking=false; terakhirSelesaiNgomong=Date.now(); if(cb) cb(); resetIdleTimer(); }
}
function buatNatural(txt){
  return txt.replace(/, /g, ', ... ').replace(/\. /g, '. ... ').replace(/\? /g, '? ... ');
}
function speakYunda(txt, cb){
  let el = document.getElementById('yundaReply'); if(el) el.textContent = txt;
  let el2 = document.getElementById('jarvisReply'); if(el2) el2.textContent = txt;
  kalimatTerakhirYunda = txt.toLowerCase();
  resetIdleTimer();
  isSpeaking = true;
  if(!('speechSynthesis' in window)){ speakViaMp3(txt, cb); return; }
  try{
    if(speechSynthesis.speaking) speechSynthesis.cancel();
    setTimeout(()=>{
      let u = new SpeechSynthesisUtterance(buatNatural(txt));
      u.lang = 'id-ID'; u.rate = 1.0; u.pitch = 1.05; u.volume = 1;
      let vv = getYundaVoice(); if(vv) u.voice = vv;
      let done = false;
      let selesai = ()=>{
        if(done) return; done = true;
        setTimeout(()=>{ isSpeaking = false; terakhirSelesaiNgomong = Date.now(); if(cb) cb(); resetIdleTimer(); }, 400);
      };
      u.onend = selesai;
      u.onerror = ()=>{ if(done) return; done = true; speakViaMp3(txt, cb); };
      speechSynthesis.speak(u);
      let durasi = Math.max(5000, txt.length * 105);
      setTimeout(()=>{ if(!done) selesai(); }, durasi);
    }, 200);
  }catch(e){ speakViaMp3(txt, cb); }
}
function parseRupiah(str){
  str = str.toLowerCase().replace(/rupiah|rp\.?|ribu|rb|k\b/g, '000').replace(/,/g, '');
  let m = str.match(/(\d[\d\s\.]*)/); if(!m) return 0;
  return parseInt(m[1].replace(/[\s\.]/g, '')) || 0;
}
function salamWaktu(){ let h=new Date().getHours(); if(h<11) return 'Selamat pagi'; if(h<15) return 'Selamat siang'; if(h<18) return 'Selamat sore'; return 'Selamat malam'; }
function tentukanJenis(t){
  if(t.includes('bukan keluar') || t.includes('bukan uang keluar')) return 'masuk';
  if(t.includes('bukan masuk') || t.includes('bukan uang masuk')) return 'keluar';
  if(t.includes('pemasukan') || t.includes('penghasilan') || t.includes('gajian') || t.includes('dibayar')) return 'masuk';
  if(t.includes('pengeluaran') || t.includes('belanja') || t.includes('bayar')) return 'keluar';
  let adaMasuk = /\bmasuk\b/.test(t);
  let adaKeluar = /\bkeluar\b/.test(t);
  if(adaMasuk && adaKeluar) return null;
  if(adaMasuk) return 'masuk';
  if(adaKeluar) return 'keluar';
  return null;
}
function resetIdleTimer(){
  sudahDitanyaIdle = false;
  if(idleTimer) clearTimeout(idleTimer);
  if(tutupTimer) clearTimeout(tutupTimer);
  if(!yundaOn) return;
  idleTimer = setTimeout(()=>{
    if(!yundaOn || isSpeaking) return;
    sudahDitanyaIdle = true;
    speakYunda('Apakah ada yang bisa saya bantu? Jika tidak, saya akan menutup percakapan.', ()=>{
      if(tutupTimer) clearTimeout(tutupTimer);
      tutupTimer = setTimeout(()=>{
        if(!yundaOn) return;
        speakYunda('Baik, saya menutup percakapan. Sampai jumpa.', ()=>{ stopYunda(); });
      }, 10000);
    });
  }, 30000);
}
function tanyaAwal(){
  yundaStep='tanya_jenis'; yundaData={ jenis:'keluar', nominal:0, uraian:'', meter:'' };
  speakYunda('Halo, saya Yunda. '+salamWaktu()+'. Mau dibantu apa hari ini? Bisa catat PLN, catat Harian, cek saldo, atau lihat laporan.');
  let t=document.getElementById('jarvisText'); if(t) t.textContent='Yunda siap membantu. Coba bilang: "Harian", "Saldo", "Laporan", atau "Bantuan"';
}
function bacaLaporan(){
  let saldo=document.body.dataset.saldo||'0';
  let masuk=document.querySelector('#tabHarian .grid3 .card:nth-child(2) .big2')?.textContent||'0';
  let keluar=document.querySelector('#tabHarian .grid3 .card:nth-child(3) .big2')?.textContent||'0';
  speakYunda('Ini laporan hari ini. Uang masuk, '+masuk+'. Uang keluar, '+keluar+'. Jadi saldo Anda sekarang, Rp '+Number(saldo).toLocaleString('id-ID')+'. Mau catat lagi, atau sudah cukup?');
  yundaStep='tanya_lagi';
}
function bacaSaldo(){
  let s=document.body.dataset.saldo||'0';
  speakYunda('Saldo Anda saat ini, Rp '+Number(s).toLocaleString('id-ID')+'. Mau saya bantu apa lagi?');
  yundaStep='tanya_lagi';
}
function bacaBantuan(){
  speakYunda('Saya bisa membantu banyak hal. Coba bilang, catat Harian, catat PLN, cek saldo, lihat laporan, buka Harian, buka PLN, hapus terakhir, cari pulsa, jam berapa, atau tanggal berapa. Mau coba yang mana?');
  yundaStep='tanya_jenis';
}
function cariTransaksi(kata){
  let rows = document.querySelectorAll('#tabHarian table tbody tr, #tabHarian .list-item, #tabHarian .item');
  let ketemu = 0;
  rows.forEach(r=>{ if(r.textContent.toLowerCase().includes(kata)) ketemu++; });
  if(ketemu>0) speakYunda('Saya menemukan '+ketemu+' transaksi yang berhubungan dengan '+kata+'. Silakan lihat di layar. Mau cari yang lain?');
  else speakYunda('Maaf, saya tidak menemukan transaksi '+kata+' hari ini. Mau cari yang lain?');
  yundaStep='tanya_lagi';
}
function hapusTerakhir(){
  let btns = document.querySelectorAll('#tabHarian .btn-hapus, #tabHarian [onclick*="hapus"], #tabHarian .delete-btn');
  if(btns.length>0){
    speakYunda('Transaksi terakhir sudah saya tandai. Silakan tekan tombol hapus di layar untuk mengonfirmasi. Atau bilang batal kalau tidak jadi.');
    yundaStep='tanya_lagi';
  } else {
    speakYunda('Maaf, saya tidak menemukan tombol hapus di layar ini. Coba buka tab Harian dulu, lalu bilang hapus lagi.');
    yundaStep='tanya_jenis';
  }
}
/* YUNDA v11 - BAGIAN 2 DARI 2 */
function handlePerintahUmum(t){
  if(t.includes('bantuan') || t.includes('bisa apa')){
    bacaBantuan(); return true;
  }
  if(t.includes('halo') || t.includes('hai yunda')){
    if(t.length < 25){ speakYunda('Halo juga. Senang bisa membantu Anda. Mau dibantu apa hari ini?'); yundaStep='tanya_jenis'; return true; }
  }
  if(t.includes('siapa kamu') || t.includes('nama kamu') || t.includes('kamu siapa')){
    speakYunda('Saya Yunda. Asisten keuangan Anda. Saya bantu catat PLN, catat Harian, cek saldo, dan lihat laporan.'); yundaStep='tanya_jenis'; return true;
  }
  if(t.includes('terima kasih') || t.includes('makasih')){
    speakYunda('Sama-sama. Senang bisa membantu. Mau dibantu apa lagi?'); yundaStep='tanya_jenis'; return true;
  }
  if(t.includes('jam berapa') || t.includes('pukul berapa')){
    let jam = new Date().toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});
    speakYunda('Sekarang pukul '+jam+'. Mau saya bantu apa lagi?'); yundaStep='tanya_jenis'; return true;
  }
  if(t.includes('tanggal') || t.includes('hari apa')){
    let tgl = new Date().toLocaleDateString('id-ID', {weekday:'long', day:'numeric', month:'long', year:'numeric'});
    speakYunda('Hari ini, '+tgl+'. Mau saya bantu apa lagi?'); yundaStep='tanya_jenis'; return true;
  }
  if(t.includes('buka harian')){
    if(window.switchTab) switchTab('harian');
    speakYunda('Sudah saya bukakan tab Harian. Mau saya bantu apa lagi?'); yundaStep='tanya_jenis'; return true;
  }
  if(t.includes('buka pln')){
    if(window.switchTab) switchTab('pln');
    speakYunda('Sudah saya bukakan tab PLN. Mau saya bantu apa lagi?'); yundaStep='tanya_jenis'; return true;
  }
  if(t.includes('tutup modal') || t.includes('tutup form')){
    let modal = document.querySelector('.modal.show, .modal.open, #modalInput');
    if(modal) modal.classList.remove('show','open');
    speakYunda('Sudah saya tutup. Mau saya bantu apa lagi?'); yundaStep='tanya_jenis'; return true;
  }
  if(t.includes('cari ')){
    let kata = t.replace(/cariin|cari transaksi|cari|transaksi/g,'').trim();
    if(kata){ cariTransaksi(kata); return true; }
    speakYunda('Mau cari transaksi apa? Sebutkan kata kuncinya, misalnya pulsa.'); yundaStep='tanya_jenis'; return true;
  }
  if(t.includes('hapus terakhir')){
    hapusTerakhir(); return true;
  }
  if(t.includes('berapa pemasukan') || t.includes('total masuk')){
    let masuk=document.querySelector('#tabHarian .grid3 .card:nth-child(2) .big2')?.textContent||'0';
    speakYunda('Total uang masuk hari ini, '+masuk+'. Mau saya bantu apa lagi?'); yundaStep='tanya_jenis'; return true;
  }
  if(t.includes('berapa pengeluaran') || t.includes('total keluar')){
    let keluar=document.querySelector('#tabHarian .grid3 .card:nth-child(3) .big2')?.textContent||'0';
    speakYunda('Total uang keluar hari ini, '+keluar+'. Mau saya bantu apa lagi?'); yundaStep='tanya_jenis'; return true;
  }
  if(t.includes('kamu cantik') || t.includes('kamu pintar')){
    speakYunda('Terima kasih. Anda juga baik. Mau saya bantu apa lagi?'); yundaStep='tanya_jenis'; return true;
  }
  if(t.includes('nyanyi') || t.includes('cerita')){
    speakYunda('Maaf, saya belum bisa bernyanyi. Tapi saya bisa bantu catat keuangan Anda. Mau catat apa hari ini?'); yundaStep='tanya_jenis'; return true;
  }
  if(t.includes('cuaca')){
    speakYunda('Maaf, saya belum bisa melihat cuaca. Tapi saya bisa bantu cek saldo atau laporan. Mau yang mana?'); yundaStep='tanya_jenis'; return true;
  }
  return false;
}
function handleYundaCommand(raw){
  if(!yundaOn) return;
  if(isSpeaking) return;
  if(Date.now() - terakhirSelesaiNgomong < 1500) return;
  let tBersih = raw.toLowerCase().trim();
  if(!tBersih) return;
  if(kalimatTerakhirYunda && (tBersih.includes('sebutkan jenis') || tBersih.includes('saya yunda') || tBersih.includes('pln atau harian') || tBersih.includes('mau dibantu apa'))) return;
  if(tBersih.includes('berikut laporan') || tBersih.includes('saldo anda saat ini') || tBersih.includes('ini laporan hari ini')) return;
  resetIdleTimer();
  sudahDitanyaIdle = false;
  let t = tBersih;
  let txtEl=document.getElementById('jarvisText'); if(txtEl) txtEl.textContent='Anda: "'+raw+'"';
  if(t.includes('tutup')||t.includes('berhenti')||t.includes('diam')){
    speakYunda('Baik, saya menutup percakapan. Sampai jumpa.', ()=>{ stopYunda(); }); return;
  }
  if((t.includes('tidak')||t.includes('enggak')) && (sudahDitanyaIdle || yundaStep==='tanya_lagi')){
    speakYunda('Baik, saya menutup percakapan. Sampai jumpa.', ()=>{ stopYunda(); }); return;
  }
  if(t.includes('batal')||t.includes('ulang')||t.includes('ulangi')){
    speakYunda('Baik, kita ulangi dari awal. Mau dibantu apa hari ini?'); yundaStep='tanya_jenis'; return;
  }
  if(yundaStep==='tanya_jenis' || yundaStep==='idle' || yundaStep==='tanya_lagi'){
    if(handlePerintahUmum(t)) return;
  }
  if(t.includes('laporan')){ bacaLaporan(); return; }
  if(t.includes('saldo')){ bacaSaldo(); return; }
  if(yundaStep==='tanya_jenis'||yundaStep==='idle'){
    if(t.includes('harian')){
      let j = tentukanJenis(t);
      if(j){
        yundaData.jenis = j;
        let nom = parseRupiah(t);
        if(nom > 0){
          yundaData.nominal = nom;
          yundaData.uraian = t.replace(/input|harian|keluar|masuk|uang|rupiah|rp\.?|ribu|rb|catat|\d[\d\s\.]*/g,'').trim()||'Pengeluaran';
          yundaStep='konfirm_harian';
          speakYunda('Uang '+yundaData.jenis+', sebesar '+nom.toLocaleString('id-ID')+', untuk '+yundaData.uraian+'. Apakah benar?');
          return;
        }
        yundaStep='tanya_nominal';
        speakYunda('Baik, uang '+j+'. Berapa nominalnya?');
        return;
      }
      yundaStep='tanya_masuk_keluar';
      speakYunda('Baik, untuk Harian. Silakan sebutkan jenis transaksinya.');
      return;
    }
    if(t.includes('pln') || t.includes('listrik') || t.includes('meter')){
      yundaStep='tanya_meter'; speakYunda('Baik, untuk PLN. Berapa meter akhir hari ini?'); return;
    }
    if(t.includes('uang') || /\bmasuk\b/.test(t) || /\bkeluar\b/.test(t) || t.includes('pemasukan') || t.includes('pengeluaran')){
      let j = tentukanJenis(t);
      if(j){
        yundaData.jenis = j;
        let nom = parseRupiah(t);
        if(nom > 0){
          yundaData.nominal = nom;
          yundaData.uraian = t.replace(/input|harian|keluar|masuk|uang|rupiah|rp\.?|ribu|rb|catat|\d[\d\s\.]*/g,'').trim()||'Pengeluaran';
          yundaStep='konfirm_harian';
          speakYunda('Uang '+yundaData.jenis+', sebesar '+nom.toLocaleString('id-ID')+', untuk '+yundaData.uraian+'. Apakah benar?');
          return;
        }
        yundaStep='tanya_nominal';
        speakYunda('Baik, uang '+j+'. Berapa nominalnya?');
        return;
      }
      yundaStep='tanya_masuk_keluar';
      speakYunda('Baik, untuk Harian. Silakan sebutkan jenis transaksinya.');
      return;
    }
    speakYunda('Maaf, saya belum jelas. Mau dibantu apa? Coba bilang, Harian, PLN, saldo, laporan, atau bantuan.'); return;
  }
  if(yundaStep==='tanya_meter'){
    let num=t.replace(/[^0-9]/g,''); if(!num){ speakYunda('Berapa meter akhirnya? Silakan sebutkan angkanya saja.'); return; }
    yundaData.meter=num; yundaStep='konfirm_pln'; speakYunda('Meter akhir, '+num+'. Apakah benar? Silakan jawab ya atau tidak.'); return;
  }
  if(yundaStep==='konfirm_pln'){
    if(t.includes('iya')||t.includes('betul')||t.includes('benar')||t==='ya'){
      switchTab('pln'); openModalByTab();
      setTimeout(()=>{ let el=document.getElementById('akhirInput'); if(el){ el.value=yundaData.meter; el.dispatchEvent(new Event('input')); } speakYunda('Sudah saya isikan. Meter akhir, '+yundaData.meter+'. Silakan tekan tombol Simpan.'); yundaStep='tanya_lagi'; },400); return;
    }
    if(t.includes('tidak')||t.includes('salah')){ yundaStep='tanya_meter'; speakYunda('Baik, silakan sebutkan kembali berapa meter akhirnya.'); return; }
    speakYunda('Apakah benar atau tidak? Silakan jawab ya atau tidak.'); return;
  }
  if(yundaStep==='tanya_masuk_keluar'){
    let j = tentukanJenis(t);
    if(!j){ speakYunda('Maaf, saya belum jelas. Silakan sebutkan satu kata saja, masuk atau keluar.'); return; }
    yundaData.jenis = j;
    let nom = parseRupiah(t);
    if(nom>0){
      yundaData.nominal=nom;
      yundaData.uraian=t.replace(/input|harian|keluar|masuk|uang|rupiah|rp\.?|ribu|rb|\d[\d\s\.]*/g,'').trim()||'Pengeluaran';
      yundaStep='konfirm_harian';
      speakYunda('Uang '+yundaData.jenis+', sebesar '+nom.toLocaleString('id-ID')+', untuk '+yundaData.uraian+'. Apakah benar?');
      return;
    }
    yundaStep='tanya_nominal';
    speakYunda('Baik, uang '+j+'. Berapa nominalnya?');
    return;
  }
  if(yundaStep==='tanya_nominal'){
    let nom=parseRupiah(t); if(!nom){ speakYunda('Berapa nominalnya? Misalnya lima puluh ribu.'); return; }
    yundaData.nominal=nom; yundaStep='tanya_uraian'; speakYunda(nom.toLocaleString('id-ID')+', untuk keperluan apa? Misalnya membeli pulsa, gaji, atau jajan.'); return;
  }
  if(yundaStep==='tanya_uraian'){
    yundaData.uraian=t.replace(/buat|untuk|beli|bayar/g,'').trim()||'Pengeluaran'; yundaStep='konfirm_harian'; speakYunda('Uang '+yundaData.jenis+', sebesar '+yundaData.nominal.toLocaleString('id-ID')+', untuk '+yundaData.uraian+'. Apakah benar? Silakan jawab ya atau tidak.'); return;
  }
  if(yundaStep==='konfirm_harian'){
    if(t.includes('iya')||t.includes('betul')||t.includes('benar')||t==='ya'){
      switchTab('harian'); openModalByTab();
      setTimeout(()=>{ if(window.setJenis) setJenis(yundaData.jenis); let n=document.getElementById('nominalInput'); if(n) n.value=yundaData.nominal; let u=document.getElementById('uraianInput'); if(u) u.value=yundaData.uraian.charAt(0).toUpperCase()+yundaData.uraian.slice(1); if(window.siapkanNominal) siapkanNominal(); speakYunda('Sudah saya isikan. Silakan tekan tombol Simpan.'); yundaStep='tanya_lagi'; },400); return;
    }
    if(t.includes('tidak')||t.includes('salah')){ yundaStep='tanya_masuk_keluar'; speakYunda('Baik, kita ulangi. Silakan sebutkan jenis transaksinya.'); return; }
    speakYunda('Apakah benar atau tidak?'); return;
  }
  if(yundaStep==='tanya_lagi'){
    if(t.includes('lagi')||t.includes('iya')||t.includes('mau')||t.includes('ingin')){ speakYunda('Baik, mau dibantu apa lagi?'); yundaStep='tanya_jenis'; return; }
    if(t.includes('sudah')||t.includes('cukup')||t.includes('tidak')){ speakYunda('Baik, terima kasih. Saya menutup percakapan. Sampai jumpa.', ()=>{ stopYunda(); }); return; }
    speakYunda('Mau dibantu apa lagi? Coba bilang, Harian, PLN, saldo, laporan, atau bantuan.'); return;
  }
}
function startYunda(){
  let SR=window.SpeechRecognition||window.webkitSpeechRecognition;
  if(!SR){ alert('Peramban ini tidak mendukung mikrofon. Silakan gunakan Chrome pada Android.'); return; }
  let overlay = document.getElementById('jarvisOverlay'); if(overlay) overlay.classList.add('show');
  let dot = document.getElementById('jarvisDot'); if(dot) dot.classList.add('listening');
  let btn = document.getElementById('jarvisBtn'); if(btn) btn.classList.add('listening');
  yundaOn=true; yundaStep='idle'; sudahDitanyaIdle=false;
  if(yundaRec) try{ yundaRec.onend=null; yundaRec.stop(); }catch(e){}
  yundaRec=new SR();
  yundaRec.lang='id-ID'; yundaRec.continuous=true; yundaRec.interimResults=false; yundaRec.maxAlternatives=1;
  yundaRec.onresult=e=>{ let hasil = e.results[e.results.length-1]; if(hasil.isFinal) handleYundaCommand(hasil[0].transcript); };
  yundaRec.onerror=(e)=>{ let t=document.getElementById('jarvisText'); if(t) t.textContent='Kesalahan mikrofon: '+e.error; resetIdleTimer(); };
  yundaRec.onend=()=>{ if(yundaOn){ setTimeout(()=>{ if(yundaOn && yundaRec){ try{ yundaRec.start(); }catch(e){} } }, 500); } };
  try{ yundaRec.start(); }catch(e){}
  tanyaAwal();
  resetIdleTimer();
}
function stopYunda(){
  yundaOn=false; yundaStep='idle'; isSpeaking=false;
  if(idleTimer) clearTimeout(idleTimer);
  if(tutupTimer) clearTimeout(tutupTimer);
  if(yundaRec) try{ yundaRec.onend=null; yundaRec.stop(); }catch(e){}
  let overlay=document.getElementById('jarvisOverlay'); if(overlay) overlay.classList.remove('show');
  let dot=document.getElementById('jarvisDot'); if(dot) dot.classList.remove('listening');
  let btn=document.getElementById('jarvisBtn'); if(btn) btn.classList.remove('listening');
  try{ if(speechSynthesis.speaking) speechSynthesis.cancel(); }catch(e){}
}
function testYundaSuara(){ speakYunda('Halo, saya Yunda. Gimana, suara saya sudah enak belum?'); }
function startJarvis(){ startYunda(); }
function stopJarvis(){ stopYunda(); }
function testJarvisSuara(){ testYundaSuara(); }
document.addEventListener('DOMContentLoaded',()=>{
  let btn=document.getElementById('jarvisBtn');
  if(btn) btn.addEventListener('click',()=>{ yundaOn ? stopYunda() : startYunda(); });
  if('speechSynthesis' in window){ speechSynthesis.onvoiceschanged=()=>{}; speechSynthesis.getVoices(); }
});
