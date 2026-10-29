<x-layouts.app title="Dashboard Dokter">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Halo, Selamat Datang Dokter {{ Auth::user()->nama ?? '' }}
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            Selamat datang di panel dokter Poliklinik.
        </p>
    </div>
</x-layouts.app>
