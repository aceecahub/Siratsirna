<?php

use App\Http\Controllers\IncomingMailController;
use App\Http\Controllers\LandingSettingController;
use App\Http\Controllers\OutgoingMailController;
use App\Http\Controllers\ProfileController;
use App\Models\LandingSetting;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $settings = LandingSetting::all()->pluck('value', 'key')->map(function ($val) {
        $decoded = json_decode($val, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $val;
    })->toArray();

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'landingSettings' => $settings,
    ]);
});

Route::get('/dashboard', function () {
    $now = now();

    return Inertia::render('Dashboard', [
        'stats' => [
            'totalSuratMasuk' => \App\Models\IncomingMail::count(),
            'totalSuratKeluar' => \App\Models\OutgoingMail::count(),
            'suratMasukBulanIni' => \App\Models\IncomingMail::whereYear('tanggal_diterima', $now->year)
                ->whereMonth('tanggal_diterima', $now->month)
                ->count(),
            'suratKeluarBulanIni' => \App\Models\OutgoingMail::whereYear('tanggal_surat', $now->year)
                ->whereMonth('tanggal_surat', $now->month)
                ->count(),
        ],
        'recentOutgoing' => \App\Http\Resources\OutgoingMailResource::collection(
            \App\Models\OutgoingMail::latest()->take(5)->get()
        ),
        'recentIncoming' => \App\Models\IncomingMail::latest()->take(5)->get(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/surat-masuk', [IncomingMailController::class, 'index'])->name('surat-masuk.index');
    Route::post('/surat-masuk', [IncomingMailController::class, 'store'])->name('surat-masuk.store');
    Route::delete('/surat-masuk/{incomingMail}', [IncomingMailController::class, 'destroy'])->name('surat-masuk.destroy');
    Route::get('/surat-masuk/{incomingMail}/unduh', [IncomingMailController::class, 'download'])->name('surat-masuk.download');

    Route::get('/surat-keluar', [OutgoingMailController::class, 'index'])->name('surat-keluar.index');
    Route::get('/surat-keluar/buat', [OutgoingMailController::class, 'create'])->name('surat-keluar.create');
    Route::post('/surat-keluar', [OutgoingMailController::class, 'store'])->name('surat-keluar.store');
    Route::get('/surat-keluar/{outgoingMail}', [OutgoingMailController::class, 'show'])->name('surat-keluar.show');
    Route::delete('/surat-keluar/{outgoingMail}', [OutgoingMailController::class, 'destroy'])->name('surat-keluar.destroy');

    // CMS Landing Page Settings
    Route::get('/landing-setting', [LandingSettingController::class, 'edit'])->name('landing-setting.edit');
    Route::post('/landing-setting', [LandingSettingController::class, 'update'])->name('landing-setting.update');
});

require __DIR__.'/auth.php';
