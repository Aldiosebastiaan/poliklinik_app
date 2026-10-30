<x-layouts.app title="Daftar Poli">

    <div class="w-full px-4 py-6 md:py-8">
        <div class="w-full max-w-7xl mx-auto">

            {{-- Warning jika masih ada antrian aktif --}}
            @if ($antrianAktif)
            <div class="alert alert-warning mb-6 rounded-2xl shadow-sm border border-amber-300 bg-amber-50 text-amber-900">
                <i class="fas fa-triangle-exclamation text-amber-600 text-xl"></i>
                <div>
                    <div class="font-bold">Perhatian: Anda Masih Memiliki Antrian Aktif!</div>
                    <div class="text-xs text-amber-800 mt-0.5">
                        Anda saat ini terdaftar di <strong>{{ $antrianAktif->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poliklinik' }}</strong> (Nomor Antrian: <strong>#{{ $antrianAktif->no_antrian }}</strong>). Sesuai ketentuan, pendaftaran poli baru hanya dapat dilakukan setelah pemeriksaan selesai.
                    </div>
                </div>
                <a href="{{ route('pasien.dashboard') }}" class="btn btn-sm bg-amber-600 hover:bg-amber-700 text-white border-none rounded-xl ml-auto">
                    Lihat Antrian
                </a>
            </div>
            @endif

            {{-- Toast/Alert Success --}}
            @if (session('message'))
            <div id="toastSuccess" class="toast toast-top toast-end z-50">
                <div class="alert alert-success shadow-lg">
                    <span>{{ session('message') }}</span>
                </div>
            </div>
            @endif

            {{-- Alert Error --}}
            @if (session('error'))
            <div class="alert alert-error mb-4 rounded-2xl">
                <i class="fas fa-circle-xmark text-lg"></i>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            {{-- Validation Error --}}
            @if ($errors->any())
            <div class="alert alert-error mb-4 rounded-2xl">
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card bg-base-100 shadow-md border border-slate-200 rounded-2xl">
                <div class="card-body p-6 md:p-8">

                    <h2 class="text-2xl font-bold text-center text-slate-800 mb-6 flex items-center justify-center gap-2">
                        <span class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base">
                            <i class="fas fa-hospital-user"></i>
                        </span>
                        Pendaftaran Poli
                    </h2>

                    <form action="{{ route('pasien.daftar.submit') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_pasien" value="{{ $user->id }}">

                        <fieldset {{ $antrianAktif ? 'disabled' : '' }}>

                            {{-- Nomor RM --}}
                            <div class="mb-4">
                                <label class="font-semibold block text-sm text-slate-700 mb-1">
                                    Nomor Rekam Medis
                                </label>

                                <input type="text" value="{{ $user->no_rm ?? '-' }}"
                                    class="w-full border-2 border-slate-200 rounded-lg p-2.5 bg-slate-100 text-slate-600 font-mono" disabled>
                            </div>

                            {{-- Pilih Poli --}}
                            <div class="mb-4">
                                <label class="font-semibold block text-sm text-slate-700 mb-1">
                                    Pilih Poli <span class="text-red-500">*</span>
                                </label>

                                <select name="id_poli" id="poliSelect" class="w-full border-2 border-slate-200 rounded-lg p-2.5 focus:border-primary focus:outline-none bg-white text-slate-800" required>
                                    <option value="">-- Pilih Poli --</option>
                                    @foreach ($polis as $poli)
                                    <option value="{{ $poli->id }}">
                                        {{ $poli->nama_poli }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Pilih Jadwal --}}
                            <div class="mb-4">
                                <label class="font-semibold block text-sm text-slate-700 mb-1">
                                    Pilih Jadwal Periksa <span class="text-red-500">*</span>
                                </label>

                                <select name="id_jadwal" id="jadwalSelect" class="w-full border-2 border-slate-200 rounded-lg p-2.5 focus:border-primary focus:outline-none bg-white text-slate-800" required>
                                    <option value="">-- Pilih Jadwal --</option>

                                    @foreach ($jadwals as $jadwal)
                                    <option value="{{ $jadwal->id }}" data-poli="{{ $jadwal->dokter?->id_poli ?? '' }}">
                                        {{ $jadwal->hari }}
                                        ({{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }})
                                        — Dr. {{ $jadwal->dokter->nama ?? '--' }}
                                    </option>
                                    @endforeach

                                </select>
                            </div>

                            {{-- Keluhan --}}
                            <div class="mb-6">
                                <label class="font-semibold block text-sm text-slate-700 mb-1">
                                    Keluhan <span class="text-slate-400 font-normal">(Opsional)</span>
                                </label>
                                <textarea name="keluhan" rows="3" class="w-full border-2 border-slate-200 rounded-lg p-2.5 focus:border-primary focus:outline-none resize-none"
                                    placeholder="Tulis keluhan yang Anda rasakan..."></textarea>
                            </div>

                            <div class="flex items-center justify-between pt-2">
                                <a href="{{ route('pasien.dashboard') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
                                </a>

                                @if ($antrianAktif)
                                <button type="button" disabled
                                    class="px-8 py-2.5 bg-slate-300 text-slate-500 font-semibold rounded-xl cursor-not-allowed">
                                    Antrian Masih Aktif
                                </button>
                                @else
                                <button type="submit"
                                    class="px-8 py-2.5 bg-primary hover:bg-primary/90 text-white font-semibold rounded-xl transition shadow-sm">
                                    Daftar Poli
                                </button>
                                @endif
                            </div>

                        </fieldset>
                    </form>

                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function(){
            const poliSelect = document.getElementById("poliSelect");
            const jadwalSelect = document.getElementById("jadwalSelect");
            if (!poliSelect || !jadwalSelect) return;

            const jadwalOptions = jadwalSelect.querySelectorAll("option");

            poliSelect.addEventListener("change", function(){
                let poliId = this.value;
                jadwalOptions.forEach(option => {
                    if (option.value === "") {
                        option.style.display = "block";
                        return;
                    }
                    if (option.dataset.poli === poliId) {
                        option.style.display = "block";
                    } else {
                        option.style.display = "none";
                    }
                });
                jadwalSelect.value = "";
            });
        });
    </script>
    @endpush
</x-layouts.app>
