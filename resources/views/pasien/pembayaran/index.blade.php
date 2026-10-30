<x-layouts.app title="Pembayaran Saya">

@section('content')
@push('scripts')
<script>
    window.Laravel = {{ json_encode(config('laravel')) }};
</script>
@endpush

{{-- Set Carbon Locale --}}
{{-- <script>window.Carbon = {locale: 'id'}</script> --}}
@push('scripts')
<script>
    // Set Carbon locale to Indonesian
    window.Carbon = {locale: 'id'};
</script>
@endpush

<!-- Page Header -->
<div class="bg-base-100 shadow-xl rounded-2xl p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold">Pembayaran Saya</h2>
            <p class="text-base-content/60">Upload bukti pembayaran pemeriksaan</p>
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

<!-- Tagihan Section -->
@if($daftarPolis->isNotEmpty())
    <div class="bg-base-100 rounded-2xl p-6 mb-6 shadow-sm">
        <h3 class="font-semibold mb-4">Tagihan dari Pemeriksaan</h3>
        <div class="space-y-4">
            @foreach($daftarPolis as $daftarPoli)
                @if($daftarPoli->periksas->isNotEmpty())
                    @php
                        $periksaTerakhir = $daftarPoli->periksas->last();
                        $biaya = $periksaTerakhir ? $periksaTerakhir->biaya_periksa : 0;
                    @endphp
                    
                    <div class="p-4 rounded-lg border border-base-300 hover:border-primary transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">{{ $daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poli' }}</p>
<p class="text-sm text-base-content/60">
    No Antrian: #{{ $daftarPoli->no_antrian }} · 
    Tanggal: {{ $daftarPoli->jadwalPeriksa->hari }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-semibold">Rp {{ number_format($biaya, 0, ',', '.') }}</p>
                                @if($periksaTerakhir->bukti_pembayaran)
                                    <p class="text-xs text-primary">Sudah ada bukti</p>
                                @endif
                            </div>
                        </div>
                        
                        @if(!$periksaTerakhir->bukti_pembayaran)
                            <p class="text-xs text-base-content/40 mt-1">Belum ada bukti pembayaran</p>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@else
    <div class="bg-base-100 rounded-2xl p-6 text-center text-base-content/40">
        <p>Anda belum memiliki riwayat pemeriksaan.</p>
    </div>
@endif

<!-- Upload Bukti Section -->
<div class="bg-base-100 rounded-2xl p-6 shadow-sm">
    <h3 class="font-semibold mb-4">Upload Bukti Pembayaran</h3>
    
    @if(session('success'))
        <div class="alert alert-success mb-3">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-error mb-3">
            <i class="fas fa-exclamation-circle"></i>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ route('pasien.pembayaran.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        
        <input type="hidden" name="id_daftar_poli" value="{{ old('id_daftar_poli', $daftarPolis->first()?->id ?? '') }}">
        
        <div>
            <label class="label">
                <span class="label-text">Pilih Poli/Jadwal</span>
            </label>
            <select name="id_daftar_poli" class="select select-bordered w-full mt-1">
                <option value="">-- Pilih Poli/Jadwal --</option>
                @foreach($daftarPolis as $dp)
                    <option value="{{ $dp->id }}"
                        {{ old('id_daftar_poli') == $dp->id ? 'selected' : '' }}>
                        {{ $dp->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poli' }} - No Antrian #{{ $dp->no_antrian }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="label">
                <span class="label-text">Bukti Pembayaran (Foto)</span>
            </label>
            <input type="file" name="bukti" class="file-input file-input-bordered w-full mt-1" accept="image/*">
            <p class="text-xs text-base-content/40 mt-1">Format: jpeg, png, jpg | Maksimal 2MB</p>
        </div>
        
        <button type="submit"
            class="btn btn-primary w-full">
            <i class="fas fa-upload me-2"></i>
            Unggah Bukti
        </button>
    </form>
</div>

<!-- Riwayat Pembayaran -->
@if($pembayaranBuktis->isNotEmpty())
    <div class="mt-6 bg-base-100 rounded-2xl p-6 shadow-sm">
        <h3 class="font-semibold mb-3">Riwayat Pembayaran</h3>
        <div class="space-y-3">
            @foreach($pembayaranBuktis as $pb)
                <div class="p-3 rounded-lg border-l-4 {{ $pb->status == 'lunas' ? 'border-green-500' : ($pb->status == 'ditolak' ? 'border-red-500' : 'border-yellow-500') }}">
                    <div class="flex items-between">
                        <div>
                            <p class="font-medium">#{{ $pb->id }}</p>
                            <p class="text-xs text-base-content/60">
                                {{ \Carbon\Carbon::parse($pb->created_at)->format('d F Y H:i') }}
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded {{ $pb->status == 'lunas' ? 'bg-green-100 text-green-800' : ($pb->status == 'ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ $pb->status === 'ditolak' ? 'Ditolak' : ucfirst($pb->status) }}
                        </span>
                    </div>
                    <p class="text-xs text-base-content/50 mt-1">
                        {{ str_replace(public_path(), '', $pb->file_path) }}
                    </p>
                    @if($pb->status === 'ditolak')
                        <p class="text-xs text-error mt-1">Bukti pembayaran ditolak. Silakan unggah ulang bukti pembayaran.</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif

</x-layouts.app>
