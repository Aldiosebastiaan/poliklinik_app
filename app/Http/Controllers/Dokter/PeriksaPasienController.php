<?php

namespace App\Http\Controllers\Dokter;

use App\Events\QueueUpdated;
use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\DetailPeriksa;
use App\Models\Obat;
use App\Models\Periksa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PeriksaPasienController extends Controller
{
    private const BIAYA_KONSULTASI = 150000;

    public function index()
    {
        $dokterId = Auth::id();

        $daftarPasien = DaftarPoli::with(['pasien', 'jadwalPeriksa', 'periksas'])
            ->whereHas('jadwalPeriksa', function ($query) use ($dokterId) {
                $query->where('id_dokter', $dokterId);
            })
            ->orderBy('no_antrian')
            ->get();

        return view('dokter.periksa-pasien.index', compact('daftarPasien'));
    }

    public function create(int $id): View
    {
        $doctorId = Auth::id();

        $daftarPoli = DaftarPoli::query()
            ->with(['pasien', 'jadwalPeriksa.dokter.poli'])
            ->whereHas('jadwalPeriksa', function (Builder $query) use ($doctorId): void {
                $query->where('id_dokter', $doctorId);
            })
            ->whereDoesntHave('periksas')
            ->findOrFail($id);

        $obats = Obat::query()->orderBy('nama_obat')->get();
        $biayaKonsultasi = self::BIAYA_KONSULTASI;

        return view('dokter.periksa-pasien.create', compact('daftarPoli', 'obats', 'biayaKonsultasi'));
    }

    public function call(int $id): RedirectResponse
    {
        $doctorId = Auth::id();

        $daftarPoli = DaftarPoli::query()
            ->with('jadwalPeriksa')
            ->whereHas('jadwalPeriksa', function (Builder $query) use ($doctorId): void {
                $query->where('id_dokter', $doctorId);
            })
            ->whereDoesntHave('periksas')
            ->findOrFail($id);

        $jadwalPeriksa = $daftarPoli->jadwalPeriksa;
        $jadwalPeriksa->forceFill([
            'sedang_dilayani' => $daftarPoli->no_antrian,
        ])->save();

        event(new QueueUpdated($jadwalPeriksa->id, (string) $daftarPoli->no_antrian, 'called'));

        return to_route('periksa-pasien.create', $daftarPoli->id);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_daftar_poli' => 'required|integer|exists:daftar_poli,id',
            'obat_json' => 'required|json',
            'keluhan' => 'required|string',
            'catatan' => 'nullable|string',
            'biaya_periksa' => 'required|integer',
        ]);

        $requestedObatIds = json_decode($validated['obat_json'], true);
        if (! is_array($requestedObatIds) || $requestedObatIds === []) {
            return back()->withErrors(['obat_json' => 'Pilih minimal satu obat sebelum menyimpan pemeriksaan.'])->withInput();
        }

        $obatIds = [];
        foreach ($requestedObatIds as $requestedObatId) {
            if ((! is_int($requestedObatId) && (! is_string($requestedObatId) || ! ctype_digit($requestedObatId))) || (int) $requestedObatId < 1) {
                return back()->withErrors(['obat_json' => 'Pilihan obat tidak valid. Silakan pilih ulang obat.'])->withInput();
            }

            $obatIds[] = (int) $requestedObatId;
        }

        if (count($obatIds) !== count(array_unique($obatIds))) {
            return back()->withErrors(['obat_json' => 'Obat yang sama tidak dapat dipilih lebih dari sekali.'])->withInput();
        }

        $doctorId = Auth::id();
        $daftarPoli = DaftarPoli::query()
            ->whereHas('jadwalPeriksa', function (Builder $query) use ($doctorId): void {
                $query->where('id_dokter', $doctorId);
            })
            ->whereDoesntHave('periksas')
            ->findOrFail($validated['id_daftar_poli']);

        $obatsTerpilih = Obat::query()->whereIn('id', $obatIds)->get();
        if ($obatsTerpilih->count() !== count($obatIds)) {
            return back()->withErrors(['obat_json' => 'Salah satu obat yang dipilih sudah tidak tersedia. Silakan pilih ulang obat.'])->withInput();
        }

        $biayaPeriksa = self::BIAYA_KONSULTASI + (int) $obatsTerpilih->sum('harga');

        DB::transaction(function () use ($daftarPoli, $validated, $obatsTerpilih, $biayaPeriksa): void {
            // Update keluhan pada DaftarPoli record
            $daftarPoli->keluhan = $validated['keluhan'];
            $daftarPoli->save();

            $periksa = Periksa::create([
                'id_daftar_poli' => $daftarPoli->id,
                'tgl_periksa' => now(),
                'catatan' => $validated['catatan'] ?? null,
                'biaya_periksa' => $biayaPeriksa,
                'keluhan' => $validated['keluhan'],
            ]);

            foreach ($obatsTerpilih as $obat) {
                DetailPeriksa::create([
                    'id_periksa' => $periksa->id,
                    'id_obat' => $obat->id,
                ]);
            }
        });

        return redirect()
            ->route('periksa-pasien.index')
            ->with('message', 'Data pemeriksaan berhasil disimpan.')
            ->with('type', 'success');
    }

    public function detail(int $id): View
    {
        $doctorId = Auth::id();

        $periksa = Periksa::query()
            ->whereHas('daftarPoli', function (Builder $query) use ($doctorId): void {
                $query->whereHas('jadwalPeriksa', function (Builder $query) use ($doctorId): void {
                    $query->where('id_dokter', $doctorId);
                });
            })
            ->with(['daftarPoli.pasien', 'daftarPoli.jadwalPeriksa.dokter.poli', 'detailPeriksas.obat'])
            ->findOrFail($id);

        return view('dokter.periksa-pasien.detail', compact('periksa'));
    }
}
