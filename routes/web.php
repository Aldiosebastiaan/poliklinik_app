<?php

use App\Http\Controllers\Admin\AdminPoliController;
use App\Http\Controllers\Admin\DokterController;
use App\Http\Controllers\Admin\ObatController;
use App\Http\Controllers\Admin\PasienController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dokter\DashboardController as DokterDashboardController;
use App\Http\Controllers\Dokter\JadwalPeriksaController;
use App\Http\Controllers\Dokter\PeriksaPasienController;
use App\Http\Controllers\Dokter\RiwayatPasienController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Pasien\PoliController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

// ===================== ADMIN =====================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminPoliController::class, 'dashboard'])->name('admin.dashboard');
    Route::resource('polis', AdminPoliController::class);
    Route::get('/dokter/export', [DokterController::class, 'export'])->name('admin.dokter.export');
    Route::resource('dokter', DokterController::class);
    Route::get('/pasien/export', [PasienController::class, 'export'])->name('admin.pasien.export');
    Route::resource('pasien', PasienController::class);
    Route::get('/obat/export', [ObatController::class, 'export'])->name('admin.obat.export');
    Route::resource('obat', ObatController::class);
    Route::get('/pembayaran', [PembayaranController::class, 'adminIndex'])->name('admin.pembayaran.index');
    Route::post('/pembayaran/{id}/verify', [PembayaranController::class, 'verify'])->name('admin.pembayaran.verify');
    Route::post('/pembayaran/{id}/reject', [PembayaranController::class, 'reject'])->name('admin.pembayaran.reject');
});

// ===================== DOKTER =====================
Route::middleware(['auth', 'role:dokter'])->prefix('dokter')->group(function () {
    Route::get('/dashboard', [DokterDashboardController::class, 'index'])->name('dokter.dashboard');
    Route::get('/jadwal-periksa/export', [JadwalPeriksaController::class, 'export'])->name('jadwal-periksa.export');
    Route::resource('jadwal-periksa', JadwalPeriksaController::class);

    Route::get('/periksa-pasien', [PeriksaPasienController::class, 'index'])->name('periksa-pasien.index');
    Route::post('/periksa-pasien', [PeriksaPasienController::class, 'store'])->name('periksa-pasien.store');
    Route::post('/periksa-pasien/{id}/panggil', [PeriksaPasienController::class, 'call'])->name('periksa-pasien.call');
    Route::get('/periksa-pasien/{id}/detail', [PeriksaPasienController::class, 'detail'])->name('periksa-pasien.detail');
    Route::get('/periksa-pasien/{id}', [PeriksaPasienController::class, 'create'])->name('periksa-pasien.create');
    Route::get('/riwayat-pasien', [RiwayatPasienController::class, 'index'])->name('riwayat-pasien.index');
    Route::get('/riwayat-pasien/export', [RiwayatPasienController::class, 'export'])->name('riwayat-pasien.export');
    Route::get('/riwayat-pasien/{id}', [RiwayatPasienController::class, 'show'])->name('riwayat-pasien.show');

});

// ===================== PASIEN =====================
Route::middleware(['auth', 'role:pasien'])->prefix('pasien')->group(function () {
    Route::get('/dashboard', [PoliController::class, 'dashboard'])->name('pasien.dashboard');
    Route::get('/daftar', [PoliController::class, 'get'])->name('pasien.daftar');
    Route::post('/daftar', [PoliController::class, 'submit'])->name('pasien.daftar.submit');
    Route::get('/antrian-status', [PoliController::class, 'antrianStatus'])->name('pasien.antrian.status');
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pasien.pembayaran');
    Route::post('/pembayaran', [PembayaranController::class, 'store'])->name('pasien.pembayaran.store');
    Route::get('/riwayat', [PoliController::class, 'riwayat'])->name('pasien.riwayat');
    Route::get('/riwayat-pasien/{id}', [PoliController::class, 'riwayatDetail'])->name('pasien.riwayat-pasien.show');
});

// ===================== NOTIFICATIONS =====================
Route::middleware(['auth'])->prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markRead');
    Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
});
