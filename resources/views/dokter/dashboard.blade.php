<x-layouts.app title="Dashboard Dokter">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Selamat datang, dr. {{ auth()->user()->nama ?? 'Dokter' }}
            </h1>
            <p class="text-sm text-slate-500 mt-1">Ringkasan jadwal dan aktivitas pasien Anda.</p>
        </div>
        <div class="avatar">
            <div class="w-10 h-10 rounded-full bg-primary text-primary-content flex items-center justify-center">
                <span class="text-sm font-semibold leading-none">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Stats Section --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        {{-- Jadwal praktik --}}
        <div class="bg-base-100 rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3">
                <div class="icon-box">
                    <i class="fas fa-calendar-check text-2xl text-indigo-600"></i>
                </div>
                <div>
                    <p class="text-sm text-base-content/60">Jadwal praktik</p>
                    <p class="text-3xl font-bold">{{ $jumlahJadwal }}</p>
                </div>
            </div>
        </div>

        {{-- Total pendaftaran --}}
        <div class="bg-base-100 rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3">
                <div class="icon-box">
                    <i class="fas fa-users text-2xl text-sky-600"></i>
                </div>
                <div>
                    <p class="text-sm text-base-content/60">Total pendaftaran</p>
                    <p class="text-3xl font-bold">{{ $jumlahPendaftaran }}</p>
                </div>
            </div>
        </div>

        {{-- Menunggu diperiksa --}}
        <div class="bg-amber-100 border-l-4 border-amber-500 text-amber-700 p-6 rounded-2xl shadow-sm">
            <div class="flex items-center gap-3">
                <div class="icon-box">
                    <i class="fas fa-hourglass-half text-2xl text-amber-500"></i>
                </div>
                <div>
                    <p class="text-sm text-amber-600">Menunggu diperiksa</p>
                    <p class="text-3xl font-bold">{{ $jumlahMenunggu }}</p>
                </div>
            </div>
        </div>

        {{-- Selesai diperiksa --}}
        <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-6 rounded-2xl shadow-sm">
            <div class="flex items-center gap-3">
                <div class="icon-box">
                    <i class="fas fa-circle-check text-2xl text-emerald-500"></i>
                </div>
                <div>
                    <p class="text-sm text-emerald-600">Selesai diperiksa</p>
                    <p class="text-3xl font-bold">{{ $jumlahSelesai }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Antrian Aktif Banner --}}
    @if($antrianAktif)
    {{-- Antrian Aktif --}}
    <div class="relative overflow-hidden rounded-3xl p-6 md:p-8 mb-8 text-white shadow-xl shadow-indigo-600/20"
         style="background: linear-gradient(135deg, #2563eb 0%, #4f46e5 50%, #4338ca 100%);">

        {{-- Background Decorative Rings --}}
        <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="absolute right-36 -top-12 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>

        <div class="relative z-10">

            {{-- Upper Title --}}
            <div class="text-[11px] md:text-xs font-bold tracking-widest uppercase text-blue-200 mb-5">
                ANTRIAN AKTIF
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">

                {{-- Left Side: Details --}}
                <div class="space-y-4">

                    {{-- Poliklinik --}}
                    <div>
                        <div class="text-[11px] text-blue-200 font-medium">Poliklinik</div>
                        <div class="text-2xl md:text-3xl font-black text-white leading-tight">
                            {{ $antrianAktif->dokter->poli->nama_poli ?? 'Poliklinik' }}
                        </div>
                    </div>

                    {{-- Dokter --}}
                    <div>
                        <div class="text-[11px] text-blue-200 font-medium">Dokter</div>
                        <div class="text-lg md:text-xl font-bold text-white">
                            Dr. {{ $antrianAktif->dokter->nama ?? '-' }}
                        </div>
                    </div>

                    {{-- Jadwal Periksa --}}
                    <div>
                        <div class="text-[11px] text-blue-200 font-medium">Jadwal Periksa</div>
                        <div class="text-sm md:text-base font-semibold text-blue-100">
                            {{ $antrianAktif->hari }} ({{ date('H:i', strtotime($antrianAktif->jam_mulai)) }} - {{ date('H:i', strtotime($antrianAktif->jam_selesai)) }})
                        </div>
                    </div>

                </div>

                {{-- Right Side: Queue Cards --}}
                <div class="flex items-center gap-3 md:gap-4 self-start lg:self-center">

                    {{-- Nomor Anda --}}
                    <div class="bg-white/15 backdrop-blur-md border border-white/25 rounded-2xl p-4 md:p-5 text-center min-w-[110px] md:min-w-[125px] shadow-inner">
                        <div class="text-[11px] md:text-xs font-medium text-blue-100 mb-1">
                            Sedang Dilayani
                        </div>
                        <div class="text-4xl md:text-5xl font-black text-white tracking-tight">
                            {{ $antrianAktif->sedang_dilayani }}
                        </div>
                    </div>

                    {{-- Sedang Dilayani --}}
                    <div class="bg-white rounded-2xl p-4 md:p-5 text-center min-w-[130px] md:min-w-[150px] shadow-2xl">
                        <div class="text-[10px] md:text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">
                            PASIEN MENUNGGU
                        </div>
                        <div id="live-banner-dilayani"
                             class="text-4xl md:text-5xl font-black text-indigo-700 tracking-tight transition-all duration-300">
                            {{ $antrianAktif->jumlah_menunggu }}
                        </div>
                        <div class="flex items-center justify-center gap-1.5 mt-2 text-[10px] font-bold text-emerald-600">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            <span>Live Update</span>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>
    @else
    <section class="bg-base-100 border border-slate-200 rounded-3xl shadow-sm p-6 md:p-8 mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Ringkasan Jadwal Praktik</h2>
                <p class="text-sm text-slate-500 mt-1">Jumlah pendaftaran dan pasien yang masih menunggu pada setiap jadwal.</p>
            </div>
            <a href="{{ route('jadwal-periksa.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i>
                Tambah Jadwal
            </a>
        </div>

        @if($jadwalPraktik->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($jadwalPraktik as $jadwal)
                    <article class="rounded-2xl border border-base-200 bg-base-100 p-5">
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div>
                                <h3 class="font-bold text-slate-800">{{ $jadwal->hari }}</h3>
                                <p class="text-sm text-slate-500 mt-1">
                                    {{ date('H:i', strtotime($jadwal->jam_mulai)) }}–{{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                                </p>
                            </div>
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                                <i class="fas fa-calendar-day"></i>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-500">Total daftar</p>
                                <p class="text-xl font-bold text-slate-800">{{ $jadwal->jumlah_pendaftaran }}</p>
                            </div>
                            <div class="rounded-xl bg-amber-50 p-3">
                                <p class="text-xs text-amber-700">Menunggu</p>
                                <p class="text-xl font-bold text-amber-800">{{ $jadwal->jumlah_menunggu }}</p>
                            </div>
                        </div>

                        <a href="{{ route('periksa-pasien.index') }}" class="btn btn-primary btn-sm text-white">
                            Buka daftar pasien
                            <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </article>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-base-300 bg-base-200/30 p-6 text-center">
                <i class="fas fa-calendar-plus text-2xl text-base-content/40 mb-3"></i>
                <h3 class="font-semibold text-slate-800">Belum ada jadwal praktik</h3>
                <p class="text-sm text-slate-500 mt-1 mb-4">Tambahkan jadwal agar pasien dapat mendaftar dan antrean tampil di sini.</p>
                <a href="{{ route('jadwal-periksa.create') }}" class="btn btn-primary btn-sm">Buat jadwal praktik</a>
            </div>
        @endif
    </section>
    @endif

    {{-- Jadwal Poliklinik & Daftar Pasien --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-xl font-bold text-slate-800">
                Daftar Pasien untuk Periksa
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
                Daftar pasien menunggu dan yang sedang dilayani
            </p>
        </div>

        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            Antrian Realtime
        </div>
    </div>

    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-0">

            <div class="overflow-x-auto">

                <table class="table w-full">

                    {{-- Head --}}
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Pasien</th>
                            <th class="px-6 py-4">Keluhan</th>
                            <th class="px-6 py-4">Hari</th>
                            <th class="px-6 py-4">Jam Periksa</th>
                            <th class="px-6 py-4 text-center">Antrean</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody class="text-sm text-slate-700">
                        @forelse($daftarPasien as $pendaftaran)
                        <tr class="border-t border-slate-100 hover:bg-slate-50 transition">

                            {{-- No --}}
                            <td class="px-6 py-4 text-slate-500">
                                {{ $loop->iteration }}
                            </td>

                            {{-- Pasien --}}
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                <span class="inline-flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    {{ $pendaftaran->pasien->nama ?? 'Pasien' }}
                                </span>
                            </td>

                            {{-- Keluhan --}}
                            <td class="px-6 py-4 text-slate-500">
                                {{ $pendaftaran->keluhan ?: '—' }}
                            </td>

                            {{-- Hari --}}
                            <td class="px-6 py-4 text-slate-600">
                                <span class="inline-flex items-center gap-1.5 font-medium">
                                    <i class="fas fa-calendar-day text-indigo-500 text-xs"></i>
                                    {{ $pendaftaran->jadwalPeriksa->hari ?? '-' }}
                                </span>
                            </td>

                            {{-- Jam Periksa --}}
                            <td class="px-6 py-4 text-slate-600">
                                <span class="font-medium text-slate-700">
                                    {{ date('H:i', strtotime($pendaftaran->jadwalPeriksa->jam_mulai)) }} - {{ date('H:i', strtotime($pendaftaran->jadwalPeriksa->jam_selesai)) }}
                                </span>
                                <span class="text-xs text-slate-400">WIB</span>
                            </td>

                            {{-- Antrean --}}
                            <td class="px-6 py-4 text-center font-bold">
                                {{ $pendaftaran->no_antrian }}
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 text-center">
                                @if($pendaftaran->periksas->isNotEmpty())
                                    <span class="badge badge-success badge-outline">Selesai</span>
                                @else
                                    <span class="badge badge-warning badge-outline">Menunggu</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                @if($pendaftaran->periksas->isNotEmpty())
                                    <a href="{{ route('riwayat-pasien.show', $pendaftaran->periksas->last()->id) }}" class="btn btn-sm btn-ghost text-primary">
                                        <i class="fas fa-eye"></i>
                                        Detail
                                    </a>
                                @else
                                    <a href="{{ route('periksa-pasien.create', $pendaftaran->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-stethoscope"></i>
                                        Periksa
                                    </a>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-3 block"></i>
                                Belum ada pasien yang menunggu pemeriksaan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>

            </div>

        </div>
    </div>


    {{-- ================= LIVE UPDATE SCRIPT (NO RELOAD) ================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusUrl = "{{ route('pasien.antrian.status') }}";

            // Laravel Echo setup for real-time updates
            <?php if (app()->environment('local') || app()->environment('testing')): ?>
            window.LaravelEcho = <?php echo json_encode([
                'broadcaster' => 'reverb',
                'key' => 'reverb-key', // Replace with actual Reverb key
                'wsUrl' => 'http://127.0.0.1:8000/reverb-websocket',
            ]); ?>
            <?php endif; ?>

            function initLaravelEcho() {
                if (typeof window.LaravelEcho === 'undefined') {
                    console.log('Laravel Echo not configured, using polling fallback');
                    return;
                }

                // Listen for queue updates per poli
                const channels = {{ json_encode($channels) }};

                channels.forEach(channelId => {
                    const channel = window.LaravelEcho.private('queue.' + channelId);
                    channel.here((users) => {
                        // User is in the channel
                    })
                    .joining((user) => {
                        // A user joined the channel
                    })
                    .leaving((user) => {
                        // A user left the channel
                    })
                    .listen('.QueueUpdated', (e) => {
                        // Update UI when queue status changes
                        const poliId = e.poli_id;
                        const antrianNumber = e.antrian_number;
                        const status = e.status;

                        // Update banner nomor sedang dilayani
                        const bannerDilayani = document.getElementById('live-banner-dilayani');
                        if (bannerDilayani) {
                            const newNum = antrianNumber > 0 ? antrianNumber : '-';
                            if (bannerDilayani.textContent.trim() !== String(newNum)) {
                                bannerDilayani.style.transform = 'scale(1.25)';
                                bannerDilayani.classList.add('text-amber-500');
                                setTimeout(() => {
                                    bannerDilayani.textContent = newNum;
                                    bannerDilayani.style.transform = 'scale(1)';
                                    bannerDilayani.classList.remove('text-amber-500');
                                }, 250);
                            }
                        }

                        // Update nomor sedang dilayani di setiap baris tabel jadwal
                        document.querySelectorAll('.jadwal-dilayani-badge[data-jadwal-id="' + poliId + '"]').forEach(badge => {
                            const numEl = badge.querySelector('.jadwal-dilayani-num');
                            const newVal = antrianNumber > 0 ? antrianNumber : '-';
                            if (numEl && numEl.textContent.trim() !== String(newVal)) {
                                numEl.textContent = newVal;
                                badge.classList.add('ring-2', 'ring-indigo-400', 'bg-indigo-100');
                                setTimeout(() => {
                                    badge.classList.remove('ring-2', 'ring-indigo-400', 'bg-indigo-100');
                                }, 1200);
                            }
                        });
                    });
                });
            }

            // Jalankan init setelah DOM fully loaded
            if (document.readyState === 'complete') {
                initLaravelEcho();
            } else {
                window.addEventListener('load', initLaravelEcho);
            }
        });
    </script>

</x-layouts.app>
