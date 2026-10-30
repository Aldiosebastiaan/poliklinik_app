<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Poli;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPoliController extends Controller
{
    public function index()
    {
        $polis = Poli::all();

        return view('admin.polis.index', compact('polis'));
    }

    public function create()
    {
        return view('admin.polis.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_poli' => 'required',
            'keterangan' => 'nullable',
        ]);

        Poli::create($validated);

        return redirect()->route('polis.index')->with('success', 'Poli berhasil di tambahkan')->with('type', 'success');
    }

    public function edit(string $id)
    {
        $poli = Poli::findOrFail($id);

        return view('admin.polis.edit', compact('poli'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nama_poli' => 'required',
            'keterangan' => 'nullable',
        ]);

        $poli = Poli::findOrFail($id);
        $poli->update($validated);

        return redirect()->route('polis.index')->with('success', 'Polis berhasil di update');
    }

    public function destroy(string $id)
    {
        $poli = Poli::findOrFail($id);
        $poli->delete();

        return redirect()->route('polis.index')->with('success', 'Polis Berhasil di hapus !');
    }

    public function dashboard(): View
    {
        $jumlahMenunggu = DaftarPoli::query()->whereDoesntHave('periksas')->count();

        $jadwals = JadwalPeriksa::query()
            ->with('dokter.poli')
            ->withCount([
                'daftarPolis as jumlah_pendaftaran',
                'daftarPolis as jumlah_menunggu' => function (Builder $query): void {
                    $query->whereDoesntHave('periksas');
                },
            ])
            ->latest('id')
            ->get();

        $channels = $jadwals->pluck('id')->all();

        return view('admin.dashboard', compact(
            'jumlahMenunggu',
            'jadwals',
            'channels'
        ));
    }
}
