<x-layouts.app title="Riwayat Pendaftaran Saya">


<!-- Page Header -->
<div class="bg-base-100 shadow-xl rounded-2xl p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold">Riwayat Pendaftaran Saya</h2>
            <p class="text-base-content/60">Riwayat pendaftaran poli Anda</p>
        </div>
        <div class="avatar">
            <div class="w-10 h-10 rounded-full bg-primary text-primary-content flex items-center justify-center">
                <span class="text-sm font-semibold leading-none">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Daftar -->
@if($riwayat->isNotEmpty())
    <div class="bg-base-100 rounded-2xl p-6 shadow-sm">
        <h3 class="font-semibold mb-4">Riwayat Pendaftaran</h3>
        <div class="space-y-3">
            @foreach($riwayat as $index => $daftarPoli)
                <div class="p-4 rounded-lg border-l-4 {{ $daftarPoli->periksas->last() ? 'border-green-500' : 'border-yellow-500' }}">
                    <div class="flex items-between">
                        <div>
                            <p class="font-medium">{{ $daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poli' }}</p>
                            <p class="text-xs text-base-content/60">
                                {{ \Illuminate\Support\Carbon::parse($daftarPoli->jadwalPeriksa->hari)->format('d F Y') }} -
                                Jam: {{ date('H:i', strtotime($daftarPoli->jadwalPeriksa->jam_mulai)) }} - {{ date('H:i', strtotime($daftarPoli->jadwalPeriksa->jam_selesai)) }}
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded {{ $daftarPoli->periksas->last() && $daftarPoli->periksas->last()->status_akhir == 'lunas' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $daftarPoli->no_antrian }}
                        </span>
                    </div>
                    <p class="text-xs text-base-content/50 mt-1">
                        Antrian #{{ $daftarPoli->no_antrian }} - <strong>Status:</strong> {{ $daftarPoli->periksas->last() ? 'Sudah diperiksa' : 'Belum diperiksa' }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="bg-base-100 rounded-2xl p-6 text-center text-base-content/40">
        <i class="fas fa-folder text-4xl mb-3"></i>
        <h3>Belum Ada Riwayat</h3>
        <p>Anda belum memiliki riwayat pendaftaran poli. Daftar poli terbaru untuk memulai.</p>
    </div>
@endif

<!-- Tombol ke Detail Pemeriksaan -->
@if($riwayat->isNotEmpty())
    <div class="mt-6 bg-base-100 rounded-2xl p-6 shadow-sm">
        <h3 class="font-semibold mb-3">Detail Pemeriksaan</h3>
        <p class="text-base-content/60 mb-4">Klik detail di bawah untuk melihat catatan dokter, obat, dan total biaya pemeriksaan.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($riwayat as $daftarPoli)
                @if($daftarPoli->periksas->last())
                    <div>
                        <a href="{{ route('pasien.riwayat-pasien.show', $daftarPoli->id) }}"
                           class="btn btn-primary w-full mb-2">
                            <i class="fas fa-eye me-2"></i>
                            Detail Pemeriksaan (ID: {{ $daftarPoli->id }})
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endif


</x-layouts.app>
