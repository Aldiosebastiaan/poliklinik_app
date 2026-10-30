<x-layouts.app title="Detail Pemeriksaan">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pasien.riwayat') }}" class="btn btn-ghost btn-sm">
            <i class="fas fa-arrow-left me-2"></i>
            Kembali ke Riwayat
        </a>
        <h2 class="text-2xl font-bold">Detail Pemeriksaan</h2>
    </div>

    <div class="space-y-5">
        <section class="bg-base-100 rounded-2xl p-6 shadow-sm">
            <h3 class="font-semibold mb-4">Informasi Pendaftaran</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm text-base-content/60">Poli</dt>
                    <dd class="font-medium">{{ $daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poli' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-base-content/60">Dokter</dt>
                    <dd class="font-medium">{{ $daftarPoli->jadwalPeriksa->dokter->nama ?? 'Dokter' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-base-content/60">Nomor antrean</dt>
                    <dd class="font-medium">{{ $daftarPoli->no_antrian }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-base-content/60">Tanggal pemeriksaan</dt>
                    <dd class="font-medium">{{ $periksa->tgl_periksa }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-sm text-base-content/60">Keluhan</dt>
                    <dd class="font-medium">{{ $daftarPoli->keluhan ?: '—' }}</dd>
                </div>
            </dl>
        </section>

        <section class="bg-base-100 rounded-2xl p-6 shadow-sm">
            <h3 class="font-semibold mb-3">Catatan Dokter</h3>
            <p class="text-base-content/70">{{ $periksa->catatan ?: 'Tidak ada catatan.' }}</p>
        </section>

        <section class="bg-base-100 rounded-2xl p-6 shadow-sm">
            <h3 class="font-semibold mb-4">Obat yang Diresepkan</h3>
            @if($periksa->detailPeriksas->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Obat</th>
                                <th class="text-right">Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($periksa->detailPeriksas as $detail)
                                <tr>
                                    <td>{{ $detail->obat->nama_obat ?? 'Obat tidak tersedia' }}</td>
                                    <td class="text-right">Rp {{ number_format($detail->obat->harga ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-base-content/60">Tidak ada obat yang diresepkan.</p>
            @endif
        </section>

        <section class="bg-base-100 rounded-2xl p-6 shadow-sm flex items-center justify-between">
            <h3 class="font-semibold">Total biaya pemeriksaan</h3>
            <p class="text-xl font-bold">Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}</p>
        </section>
    </div>
</x-layouts.app>
