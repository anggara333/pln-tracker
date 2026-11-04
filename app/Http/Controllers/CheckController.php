<?php

namespace App\Http\Controllers;

use App\Models\Check;
use Illuminate\Http\Request;

class CheckController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $checks = Check::orderBy('tanggal','desc')->get();
        $totalKwh = $checks->sum('selisih');
        $totalBayar = $checks->sum('pakai');
        $rataKwh = $checks->avg('selisih') ?? 0;
        $lastAkhir = $checks->first()->akhir ?? '';
        $yesterday = now()->subDay()->format('Y-m-d');
        
        return view('check.index', compact('checks','totalKwh','totalBayar','rataKwh','lastAkhir','yesterday'));
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

        Check::create($request->only(['tanggal', 'awal', 'akhir', 'harga']));

        return redirect()->route('check.index')->with('success', 'Data berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Check $check)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Check $check)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Check $check)
    {
        //
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
        $total = 0;
        $jumlah = 0;

        foreach ($checks as $c) {
            $total += $c->selisih;
            $jumlah += $c->pakai;
            $c->updateQuietly([
                'total' => $total,
                'jumlah' => $jumlah
            ]);
        }
    }
}

