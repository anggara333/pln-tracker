<?php

namespace App\Http\Controllers;

use App\Models\Harian;
use Illuminate\Http\Request;

class HarianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $harians = Harian::orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();

        $totalKredit = $harians->sum('kredit');
        $totalDebit = $harians->sum('debit');
        $saldoAkhir = $harians->first()?->saldo ?? 0;

        return view('harian.index', compact('harians', 'totalKredit', 'totalDebit', 'saldoAkhir'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'uraian' => 'required|string|max:255',
            'kredit' => 'nullable|numeric|min:0',
            'debit' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        $kredit = $request->kredit ?? 0;
        $debit = $request->debit ?? 0;

        // Saldo terakhir = saldo data paling baru
        $last = Harian::orderBy('id', 'desc')->first();
        $saldoSebelumnya = $last ? $last->saldo : 0;

        // Rumus: Saldo = Saldo Lama + Kredit - Debit
        $saldoBaru = $saldoSebelumnya + $kredit - $debit;

        Harian::create([
            'tanggal' => $request->tanggal,
            'uraian' => $request->uraian,
            'kredit' => $kredit,
            'debit' => $debit,
            'saldo' => $saldoBaru,
            'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Data harian berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Harian $harian)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Harian $harian)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Harian $harian)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $harian = Harian::findOrFail($id);
        $harian->delete();

        // Hitung ulang saldo dari awal biar gak loncat setelah hapus
        $this->hitungUlangSaldo();

        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }
    
    private function hitungUlangSaldo()
    {
        $saldo = 0;
        $all = Harian::orderBy('id', 'asc')->get();
        foreach ($all as $row) {
            $saldo = $saldo + $row->kredit - $row->debit;
            $row->update(['saldo' => $saldo]);
        }
    }
    
    public function bulkDelete(Request $request)
{
    $ids = $request->input('ids', []);
    Harian::whereIn('id', $ids)->delete();
    // hitung ulang saldo dari awal
    $all = Harian::orderBy('tanggal','asc')->orderBy('id','asc')->get();
    $saldo = 0;
    foreach($all as $h){
        $saldo += $h->kredit - $h->debit;
        $h->updateQuietly(['saldo' => $saldo]);
    }
    return back()->with('success', count($ids).' transaksi dihapus!');
}

public function exportPdf()
{
    $harians = Harian::orderBy('tanggal','asc')->orderBy('id','desc')->get();
    $totalKredit = $harians->sum('kredit');
    $totalDebit = $harians->sum('debit');
    $saldoAkhir = $harians->first()?->saldo ?? 0;
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.harian', compact('harians','totalKredit','totalDebit','saldoAkhir'));
    return $pdf->download('laporan-harian-'.date('Y-m-d').'.pdf');
}
}
