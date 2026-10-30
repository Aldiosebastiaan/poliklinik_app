<?php

namespace App\Http\Controllers\Dokter;

use App\Exports\TableExport;
use App\Http\Controllers\Controller;
use App\Models\Periksa;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class RiwayatPasienController extends Controller
{
    public function index()
    {
        $riwayatPasien = Periksa::whereHas('daftarPoli.jadwalPeriksa', function ($query) {
            $query->where('id_dokter', Auth::id());
        })->with([
            'daftarPoli.pasien',
            'daftarPoli.jadwalPeriksa.dokter',
            'detailPeriksas.obat',
        ])->orderBy('tgl_periksa', 'desc')->get();

        return view('dokter.riwayat-pasien.index', compact('riwayatPasien'));
    }

    public function export()
    {
        $riwayatPasien = Periksa::whereHas('daftarPoli.jadwalPeriksa', function ($query) {
            $query->where('id_dokter', Auth::id());
        })->with(['daftarPoli.pasien', 'detailPeriksas.obat'])
            ->orderByDesc('tgl_periksa')
            ->get();

        $rows = $riwayatPasien->map(fn (Periksa $periksa): array => [
            $periksa->daftarPoli?->no_antrian,
            $periksa->daftarPoli?->pasien?->nama,
            $periksa->daftarPoli?->keluhan,
            $periksa->tgl_periksa,
            $periksa->catatan,
            $periksa->detailPeriksas->pluck('obat.nama_obat')->filter()->implode(', '),
            $periksa->biaya_periksa,
        ])->all();

        return Excel::download(new TableExport($rows, ['No. Antrian', 'Nama Pasien', 'Keluhan', 'Tanggal Periksa', 'Catatan', 'Obat', 'Biaya (Rp)']), 'riwayat-pasien.xlsx');
    }

    public function show($id)
    {
        $periksa = Periksa::whereHas('daftarPoli.jadwalPeriksa', function ($query) {
            $query->where('id_dokter', Auth::id());
        })->with([
            'daftarPoli.pasien',
            'daftarPoli.jadwalPeriksa.dokter.poli',
            'detailPeriksas.obat',
        ])->findOrFail($id);

        return view('dokter.riwayat-pasien.show', compact('periksa'));
    }
}
