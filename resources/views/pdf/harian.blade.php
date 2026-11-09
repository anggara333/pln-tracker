<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  @page { margin: 20px 25px; }
  body { font-family: 'DejaVu Sans Mono', monospace; font-size: 11px; color: #111; }
  .header { display: table; width: 100%; border-bottom: 3px solid #000; padding-bottom: 8px; margin-bottom: 12px; }
  .header-left { display: table-cell; }
  .header-right { display: table-cell; text-align: right; vertical-align: bottom; color: #666; font-size: 10px; }
  h2 { margin: 0; font-size: 18px; letter-spacing: -0.5px; }
  .sub { margin: 2px 0 0; color: #666; font-size: 10px; }
  .tot-box { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
  .tot-box td { border: 1px solid #000; background: #f8fafc; padding: 10px; text-align: center; width: 33.33%; }
  .tot-label { font-size: 9px; color: #666; letter-spacing: 1px; display: block; margin-bottom: 3px; }
  .tot-val { font-size: 13px; font-weight: bold; }
  table.data { width: 100%; border-collapse: collapse; }
  table.data th { background: #000; color: #fff; padding: 8px 6px; font-size: 10px; text-align: left; letter-spacing: 0.5px; }
  table.data th.num, table.data td.num { text-align: right; }
  table.data th.center, table.data td.center { text-align: center; }
  table.data td { border-bottom: 1px solid #ddd; padding: 7px 6px; vertical-align: top; }
  table.data tr:nth-child(even) td { background: #f9fafb; }
  .in { font-weight: bold; }
  .out { font-weight: bold; }
  .saldo { font-weight: bold; }
  .uraian { font-weight: bold; }
  .catatan { color: #666; font-size: 10px; }
  .footer { margin-top: 14px; display: table; width: 100%; font-size: 10px; color: #666; border-top: 1px solid #000; padding-top: 8px; }
  .footer-left { display: table-cell; }
  .footer-right { display: table-cell; text-align: right; }
</style>
</head>
<body>

<div class="header">
  <div class="header-left">
    <h2>LAPORAN HARIAN</h2>
    <p class="sub">Super Apps - Laporan Keuangan Harian</p>
  </div>
  <div class="header-right">
    Total Transaksi: {{ $harians->count() }} data
  </div>
</div>

<table class="tot-box"><tr>
  <td>
    <span class="tot-label">SALDO AKHIR</span>
    <span class="tot-val">Rp {{ number_format($saldoAkhir,0,',','.') }}</span>
  </td>
  <td>
    <span class="tot-label">TOTAL MASUK</span>
    <span class="tot-val">Rp {{ number_format($totalKredit,0,',','.') }}</span>
  </td>
  <td>
    <span class="tot-label">TOTAL KELUAR</span>
    <span class="tot-val">Rp {{ number_format($totalDebit,0,',','.') }}</span>
  </td>
</tr></table>

<table class="data">
  <thead>
    <tr>
      <th style="width:14%">TANGGAL</th>
      <th style="width:28%">URAIAN</th>
      <th class="num" style="width:16%">MASUK</th>
      <th class="num" style="width:16%">KELUAR</th>
      <th class="num" style="width:16%">SALDO</th>
      <th style="width:10%">CATATAN</th>
    </tr>
  </thead>
  <tbody>
    @forelse($harians as $h)
    <tr>
      <td class="center">{{ $h->tanggal->format('d M Y') }}</td>
      <td><span class="uraian">{{ $h->uraian }}</span></td>
      <td class="num in">{{ $h->kredit > 0 ? 'Rp '.number_format($h->kredit,0,',','.') : '-' }}</td>
      <td class="num out">{{ $h->debit > 0 ? 'Rp '.number_format($h->debit,0,',','.') : '-' }}</td>
      <td class="num saldo">Rp {{ number_format($h->saldo,0,',','.') }}</td>
      <td><span class="catatan">{{ $h->catatan ?? '-' }}</span></td>
    </tr>
    @empty
    <tr><td colspan="6" style="text-align:center;padding:20px;color:#888">Belum ada transaksi harian</td></tr>
    @endforelse
  </tbody>
  @if($harians->count() > 0)
  <tfoot>
    <tr>
      <td colspan="2" style="text-align:right;font-weight:bold;background:#f3f4f6;border-top:2px solid #000">TOTAL</td>
      <td class="num" style="font-weight:bold;background:#f3f4f6;border-top:2px solid #000">Rp {{ number_format($totalKredit,0,',','.') }}</td>
      <td class="num" style="font-weight:bold;background:#f3f4f6;border-top:2px solid #000">Rp {{ number_format($totalDebit,0,',','.') }}</td>
      <td class="num" style="font-weight:bold;background:#f3f4f6;border-top:2px solid #000">Rp {{ number_format($saldoAkhir,0,',','.') }}</td>
      <td style="background:#f3f4f6;border-top:2px solid #000"></td>
    </tr>
  </tfoot>
  @endif
</table>

<div class="footer">
  <div class="footer-left">Super Apps - Daily Report</div>
  <div class="footer-right">Dicetak : {{ date('d M Y, H:i') }}</div>
</div>

</body>
</html>
