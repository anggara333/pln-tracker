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
  .bayar { font-weight: bold; }
  .selisih { font-weight: bold; }
  .footer { margin-top: 14px; display: table; width: 100%; font-size: 10px; color: #666; border-top: 1px solid #000; padding-top: 8px; }
  .footer-left { display: table-cell; }
  .footer-right { display: table-cell; text-align: right; }
</style>
</head>
<body>

<div class="header">
  <div class="header-left">
    <h2>LAPORAN PLN</h2>
    <p class="sub">Super Apps - Laporan Pemakaian Listrik</p>
  </div>
  <div class="header-right">
    Total Data: {{ $checks->count() }} catatan
  </div>
</div>

<table class="tot-box"><tr>
  <td>
    <span class="tot-label">TOTAL PEMAKAIAN</span>
    <span class="tot-val">{{ number_format($totalKwh,2,',','.') }} kWh</span>
  </td>
  <td>
    <span class="tot-label">TOTAL BAYAR</span>
    <span class="tot-val">Rp {{ number_format($totalBayar,0,',','.') }}</span>
  </td>
  <td>
    <span class="tot-label">RATA-RATA / HARI</span>
    <span class="tot-val">{{ number_format($rataKwh,2,',','.') }} kWh</span>
  </td>
</tr></table>

<table class="data">
  <thead>
    <tr>
      <th style="width:16%">TANGGAL</th>
      <th class="num" style="width:20%">METER AWAL</th>
      <th class="num" style="width:20%">METER AKHIR</th>
      <th class="num" style="width:20%">PEMAKAIAN</th>
      <th class="num" style="width:24%">BAYAR</th>
    </tr>
  </thead>
  <tbody>
    @forelse($checks as $c)
    <tr>
      <td class="center">{{ $c->tanggal->format('d M Y') }}</td>
      <td class="num">{{ number_format($c->awal,2,',','.') }}</td>
      <td class="num">{{ number_format($c->akhir,2,',','.') }}</td>
      <td class="num selisih">{{ number_format($c->selisih,2,',','.') }} kWh</td>
      <td class="num bayar">Rp {{ number_format($c->pakai,0,',','.') }}</td>
    </tr>
    @empty
    <tr><td colspan="5" style="text-align:center;padding:20px;color:#888">Belum ada data PLN</td></tr>
    @endforelse
  </tbody>
  @if($checks->count() > 0)
  <tfoot>
    <tr>
      <td colspan="3" style="text-align:right;font-weight:bold;background:#f3f4f6;border-top:2px solid #000">TOTAL</td>
      <td class="num" style="font-weight:bold;background:#f3f4f6;border-top:2px solid #000">{{ number_format($totalKwh,2,',','.') }} kWh</td>
      <td class="num" style="font-weight:bold;background:#f3f4f6;border-top:2px solid #000">Rp {{ number_format($totalBayar,0,',','.') }}</td>
    </tr>
  </tfoot>
  @endif
</table>

<div class="footer">
  <div class="footer-left">Super Apps - PLN Tracker</div>
  <div class="footer-right">Dicetak : {{ date('d M Y, H:i') }}</div>
</div>

</body>
</html>

