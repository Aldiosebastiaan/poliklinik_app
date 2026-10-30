<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TableExport;
use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ObatController extends Controller
{
    public function index()
    {
        $obats = Obat::all();

        return view('admin.obat.index', compact('obats'));
    }

    public function export()
    {
        $rows = Obat::orderBy('nama_obat')->get()
            ->map(fn (Obat $obat): array => [
                $obat->nama_obat,
                $obat->kemasan,
                $obat->stok,
                $obat->harga,
            ])->all();

        return Excel::download(new TableExport($rows, ['Nama Obat', 'Kemasan', 'Stok', 'Harga (Rp)']), 'data-obat.xlsx');
    }

    public function create()
    {
        return view('admin.obat.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_obat' => ['required', 'string', 'max:255', Rule::unique('obat', 'nama_obat')],
            'kemasan' => 'required|string|max:35',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
        ], [
            'nama_obat.unique' => 'Obat sudah terdaftar.',
        ]);

        Obat::create($validated);

        return redirect()->route('obat.index')->with('success', 'Data obat berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $obat = Obat::findOrFail($id);

        return redirect()->route('obat.edit', $obat->id);
    }

    public function edit(string $id)
    {
        $obat = Obat::findOrFail($id);

        return view('admin.obat.edit', compact('obat'));
    }

    public function update(Request $request, string $id)
    {
        $obat = Obat::findOrFail($id);

        $validated = $request->validate([
            'nama_obat' => ['required', 'string', 'max:255', Rule::unique('obat', 'nama_obat')->ignore($obat->id)],
            'kemasan' => 'required|string|max:35',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
        ], [
            'nama_obat.unique' => 'Obat sudah terdaftar.',
        ]);

        $obat->update($validated);

        return redirect()->route('obat.index')->with('success', 'Data obat berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        $obat = Obat::findOrFail($id);
        $obat->delete();

        return redirect()->route('obat.index')->with('success', 'Data obat berhasil dihapus');
    }
}
