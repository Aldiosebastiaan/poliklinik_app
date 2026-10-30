<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $doctorId = Auth::id();

        $doctorRegistrations = DaftarPoli::query()->whereHas(
            'jadwalPeriksa',
            function (Builder $query) use ($doctorId): void {
                $query->where('id_dokter', $doctorId);
            }
        );

        $jumlahJadwal = JadwalPeriksa::query()->where('id_dokter', $doctorId)->count();
        $jumlahPendaftaran = (clone $doctorRegistrations)->count();
        $jumlahMenunggu = (clone $doctorRegistrations)->whereDoesntHave('periksas')->count();
        $jumlahSelesai = $jumlahPendaftaran - $jumlahMenunggu;

        $daftarPasien = (clone $doctorRegistrations)
            ->with(['pasien', 'jadwalPeriksa', 'periksas'])
            ->latest('id')
            ->limit(100)
            ->get();

        $jadwalPraktik = JadwalPeriksa::query()
            ->where('id_dokter', $doctorId)
            ->withCount([
                'daftarPolis as jumlah_pendaftaran',
                'daftarPolis as jumlah_menunggu' => function (Builder $query): void {
                    $query->whereDoesntHave('periksas');
                },
            ])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        $antrianAktif = JadwalPeriksa::query()
            ->with('dokter.poli')
            ->withCount([
                'daftarPolis as jumlah_menunggu' => function (Builder $query): void {
                    $query->whereDoesntHave('periksas');
                },
            ])
            ->whereNotNull('sedang_dilayani')
            ->where('sedang_dilayani', '>', 0)
            ->where('id_dokter', $doctorId)
            ->latest('updated_at')
            ->first();

        $channels = JadwalPeriksa::query()->where('id_dokter', $doctorId)->pluck('id')->all();

        return view('dokter.dashboard', compact(
            'jumlahJadwal',
            'jumlahPendaftaran',
            'jumlahMenunggu',
            'jumlahSelesai',
            'daftarPasien',
            'jadwalPraktik',
            'antrianAktif',
            'channels'
        ));
    }
}
