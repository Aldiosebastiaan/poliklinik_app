<x-layouts.app title="Edit Jadwal Periksa">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('jadwal-periksa.index') }}" class="flex items-center justify-center w-9 h-9 
                  rounded-lg bg-slate-100 hover:bg-slate-200 
                  text-slate-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>

        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Edit Jadwal Periksa
            </h2>
            <p class="text-sm text-slate-500">
                Perbarui hari atau jam praktik pemeriksaan Anda
            </p>
        </div>
    </div>

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-8">

            <form action="{{ route('jadwal-periksa.update', $jadwal->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6 mb-6">

                    {{-- Hari --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                            Hari Praktik <span class="text-red-500">*</span>
                        </label>
                        <select name="hari"
                            class="w-full border-2 rounded-lg p-2.5 bg-white
                                   focus:border-primary focus:outline-none text-slate-800
                                   @error('hari') border-red-500 @enderror"
                            required>
                            @foreach($hariList as $hari)
                            <option value="{{ $hari }}" {{ old('hari', $jadwal->hari) == $hari ? 'selected' : '' }}>
                                {{ $hari }}
                            </option>
                            @endforeach
                        </select>
                        @error('hari')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jam Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Jam Mulai --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">
                                Jam Mulai <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="jam_mulai"
                                value="{{ old('jam_mulai', date('H:i', strtotime($jadwal->jam_mulai))) }}"
                                class="w-full border-2 rounded-lg p-2.5 text-slate-800
                                       focus:border-primary focus:outline-none
                                       @error('jam_mulai') border-red-500 @enderror"
                                required>
                            @error('jam_mulai')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Jam Selesai --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">
                                Jam Selesai <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="jam_selesai"
                                value="{{ old('jam_selesai', date('H:i', strtotime($jadwal->jam_selesai))) }}"
                                class="w-full border-2 rounded-lg p-2.5 text-slate-800
                                       focus:border-primary focus:outline-none
                                       @error('jam_selesai') border-red-500 @enderror"
                                required>
                            @error('jam_selesai')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('jadwal-periksa.index') }}"
                        class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-sm font-semibold transition shadow-sm">
                        Perbarui Jadwal
                    </button>
                </div>

            </form>

        </div>
    </div>

</x-layouts.app>
