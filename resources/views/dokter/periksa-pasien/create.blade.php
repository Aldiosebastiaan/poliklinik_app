<x-layouts.app title="Periksa Pasien">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('periksa-pasien.index') }}" class="inline-flex items-center justify-center w-9 h-9 
                  rounded-lg bg-slate-100 text-slate-500 
                  hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <h2 class="text-2xl font-bold text-slate-800">
            Periksa Pasien
        </h2>
    </div>

    {{-- Card --}}
    <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
        <div class="card-body p-8">

            <section class="rounded-xl border border-indigo-100 bg-indigo-50/60 p-5 mb-6" aria-labelledby="patient-info-title">
                <h3 id="patient-info-title" class="font-bold text-slate-800 mb-4">Informasi Pasien</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-xs text-slate-500">Nama pasien</p>
                        <p class="font-semibold text-slate-800">{{ $daftarPoli->pasien?->nama ?? 'Pasien' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">No. antrean</p>
                        <p class="font-semibold text-slate-800">{{ $daftarPoli->no_antrian }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Keluhan pasien</p>
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $daftarPoli->keluhan ?: 'Tidak ada keluhan yang dicatat.' }}</p>
                </div>
            </section>

            <form action="{{ route('periksa-pasien.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_daftar_poli" value="{{ $daftarPoli->id }}">

                @if ($errors->any())
                    <div class="alert alert-error mb-5" role="alert">
                        <div>
                            <p class="font-semibold">Pemeriksaan belum tersimpan.</p>
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                {{-- Keluhan --}}
                <div class="form-control mb-5">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-gray-700">Keluhan <span class="text-red-500">*</span></span>
                    </label>
                    <textarea name="keluhan" id="keluhan" rows="4"
                        placeholder="Masukkan keluhan pasien..."
                        class="textarea textarea-bordered w-full border-2 px-4 py-2 rounded-lg resize-none">{{ old('keluhan') }}</textarea>
                    <p class="text-xs text-slate-400 font-normal mt-1">* Wajib diisi karena ada constraint not null di database</p>
                </div>

                {{-- Pilih Obat --}}
                <div class="form-control mb-5">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-gray-700">Pilih Obat <span class="text-red-500">*</span></span>
                    </label>
                    <select id="select-obat" class="select select-bordered w-full rounded-lg border-2 px-4">
                        <option value="">-- Pilih Obat --</option>
                        @foreach ($obats as $obat)
                            <option value="{{ $obat->id }}"
                                data-nama="{{ $obat->nama_obat }}"
                                data-harga="{{ $obat->harga }}">
                                {{ $obat->nama_obat }} - Rp{{ number_format($obat->harga) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Obat Terpilih --}}
                <div class="form-control mb-5">
                    <label class="label pb-1 ">
                        <span class="text-sm font-semibold text-gray-700">Obat Terpilih</span>
                    </label>

                    <ul id="obat-terpilih" class="flex flex-col gap-2 mb-2 min-h-[48px]"></ul>

                    <input type="hidden" name="obat_json" id="obat_json" value="{{ old('obat_json', '[]') }}">
                <input type="hidden" name="biaya_periksa" id="biaya_periksa" value="{{ $biayaKonsultasi }}">
                    @error('obat_json')
                        <p class="text-sm text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Total Harga --}}
                <div class="form-control mb-5">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-gray-700">Total Harga</span>
                    </label>
                    <div class="input input-bordered w-full rounded-lg flex items-center bg-slate-50 text-slate-700 font-bold" id="total-harga">
                        Rp {{ number_format($biayaKonsultasi, 0, ',', '.') }}
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="form-control mb-8">
                    <label class="label pb-1">
                        <span class="text-sm font-semibold text-gray-700">Catatan <span class="text-slate-400 font-normal">(Opsional)</span></span>
                    </label>
                    <textarea name="catatan" id="catatan" rows="4"
                        placeholder="Masukkan catatan..."
                        class="textarea textarea-bordered w-full border-2 px-4 py-2 rounded-lg resize-none">{{ old('catatan') }}</textarea>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3">
                    <button type="submit"
                        class="btn bg-[#2d4499] hover:bg-[#1e2d6b] text-white border-none rounded-lg px-6">
                        <i class="fas fa-save"></i>
                        Simpan
                    </button>
                    <a href="{{ route('periksa-pasien.index') }}"
                        class="btn btn-ghost bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-lg px-6">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>

    <script>
        const selectObat = document.getElementById('select-obat');
        const listObat = document.getElementById('obat-terpilih');
        const inputObatJson = document.getElementById('obat_json');
        const totalHargaEl = document.getElementById('total-harga');
        const biayaKonsultasi = @json($biayaKonsultasi);

        let daftarObat = [];

        selectObat.addEventListener('change', () => {
            const selectedOption = selectObat.options[selectObat.selectedIndex];
            const id = selectedOption.value;
            const nama = selectedOption.dataset.nama;
            const harga = parseInt(selectedOption.dataset.harga || 0);

            if (!id || daftarObat.some(o => o.id == id)) return;

            daftarObat.push({ id, nama, harga });
            renderObat();
            selectObat.selectedIndex = 0;
        });

        function renderObat() {
            listObat.innerHTML = '';
            let totalObat = 0;

            daftarObat.forEach((obat, index) => {
                totalObat += obat.harga;

                const item = document.createElement('li');
                item.className = 'flex items-center justify-between px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700';
                item.innerHTML = `
                    <span>${obat.nama} — <span class="font-semibold">Rp ${obat.harga.toLocaleString()}</span></span>
                    <button type="button"
                        onclick="hapusObat(${index})"
                        class="btn btn-sm bg-red-500 hover:bg-red-600 text-white border-none rounded-lg px-3">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
                listObat.appendChild(item);
            });

            totalHargaEl.textContent = `Rp ${(biayaKonsultasi + totalObat).toLocaleString('id-ID')}`;
            inputObatJson.value = JSON.stringify(daftarObat.map(o => o.id));
        }

        try {
            const oldObatIds = JSON.parse(inputObatJson.value || '[]');
            oldObatIds.forEach((id) => {
                const option = Array.from(selectObat.options).find((item) => item.value === String(id));
                if (option && option.value) {
                    daftarObat.push({
                        id: option.value,
                        nama: option.dataset.nama,
                        harga: Number(option.dataset.harga || 0),
                    });
                }
            });
            renderObat();
        } catch {
            inputObatJson.value = '[]';
        }

        function hapusObat(index) {
            daftarObat.splice(index, 1);
            renderObat();
        }
    </script>

</x-layouts.app>
