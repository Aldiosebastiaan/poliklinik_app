<x-layouts.app title="Profil">

    @php
        $dashboardRoute = match (auth()->user()->role) {
            'admin' => 'admin.dashboard',
            'dokter' => 'dokter.dashboard',
            default => 'pasien.dashboard',
        };
    @endphp

    <div class="max-w-7xl mx-auto mb-4">
        <a href="{{ route($dashboardRoute) }}" class="btn btn-ghost btn-sm">
            <i class="fas fa-arrow-left me-2"></i>
            Kembali
        </a>
    </div>

    {{-- Welcome Header --}}
    <div class="max-w-7xl mx-auto">
        <div class="bg-base-100 rounded-2xl p-6 shadow-sm mb-6">
            <h2 class="text-2xl font-bold">Profil</h2>
            <p class="text-base-content/60 mt-1">Kelola keamanan akun Anda.</p>
        </div>

        <div class="bg-base-100 rounded-2xl p-6 shadow-sm">
            <h3 class="text-lg font-semibold mb-2">Ganti Password</h3>
            <p class="text-sm text-base-content/60 mb-5">Gunakan password baru yang berbeda dan minimal 8 karakter.</p>

            @if(session('status'))
                <div class="alert alert-success mb-5" role="status">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="label">
                        <span class="label-text font-medium">Password lama</span>
                    </label>
                    <input
                        id="current_password"
                        name="current_password"
                        type="password"
                        autocomplete="current-password"
                        required
                        aria-invalid="@error('current_password') true @else false @enderror"
                        aria-describedby="current_password_error"
                        class="input input-bordered w-full @error('current_password') input-error @enderror">
                    @error('current_password')
                        <p id="current_password_error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="label">
                        <span class="label-text font-medium">Password baru</span>
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        required
                        aria-invalid="@error('password') true @else false @enderror"
                        aria-describedby="password_error"
                        class="input input-bordered w-full @error('password') input-error @enderror">
                    @error('password')
                        <p id="password_error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="label">
                        <span class="label-text font-medium">Konfirmasi password baru</span>
                    </label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        required
                        aria-invalid="@error('password_confirmation') true @else false @enderror"
                        aria-describedby="password_confirmation_error"
                        class="input input-bordered w-full @error('password_confirmation') input-error @enderror">
                    @error('password_confirmation')
                        <p id="password_confirmation_error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-full sm:w-auto">
                    Simpan Password Baru
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>
