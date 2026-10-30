<x-layouts.app title="Edit Obat">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('obat.index') }}" class="flex items-center justify-center w-9 h-9 rounded-lg 
                  bg-slate-100 hover:bg-slate-200 
                  text-slate-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>

        <h2 class="text-2xl font-bold text-slate-800">
            Edit Obat
        </h2>
    </div>

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-8">

            <form action="{{ route('obat.update', $obat->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Fields --}}
                <div class="space-y-5 mb-6">

                    {{-- Nama Obat --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                            Nama Obat <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_obat" value="{{ old('nama_obat', $obat->nama_obat) }}"
                            placeholder="Contoh: Paracetamol 500mg..."
                            class="w-full border-2 rounded-lg p-2.5
                                   focus:border-primary focus:outline-none
                                   @error('nama_obat') border-red-500 @enderror"
                            required>
                        @error('nama_obat')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kemasan --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                            Kemasan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="kemasan" value="{{ old('kemasan', $obat->kemasan) }}"
                            placeholder="Contoh: Strip @ 10 tablet, Botol 60ml..."
                            class="w-full border-2 rounded-lg p-2.5
                                   focus:border-primary focus:outline-none
                                   @error('kemasan') border-red-500 @enderror"
                            required>
                        @error('kemasan')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Stok --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                            Stok <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stok" value="{{ old('stok', $obat->stok) }}" min="0"
                            class="w-full border-2 rounded-lg p-2.5 focus:border-primary focus:outline-none @error('stok') border-red-500 @enderror" required>
                        @error('stok')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Harga --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                            Harga (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="harga" value="{{ old('harga', $obat->harga) }}"
                            placeholder="Contoh: 15000..." min="0"
                            class="w-full border-2 rounded-lg p-2.5
                                   focus:border-primary focus:outline-none
                                   @error('harga') border-red-500 @enderror"
                            required>
                        @error('harga')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('obat.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary/90 text-white text-sm font-semibold transition">
                        Perbarui Obat
                    </button>
                </div>

            </form>

        </div>
    </div>

</x-layouts.app>
