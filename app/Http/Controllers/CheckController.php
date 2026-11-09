<?php

namespace App\Http\Controllers;

use App\Models\Check;
use App\Models\Harian;
use Illuminate\Http\Request;

class CheckController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $checks = Check::orderBy('tanggal', 'desc')->get();
        $totalKwh = $checks->sum('selisih');
        $totalBayar = $checks->sum('pakai');
        $rataKwh = $checks->count() ? $totalKwh / $checks->count() : 0;

        $harians = Harian::orderBy('tanggal', 'desc')->orderBy('id', 'desc')->get();
        $totalKredit = $harians->sum('kredit');
        $totalDebit = $harians->sum('debit');
        $saldoAkhir = $harians->first()?->saldo ?? 0;

        return view('check.index', compact('checks', 'totalKwh', 'totalBayar', 'rataKwh', 'harians', 'totalKredit', 'totalDebit', 'saldoAkhir'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $last = Check::orderBy('tanggal', 'desc')->first();

        return view('checks.create', [
            'awal' => $last->akhir ?? 4190, // lanjut dari akhir terakhir (punya lu 4190)
            'tanggal' => now()->format('Y-m-d'),
            'harga' => 1487.20
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|unique:checks,tanggal',
            'awal' => 'required|numeric',
            'akhir' => 'required|numeric|gt:awal',
            'harga' => 'required|numeric',
        ], [
            'akhir.gt' => 'Meter akhir harus lebih besar dari awal!',
            'tanggal.unique' => 'Tanggal ini sudah diinput!'
        ]);

        $selisih = $request->akhir - $request->awal;
        $pakai = $selisih * $request->harga;

        Check::create([
            'tanggal' => $request->tanggal,
            'awal' => $request->awal,
            'akhir' => $request->akhir,
            'harga' => $request->harga,
            'selisih' => $selisih,
            'pakai' => $pakai,
        ]);

        return redirect()->route('check.index')->with('success', 'Data berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Check $check)
    {
        return view('checks.show', compact('check'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Check $check)
    {
        return view('checks.edit', compact('check'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Check $check)
    {
        $request->validate([
            'tanggal' => 'required|date|unique:checks,tanggal,' . $check->id,
            'awal' => 'required|numeric',
            'akhir' => 'required|numeric|gt:awal',
            'harga' => 'required|numeric',
        ], [
            'akhir.gt' => 'Meter akhir harus lebih besar dari awal!',
            'tanggal.unique' => 'Tanggal ini sudah diinput!'
        ]);

        $selisih = $request->akhir - $request->awal;
        $pakai = $selisih * $request->harga;

        $check->update([
            'tanggal' => $request->tanggal,
            'awal' => $request->awal,
            'akhir' => $request->akhir,
            'harga' => $request->harga,
            'selisih' => $selisih,
            'pakai' => $pakai,
        ]);

        $this->recalculate();

        return redirect()->route('check.index')->with('success', 'Data berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Check $check)
    {
        $check->delete();

        $this->recalculate();

        return back()->with('success', 'Data dihapus & total dihitung ulang');
    }

    private function recalculate()
    {
        $checks = Check::orderBy('tanggal', 'asc')->get();

        foreach ($checks as $c) {
            $selisih = $c->akhir - $c->awal;
            $pakai = $selisih * $c->harga;
            $c->updateQuietly([
                'selisih' => $selisih,
                'pakai' => $pakai,
            ]);
        }
    }
    
    public function bulkDelete(Request $request)
{
    $ids = $request->input('ids', []);
    Check::whereIn('id', $ids)->delete();
    $this->recalculate();
    return back()->with('success', count($ids).' data PLN dihapus!');
}

public function exportPdf()
{
    $checks = Check::orderBy('tanggal','desc')->get();
    $totalKwh = $checks->sum('selisih');
    $totalBayar = $checks->sum('pakai');
    $rataKwh = $checks->count() ? $totalKwh / $checks->count() : 0;

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.pln', compact('checks','totalKwh','totalBayar','rataKwh'));
    return $pdf->download('laporan-pln-'.date('Y-m-d').'.pdf');
}
}
