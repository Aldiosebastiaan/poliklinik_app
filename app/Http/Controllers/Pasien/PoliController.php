<?php

namespace App\Http\Controllers\Pasien;

use App\Events\QueueUpdated;
use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Poli;
use App\Notifications\PendaftaranPoliBerhasil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PoliController extends Controller
{
    /**
     * Hitung nomor antrian yang sedang dilayani pada suatu jadwal periksa.
     * Mengambil nomor antrian terkecil yang belum selesai diperiksa.
     * Jika semua sudah diperiksa, mengambil nomor terakhir yang sudah diperiksa.
     */
    private function getNomorSedangDilayani($jadwalId): int
    {
        $unexamined = DaftarPoli::where('id_jadwal', $jadwalId)
            ->whereDoesntHave('periksas')
            ->orderBy('no_antrian', 'asc')
            ->value('no_antrian');

        if ($unexamined !== null) {
            return (int) $unexamined;
        }

        $lastExamined = DaftarPoli::where('id_jadwal', $jadwalId)
            ->whereHas('periksas')
            ->max('no_antrian');

        return $lastExamined !== null ? (int) $lastExamined : 0;
    }

    /**
     * Tampilkan dashboard pasien beserta banner antrian aktif dan daftar jadwal dokter.
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Cari antrian aktif milik pasien yang sedang login (belum ada periksa)
        $antrianAktif = DaftarPoli::with(['jadwalPeriksa.dokter.poli'])
            ->where('id_pasien', $user->id)
            ->whereDoesntHave('periksas')
            ->latest('id')
            ->first();

        $sedangDilayaniAktif = 0;
        if ($antrianAktif && $antrianAktif->jadwalPeriksa) {
            $sedangDilayaniAktif = $this->getNomorSedangDilayani($antrianAktif->id_jadwal);
        }

        // Ambil semua jadwal periksa beserta relasi dokter dan poli
        $jadwals = JadwalPeriksa::with(['dokter.poli'])->get();

        $jadwals = $jadwals->map(function ($jadwal) {
            $jadwal->sedang_dilayani = $this->getNomorSedangDilayani($jadwal->id);

            return $jadwal;
        });

        // Persiapan channel untuk Laravel Echo
        $channels = array_keys(
            JadwalPeriksa::select('id')->get()->mapWithKeys(function ($j) {
                return [$j->id => 'queue.'.$j->id];
            })->toArray()
        );

        return view('pasien.dashboard', compact('user', 'antrianAktif', 'sedangDilayaniAktif', 'jadwals', 'channels'));
    }

    /**
     * Halaman riwayat pendaftaran pasien.
     */
    public function riwayat()
    {
        $user = Auth::user();

        // Ambil daftar pendaftaran pasien yang login, diurutkan terbaru
        // Gunakan load() secara manual untuk menghindari error null relationship
        $riwayat = DaftarPoli::where('id_pasien', $user->id)->latest('id')->get();

        // Memuat hubungan secara manual untuk menghindari error
        foreach ($riwayat as $dp) {
            $dp->load('jadwalPeriksa');
            $dp->load('periksas');
        }

        return view('pasien.riwayat', compact('user', 'riwayat'));
    }

    /**
     * Tampilkan detail pemeriksaan milik pasien yang sedang login.
     */
    public function riwayatDetail(int $id)
    {
        $user = Auth::user();

        $daftarPoli = DaftarPoli::query()
            ->where('id_pasien', $user->id)
            ->with('jadwalPeriksa.dokter.poli')
            ->findOrFail($id);

        $periksa = $daftarPoli->periksas()
            ->with('detailPeriksas.obat')
            ->latest('id')
            ->firstOrFail();

        return view('pasien.riwayat.show', compact('daftarPoli', 'periksa'));
    }

    /**
     * API endpoint untuk live update status nomor antrian tanpa reload halaman.
     */
    public function antrianStatus()
    {
        $user = Auth::user();

        $antrianAktif = DaftarPoli::where('id_pasien', $user->id)
            ->whereDoesntHave('periksas')
            ->latest('id')
            ->first();

        $activeData = null;
        if ($antrianAktif) {
            $activeData = [
                'id' => $antrianAktif->id,
                'no_antrian' => $antrianAktif->no_antrian,
                'sedang_dilayani' => $this->getNomorSedangDilayani($antrianAktif->id_jadwal),
            ];
        }

        $jadwalStatus = JadwalPeriksa::select('id')->get()->mapWithKeys(function ($j) {
            return [$j->id => $this->getNomorSedangDilayani($j->id)];
        });

        // Broadcast queue status update for Laravel Echo
        broadcast(new QueueUpdated($jadwalStatus ? array_keys($jadwalStatus)[0] ?? 0 : 0,
            $activeData ? $activeData['sedang_dilayani'] : 0,
            'called'));

        return response()->json([
            'antrian_aktif' => $activeData,
            'jadwal_status' => $jadwalStatus,
        ]);
    }

    /**
     * Halaman form pendaftaran poli.
     */
    public function get()
    {
        $user = Auth::user();
        $polis = Poli::all();
        $jadwal = JadwalPeriksa::with('dokter', 'dokter.poli')->get();

        // Cek apakah pasien masih memiliki antrian aktif
        $antrianAktif = DaftarPoli::with(['jadwalPeriksa.dokter.poli'])
            ->where('id_pasien', $user->id)
            ->whereDoesntHave('periksas')
            ->latest('id')
            ->first();

        return view('pasien.daftar', [
            'user' => $user,
            'polis' => $polis,
            'jadwals' => $jadwal,
            'antrianAktif' => $antrianAktif,
        ]);
    }

    /**
     * Simpan pendaftaran poli dengan validasi ketat.
     */
    public function submit(Request $request)
    {
        $request->validate([
            'id_poli' => 'required|exists:poli,id',
            'id_jadwal' => 'required|exists:jadwal_periksa,id',
            'keluhan' => 'nullable|string',
            'id_pasien' => 'required|exists:users,id',
        ]);

        // Ketentuan: Pasien tidak dapat mendaftar ke dua poli sekaligus.
        // Hanya dapat mendaftar kembali setelah pemeriksaan selesai.
        $cekAntrianAktif = DaftarPoli::with('jadwalPeriksa.dokter.poli')
            ->where('id_pasien', $request->user()->id)
            ->whereDoesntHave('periksas')
            ->first();

        if ($cekAntrianAktif) {
            $poliName = $cekAntrianAktif->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poli';

            return redirect()
                ->back()
                ->with('error', "Anda masih memiliki antrian aktif di {$poliName} (Nomor Antrian: #{$cekAntrianAktif->no_antrian}). Anda hanya dapat mendaftar kembali setelah proses pemeriksaan selesai.")
                ->withInput();
        }

        $jumlahSudahDaftar = DaftarPoli::where('id_jadwal', $request->id_jadwal)->count();

        $pendaftaran = DaftarPoli::create([
            'id_pasien' => $request->user()->id,
            'id_jadwal' => $request->id_jadwal,
            'keluhan' => $request->keluhan,
            'no_antrian' => $jumlahSudahDaftar + 1,
        ]);

        $request->user()->notify(new PendaftaranPoliBerhasil($pendaftaran->no_antrian));

        // Broadcast queue update to pasien
        $user = Auth::check() ? Auth::user() : null;
        $poli = $user && $user->role === 'dokter' ? null : ($user ? $user->poli : null);
        if ($poli) {
            $jadwal = DB::table('jadwal_periksa')->where('id_dokter', Auth::id())->latest()->first();
            if ($jadwal) {
                event(new QueueUpdated($jadwal->id, $jumlahSudahDaftar + 1));
            }
        }

        return redirect()
            ->route('pasien.dashboard')
            ->with('message', 'Berhasil Mendaftar ke Poli! Nomor antrian Anda telah terbit.')
            ->with('type', 'success');
    }
}
