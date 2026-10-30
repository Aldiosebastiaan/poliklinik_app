<x-layouts.app title="Dashboard Pasien">

    {{-- Welcome Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Halo, Selamat Datang Pasien {{ Auth::user()->nama ?? '' }}
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                Pantau antrian aktif Anda dan jadwal praktik dokter poliklinik secara realtime.
            </p>
        </div>

        @if(!$antrianAktif)
        <a href="{{ route('pasien.daftar') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-xl transition shadow-sm">
            <i class="fas fa-plus text-xs"></i>
            Daftar Poli
        </a>
        @endif
    </div>

    {{-- Flash Message --}}
    @if(session('message'))
    <div class="alert alert-success mb-6 rounded-2xl shadow-sm">
        <i class="fas fa-circle-check text-lg"></i>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-error mb-6 rounded-2xl shadow-sm">
        <i class="fas fa-circle-exclamation text-lg"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif


    {{-- ================= BANNER ANTRIAN AKTIF ================= --}}
    @if($antrianAktif && $antrianAktif->jadwalPeriksa)
    <div class="relative overflow-hidden rounded-3xl p-6 md:p-8 mb-8 text-white shadow-xl shadow-indigo-600/20"
         style="background: linear-gradient(135deg, #2563eb 0%, #4f46e5 50%, #4338ca 100%);">

        {{-- Background Decorative Rings --}}
        <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="absolute right-36 -top-12 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>

        <div class="relative z-10">

            {{-- Upper Title --}}
            <div class="text-[11px] md:text-xs font-bold tracking-widest uppercase text-blue-200 mb-5">
                ANTRIAN AKTIF ANDA
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">

                {{-- Left Side: Details --}}
                <div class="space-y-4">

                    {{-- Poliklinik --}}
                    <div>
                        <div class="text-[11px] text-blue-200 font-medium">Poliklinik</div>
                        <div class="text-2xl md:text-3xl font-black text-white leading-tight">
                            {{ $antrianAktif->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poliklinik' }}
                        </div>
                    </div>

                    {{-- Dokter --}}
                    <div>
                        <div class="text-[11px] text-blue-200 font-medium">Dokter</div>
                        <div class="text-lg md:text-xl font-bold text-white">
                            Dr. {{ $antrianAktif->jadwalPeriksa->dokter->nama ?? '-' }}
                        </div>
                    </div>

                    {{-- Jadwal Periksa --}}
                    <div>
                        <div class="text-[11px] text-blue-200 font-medium">Jadwal Periksa</div>
                        <div class="text-sm md:text-base font-semibold text-blue-100">
                            {{ $antrianAktif->jadwalPeriksa->hari }} ({{ date('H:i', strtotime($antrianAktif->jadwalPeriksa->jam_mulai)) }} - {{ date('H:i', strtotime($antrianAktif->jadwalPeriksa->jam_selesai)) }})
                        </div>
                    </div>

                </div>

                {{-- Right Side: Queue Cards --}}
                <div class="flex items-center gap-3 md:gap-4 self-start lg:self-center">

                    {{-- Nomor Anda --}}
                    <div class="bg-white/15 backdrop-blur-md border border-white/25 rounded-2xl p-4 md:p-5 text-center min-w-[110px] md:min-w-[125px] shadow-inner">
                        <div class="text-[11px] md:text-xs font-medium text-blue-100 mb-1">
                            Nomor Anda
                        </div>
                        <div class="text-4xl md:text-5xl font-black text-white tracking-tight">
                            {{ $antrianAktif->no_antrian }}
                        </div>
                    </div>

                    {{-- Sedang Dilayani --}}
                    <div class="bg-white rounded-2xl p-4 md:p-5 text-center min-w-[130px] md:min-w-[150px] shadow-2xl">
                        <div class="text-[10px] md:text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">
                            SEDANG DILAYANI
                        </div>
                        <div id="live-banner-dilayani"
                             class="text-4xl md:text-5xl font-black text-indigo-700 tracking-tight transition-all duration-300">
                            {{ $sedangDilayaniAktif > 0 ? $sedangDilayaniAktif : '-' }}
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
    {{-- Pesan Kosong Jika Tidak Ada Antrian Aktif --}}
    <div class="card bg-base-100 border border-slate-200 rounded-3xl shadow-sm p-8 mb-8 text-center">
        <div class="max-w-md mx-auto py-2">
            <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fas fa-ticket-simple"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">
                Tidak Ada Antrian Aktif
            </h3>
            <p class="text-sm text-slate-500 mb-5">
                Anda tidak memiliki antrian pemeriksaan aktif saat ini. Anda dapat mendaftar antrian poli baru melalui tombol di bawah.
            </p>
            <a href="{{ route('pasien.daftar') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                <i class="fas fa-calendar-plus text-xs"></i>
                Daftar Antrian Poli
            </a>
        </div>
    </div>
    @endif


    {{-- ================= TABEL JADWAL POLIKLINIK DAN DOKTER ================= --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">
                Daftar Jadwal Poliklinik & Dokter
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
                Informasi jadwal praktik dokter dan nomor antrian yang sedang dilayani saat ini
            </p>
        </div>

        <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            Realtime Antrian
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
                            <th class="px-6 py-4">Poli</th>
                            <th class="px-6 py-4">Dokter</th>
                            <th class="px-6 py-4">Hari</th>
                            <th class="px-6 py-4">Jam Periksa</th>
                            <th class="px-6 py-4 text-center">Sedang Dilayani</th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody class="text-sm text-slate-700">
                        @forelse($jadwals as $jadwal)
                        <tr class="border-t border-slate-100 hover:bg-slate-50 transition">

                            {{-- No --}}
                            <td class="px-6 py-4 text-slate-500">
                                {{ $loop->iteration }}
                            </td>

                            {{-- Poli --}}
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                <span class="inline-flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">
                                        <i class="fas fa-hospital"></i>
                                    </span>
                                    {{ $jadwal->dokter->poli->nama_poli ?? '-' }}
                                </span>
                            </td>

                            {{-- Dokter --}}
                            <td class="px-6 py-4 font-medium text-slate-800">
                                Dr. {{ $jadwal->dokter->nama ?? '-' }}
                            </td>

                            {{-- Hari --}}
                            <td class="px-6 py-4 text-slate-600">
                                <span class="inline-flex items-center gap-1.5 font-medium">
                                    <i class="fas fa-calendar-day text-indigo-500 text-xs"></i>
                                    {{ $jadwal->hari }}
                                </span>
                            </td>

                            {{-- Jam Periksa --}}
                            <td class="px-6 py-4 text-slate-600">
                                <span class="font-medium text-slate-700">
                                    {{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                                </span>
                                <span class="text-xs text-slate-400">WIB</span>
                            </td>

                            {{-- Sedang Dilayani Per Jadwal --}}
                            <td class="px-6 py-4 text-center">
                                <span class="jadwal-dilayani-badge inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 transition-all duration-300"
                                      data-jadwal-id="{{ $jadwal->id }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                    <span>Nomor <span class="jadwal-dilayani-num">{{ $jadwal->sedang_dilayani > 0 ? $jadwal->sedang_dilayani : '-' }}</span></span>
                                </span>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                <i class="fas fa-calendar-xmark text-3xl mb-3 block text-slate-300"></i>
                                Belum ada jadwal poliklinik yang tersedia
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
            ]); ?>;
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
