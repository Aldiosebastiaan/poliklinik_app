<x-layouts.app title="Dashboard Pasien">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Halo, Selamat Datang Pasien {{ Auth::user()->nama ?? '' }}
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            Selamat datang di panel Pasien Poliklinik.
        </p>
    </div>
</x-layouts.app>
