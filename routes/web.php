<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// 🔷 Import Controllers - SISWA
use App\Http\Controllers\Siswa\AbsensiController;
use App\Http\Controllers\Siswa\JurnalController;
use App\Http\Controllers\Siswa\EOTAGController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\PerusahaanController as SiswaPerusahaanController; // 🔥 Baru

// 🔷 Import Controllers - PEMBIMBING
use App\Http\Controllers\Pembimbing\MonitoringController as PembimbingMonitoringController;
use App\Http\Controllers\Pembimbing\KunjunganController;
use App\Http\Controllers\Pembimbing\PenilaianController;
use App\Http\Controllers\Pembimbing\DashboardController as PembimbingDashboardController;
use App\Http\Controllers\Pembimbing\SiswaBinaanController;
use App\Http\Controllers\Pembimbing\AbsensiController as PembimbingAbsensiController;
use App\Http\Controllers\Pembimbing\JurnalController as PembimbingJurnalController;
use App\Http\Controllers\Pembimbing\NilaiController;
use App\Http\Controllers\Pembimbing\LaporanController;

// 🔷 Import Controllers - ADMIN
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\PerusahaanController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\PeriodePKLController;
use App\Http\Controllers\Admin\MonitoringController as AdminMonitoringController;
use App\Http\Controllers\Admin\PengumumanController;
use App\Http\Controllers\Admin\RekapAbsensiController;
use App\Http\Controllers\Admin\PerusahaanDataController; // 🔥 Baru
use App\Http\Controllers\Admin\AbsensiController as AdminAbsensiController;
use App\Http\Controllers\Admin\JurnalController as AdminJurnalController;

// =================================================================
// 🔐 PUBLIC ROUTES (Registrasi & Login)
// =================================================================
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register.store');

// =================================================================
// 🔐 AUTH ROUTES (Laravel Breeze default)
// =================================================================
require __DIR__.'/auth.php';

// =================================================================
// 🔷 Redirect otomatis berdasarkan role setelah login
// =================================================================
Route::get('/', function () {
    if (Auth::check()) {
        return match(Auth::user()->role) {
            'admin'      => to_route('admin.dashboard'),
            'pembimbing' => to_route('pembimbing.dashboard'),
            'siswa'      => to_route('siswa.dashboard'),
            default      => to_route('login'),
        };
    }
    return redirect()->route('login');
});

// =================================================================
// 🔷 ADMIN ROUTES
// =================================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard Admin
    Route::get('/dashboard', [AdminMonitoringController::class, 'dashboard'])->name('dashboard');
    
    // Manajemen User + Import + Approval Registrasi
    Route::get('/users/import', [UserController::class, 'importForm'])->name('users.import');
    Route::post('/users/import', [UserController::class, 'importStore'])->name('users.import.store');
    Route::resource('users', UserController::class);
    
    // Approval Registrasi Siswa
    Route::get('/registrasi-siswa', [UserController::class, 'registrasi'])->name('registrasi');
    Route::post('/registrasi-siswa/{user}/approve', [UserController::class, 'approveRegistrasi'])->name('registrasi.approve');
    Route::post('/registrasi-siswa/{user}/reject', [UserController::class, 'rejectRegistrasi'])->name('registrasi.reject');
    
    // Manajemen Master Data
    Route::resource('jurusan', JurusanController::class);
    Route::resource('perusahaan', PerusahaanController::class);
    

    // 🔥 BARU: Data Perusahaan PKL - Approval Actions
    Route::get('/perusahaan-data', [PerusahaanDataController::class, 'index'])->name('perusahaan-data');
    Route::get('/perusahaan-data/print', [PerusahaanDataController::class, 'print'])->name('perusahaan-data.print');
    Route::post('/perusahaan-data/{perusahaanData}/approve', [PerusahaanDataController::class, 'approve'])->name('perusahaan-data.approve');
    Route::post('/perusahaan-data/{perusahaanData}/reject', [PerusahaanDataController::class, 'reject'])->name('perusahaan-data.reject');
    
    // Manajemen Siswa (Assign)
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/{user}/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
    Route::put('/siswa/{user}', [SiswaController::class, 'update'])->name('siswa.update');
    
    // Periode PKL
    Route::resource('periode-pkl', PeriodePKLController::class);
    Route::post('/periode-pkl/{periodePkl}/activate', [PeriodePKLController::class, 'activate'])->name('periode-pkl.activate');
    
    // Monitoring Admin
    Route::get('/monitoring/kunjungan', [AdminMonitoringController::class, 'kunjungan'])->name('monitoring.kunjungan');
    Route::get('/monitoring/verifikasi', [AdminMonitoringController::class, 'verifikasi'])->name('monitoring.verifikasi');
    
    // Pengumuman
    Route::resource('pengumuman', PengumumanController::class);
    
    // Rekap Absensi
    Route::get('/rekap-absensi', [RekapAbsensiController::class, 'index'])->name('rekap-absensi.index');
    Route::get('/rekap-absensi/export', [RekapAbsensiController::class, 'export'])->name('rekap-absensi.export');

    // Kelola & Hapus Absensi
    Route::get('/absensi', [AdminAbsensiController::class, 'index'])->name('absensi.index');
    Route::delete('/absensi/{absensi}', [AdminAbsensiController::class, 'destroy'])->name('absensi.destroy');
    Route::post('/absensi/bulk-destroy', [AdminAbsensiController::class, 'bulkDestroy'])->name('absensi.bulk-destroy');

    // Kelola & Hapus Jurnal
    Route::get('/jurnal', [AdminJurnalController::class, 'index'])->name('jurnal.index');
    Route::delete('/jurnal/{jurnal}', [AdminJurnalController::class, 'destroy'])->name('jurnal.destroy');
    Route::post('/jurnal/bulk-destroy', [AdminJurnalController::class, 'bulkDestroy'])->name('jurnal.bulk-destroy');
    
}); // ✅ Tutup group admin

