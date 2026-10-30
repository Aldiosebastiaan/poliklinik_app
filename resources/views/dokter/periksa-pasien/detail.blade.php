<x-layouts.app title="Detail Pemeriksaan">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-slate-800">Detail Pemeriksaan</h2>
        <div class="avatar">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-primary-content">
                <span class="text-sm font-semibold leading-none">
                    {{ strtoupper(substr(auth()->user()->nama ?? 'U', 0, 1)) }}
                </span>
            </div>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-2xl bg-base-100 p-4 shadow-sm">
            <h5 class="mb-2 font-semibold text-slate-700">Poli</h5>
            <p class="text-slate-800">{{ $periksa->daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? '—' }}</p>
        </div>
        <div class="rounded-2xl bg-base-100 p-4 shadow-sm">
            <h5 class="mb-2 font-semibold text-slate-700">No. Antrian</h5>
            <p class="text-slate-800">{{ $periksa->daftarPoli->no_antrian ?? '—' }}</p>
        </div>
        <div class="rounded-2xl bg-base-100 p-4 shadow-sm">
            <h5 class="mb-2 font-semibold text-slate-700">Pasien</h5>
            <p class="text-slate-800">{{ $periksa->daftarPoli->pasien->nama ?? '—' }}</p>
        </div>
        <div class="rounded-2xl bg-base-100 p-4 shadow-sm">
            <h5 class="mb-2 font-semibold text-slate-700">Dokter</h5>
            <p class="text-slate-800">{{ $periksa->daftarPoli->jadwalPeriksa->dokter->nama ?? '—' }}</p>
        </div>
        <div class="rounded-2xl bg-base-100 p-4 shadow-sm">
            <h5 class="mb-2 font-semibold text-slate-700">Tanggal Periksa</h5>
            <p class="text-slate-800">{{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d F Y') }}</p>
        </div>
        <div class="rounded-2xl bg-base-100 p-4 shadow-sm">
            <h5 class="mb-2 font-semibold text-slate-700">Keluhan</h5>
            <p class="whitespace-pre-line text-slate-800">{{ $periksa->daftarPoli->keluhan ?: 'Tidak ada keluhan.' }}</p>
        </div>
    </div>

    <div class="card rounded-2xl border border-slate-200 bg-base-100 shadow-md">
        <div class="card-body">
            <h5 class="font-semibold">Catatan Periksa</h5>
            <p class="mb-4 whitespace-pre-line text-slate-700">{{ $periksa->catatan ?: 'Tidak ada catatan.' }}</p>

            <h6 class="mt-4 font-semibold">Obat yang Diresepkan</h6>
            <ul class="list-inside list-disc text-slate-700">
                @forelse ($periksa->detailPeriksas as $detailObat)
                    <li>{{ $detailObat->obat->nama_obat ?? 'Obat tidak tersedia' }} — Rp {{ number_format($detailObat->obat->harga ?? 0, 0, ',', '.') }}</li>
                @empty
                    <li>Tidak ada obat yang diresepkan.</li>
                @endforelse
            </ul>

            <p class="mt-4"><strong>Total Biaya:</strong> Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('periksa-pasien.index') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-slate-100 px-4 py-2 font-medium text-slate-700 transition hover:bg-slate-200">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
            Kembali ke Daftar Pasien
        </a>
    </div>
</x-layouts.app>
