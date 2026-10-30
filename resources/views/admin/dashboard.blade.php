<x-layouts.app title="Admin Dashboard">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Selamat Datang Admin</h1>
            <p class="text-base-content/60">Panel kontrol administrator</p>
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
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        {{-- Poli --}}
        <div class="bg-base-100 rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3">
                <div class="icon-box">
                    <i class="fas fa-hospital text-2xl text-indigo-600"></i>
                </div>
                <div>
                    <p class="text-sm text-base-content/60">Poliklinik</p>
                    <p class="text-2xl font-bold">{{ \App\Models\Poli::count() }}</p>
                </div>
            </div>
        </div>

        {{-- Dokter --}}
        <div class="bg-base-100 rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3">
                <div class="icon-box">
                    <i class="fas fa-user-doctor text-2xl text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm text-base-content/60">Dokter</p>
                    <p class="text-2xl font-bold">{{ \App\Models\User::where('role', 'dokter')->count() }}</p>
                </div>
            </div>
        </div>

        {{-- Pasien --}}
        <div class="bg-base-100 rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3">
                <div class="icon-box">
                    <i class="fas fa-user text-2xl text-amber-600"></i>
                </div>
                <div>
                    <p class="text-sm text-base-content/60">Pasien</p>
                    <p class="text-2xl font-bold">{{ \App\Models\User::where('role', 'pasien')->count() }}</p>
                </div>
            </div>
        </div>

        {{-- Obat --}}
        <div class="bg-base-100 rounded-2xl p-6 shadow-sm hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3">
                <div class="icon-box">
                    <i class="fas fa-pills text-2xl text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-base-content/60">Obat</p>
                    <p class="text-2xl font-bold">{{ \App\Models\Obat::count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan pasien per jadwal --}}
    <section class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-4">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Pasien Menunggu per Jadwal</h2>
                <p class="text-sm text-slate-500 mt-1">Jumlah pasien yang terdaftar dan masih menunggu pada setiap jadwal periksa.</p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1.5 text-sm font-semibold text-amber-800 border border-amber-200">
                <i class="fas fa-hourglass-half"></i>
                {{ $jumlahMenunggu }} pasien menunggu total
            </span>
        </div>

        @if($jadwals->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($jadwals as $jadwal)
                    <article class="bg-base-100 rounded-2xl border border-base-200 p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div>
                                <h3 class="font-bold text-slate-800">{{ $jadwal->hari }}</h3>
                                <p class="text-sm text-slate-500 mt-1">
                                    {{ date('H:i', strtotime($jadwal->jam_mulai)) }}–{{ date('H:i', strtotime($jadwal->jam_selesai)) }} WIB
                                </p>
                            </div>
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                                <i class="fas fa-calendar-day"></i>
                            </span>
                        </div>

                        <p class="text-sm text-slate-600 mb-4">
                            {{ $jadwal->dokter->poli->nama_poli ?? 'Poli' }}
                            <span class="text-slate-400">·</span>
                            Dr. {{ $jadwal->dokter->nama ?? 'Dokter' }}
                        </p>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-amber-50 p-3">
                                <p class="text-xs text-amber-700">Pasien menunggu</p>
                                <p class="text-2xl font-bold text-amber-800">{{ $jadwal->jumlah_menunggu }}</p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-500">Total terdaftar</p>
                                <p class="text-2xl font-bold text-slate-800">{{ $jadwal->jumlah_pendaftaran }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-base-300 bg-base-100 p-8 text-center text-slate-500">
                <i class="fas fa-calendar-xmark text-3xl text-slate-300 mb-3"></i>
                <p>Belum ada jadwal periksa yang tersedia.</p>
            </div>
        @endif
    </section>

    {{-- Jadwal Poliklinik & Dokter ================= --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-xl font-bold text-slate-800">
                Daftar Jadwal Poliklinik & Dokter
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
                Informasi jadwal praktik dokter dan nomor antrian yang sedang dilayani saat ini
            </p>
        </div>

        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200">
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
                                <span class="jadwal-dilayani-badge inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-202 transition-all duration-300"
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