// =================================================================
// 🔷 PEMBIMBING ROUTES
// =================================================================
Route::middleware(['auth', 'role:pembimbing'])->prefix('pembimbing')->name('pembimbing.')->group(function () {
    
    // Dashboard (HANYA statistik ringkasan)
    Route::get('/dashboard', [\App\Http\Controllers\Pembimbing\DashboardController::class, 'index'])->name('dashboard');
    
    // 👥 Siswa Binaan
    Route::get('/siswa-binaan', [\App\Http\Controllers\Pembimbing\SiswaBinaanController::class, 'index'])->name('siswa-binaan');
    
    // 📅 Absensi Siswa
    Route::get('/absensi', [\App\Http\Controllers\Pembimbing\AbsensiController::class, 'index'])->name('absensi');
    Route::get('/absensi/export', [\App\Http\Controllers\Pembimbing\AbsensiController::class, 'export'])->name('absensi.export');
    Route::post('/absensi/{absensi}/verify', [\App\Http\Controllers\Pembimbing\AbsensiController::class, 'verify'])->name('absensi.verify');
    Route::post('/absensi/{absensi}/reject', [\App\Http\Controllers\Pembimbing\AbsensiController::class, 'reject'])->name('absensi.reject');
    // 📖 Review Jurnal
    Route::get('/jurnal', [\App\Http\Controllers\Pembimbing\JurnalController::class, 'index'])->name('jurnal');
    Route::post('/jurnal/{jurnal}/approve', [\App\Http\Controllers\Pembimbing\JurnalController::class, 'approve'])->name('jurnal.approve');
    
    // 🏢 Input Kunjungan
    Route::get('/kunjungan/create', [\App\Http\Controllers\Pembimbing\KunjunganController::class, 'create'])->name('kunjungan.create');
    Route::post('/kunjungan', [\App\Http\Controllers\Pembimbing\KunjunganController::class, 'store'])->name('kunjungan.store');
    Route::get('/kunjungan', [\App\Http\Controllers\Pembimbing\KunjunganController::class, 'index'])->name('kunjungan');
    Route::get('/kunjungan/{kunjungan}/edit', [\App\Http\Controllers\Pembimbing\KunjunganController::class, 'edit'])->name('kunjungan.edit');
    Route::put('/kunjungan/{kunjungan}', [\App\Http\Controllers\Pembimbing\KunjunganController::class, 'update'])->name('kunjungan.update');
    Route::delete('/kunjungan/{kunjungan}', [\App\Http\Controllers\Pembimbing\KunjunganController::class, 'destroy'])->name('kunjungan.destroy');
    
    // 🎯 Nilai Siswa
    Route::get('/nilai', [\App\Http\Controllers\Pembimbing\NilaiController::class, 'index'])->name('nilai.index');
    Route::get('/nilai/{siswa}', [\App\Http\Controllers\Pembimbing\NilaiController::class, 'create'])->name('nilai.create');
    Route::post('/nilai/{siswa}', [\App\Http\Controllers\Pembimbing\NilaiController::class, 'store'])->name('nilai.store');
    
    // Alias route untuk kompatibilitas view lama
    Route::get('/penilaian/{siswa}/final', [\App\Http\Controllers\Pembimbing\NilaiController::class, 'create'])->name('penilaian.final');
    Route::post('/penilaian/{siswa}/final', [\App\Http\Controllers\Pembimbing\NilaiController::class, 'store'])->name('penilaian.final.store');
    
    // 📄 Laporan
    Route::get('/laporan', [\App\Http\Controllers\Pembimbing\LaporanController::class, 'index'])->name('laporan');
    Route::get('/laporan/export', [\App\Http\Controllers\Pembimbing\LaporanController::class, 'export'])->name('laporan.export');
    
}); // ✅ Tutup group pembimbing

// =================================================================
// 🔷 SISWA ROUTES
// =================================================================
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    
    // Dashboard Siswa
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
    
    // Absensi
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/create', [AbsensiController::class, 'create'])->name('absensi.create');
    Route::post('/absensi', [AbsensiController::class, 'store'])->name('absensi.store');
    
    // Jurnal Harian
    Route::get('/jurnal', [JurnalController::class, 'index'])->name('jurnal.index');
    Route::post('/jurnal', [JurnalController::class, 'store'])->name('jurnal.store');
    Route::get('/jurnal/{jurnal}/edit', [JurnalController::class, 'edit'])->name('jurnal.edit');
    Route::put('/jurnal/{jurnal}', [JurnalController::class, 'update'])->name('jurnal.update');
    
    // 🔥 BARU: Data Perusahaan PKL (Siswa Input)
    Route::get('/perusahaan', [SiswaPerusahaanController::class, 'index'])->name('perusahaan');
    Route::post('/perusahaan', [SiswaPerusahaanController::class, 'store'])->name('perusahaan.store');
    
}); // ✅ Tutup group siswa

// 👤 PROFILE ROUTES
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});