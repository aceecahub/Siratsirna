<?php

use App\Http\Controllers\AssistanceLetterController;
use App\Http\Controllers\DomicileLetterController;
use App\Http\Controllers\HeirLetterController;
use App\Http\Controllers\IncomingMailController;
use App\Http\Controllers\IncomeLetterController;
use App\Http\Controllers\KtpLetterController;
use App\Http\Controllers\LandingSettingController;
use App\Http\Controllers\LossLetterController;
use App\Http\Controllers\MarriageLetterController;
use App\Http\Controllers\OutgoingMailController;
use App\Http\Controllers\PovertyLetterController;
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

    // Generate chart data for the last 6 months
    $months = collect(range(5, 0))->map(function ($i) use ($now) {
        return $now->copy()->subMonths($i);
    });

    $chartCategories = $months->map->format('M Y')->toArray();

    $incomingCounts = $months->map(function ($month) {
        return \App\Models\IncomingMail::whereYear('tanggal_diterima', $month->year)
            ->whereMonth('tanggal_diterima', $month->month)
            ->count();
    })->toArray();

    $outgoingCounts = $months->map(function ($month) {
        return \App\Models\OutgoingMail::whereYear('tanggal_surat', $month->year)
            ->whereMonth('tanggal_surat', $month->month)
            ->count();
    })->toArray();

    // Combined counts for Surat Keterangan Warga (Domisili, SKTM, Menikah, E-KTP, Kehilangan, Penghasilan, Ahli Waris, Bantuan)
    $keteranganCounts = $months->map(function($month) {
        $y = $month->year;
        $m = $month->month;
        return \App\Models\DomicileLetter::whereYear('created_at', $y)->whereMonth('created_at', $m)->count() +
            \App\Models\PovertyLetter::whereYear('created_at', $y)->whereMonth('created_at', $m)->count() +
            \App\Models\MarriageLetter::whereYear('created_at', $y)->whereMonth('created_at', $m)->count() +
            \App\Models\KtpLetter::whereYear('created_at', $y)->whereMonth('created_at', $m)->count() +
            \App\Models\LossLetter::whereYear('created_at', $y)->whereMonth('created_at', $m)->count() +
            \App\Models\IncomeLetter::whereYear('created_at', $y)->whereMonth('created_at', $m)->count() +
            \App\Models\HeirLetter::whereYear('created_at', $y)->whereMonth('created_at', $m)->count() +
            \App\Models\AssistanceLetter::whereYear('created_at', $y)->whereMonth('created_at', $m)->count();
    })->toArray();

    $chartData = [
        'categories' => $chartCategories,
        'series' => [
            [
                'name' => 'Surat Keluar Utama',
                'data' => $outgoingCounts
            ],
            [
                'name' => 'Surat Masuk',
                'data' => $incomingCounts
            ],
            [
                'name' => 'Surat Keterangan Warga',
                'data' => $keteranganCounts
            ]
        ]
    ];

    // Breakdown for Donut Chart (Jenis Surat Keterangan)
    $breakdownStats = [
        'Domisili'   => \App\Models\DomicileLetter::count(),
        'SKTM'       => \App\Models\PovertyLetter::count(),
        'Nikah'      => \App\Models\MarriageLetter::count(),
        'E-KTP'      => \App\Models\KtpLetter::count(),
        'Kehilangan' => \App\Models\LossLetter::count(),
        'Penghasilan'=> \App\Models\IncomeLetter::count(),
        'Ahli Waris' => \App\Models\HeirLetter::count(),
        'Bantuan'    => \App\Models\AssistanceLetter::count(),
    ];

    $breakdownChartData = [
        'labels' => array_keys($breakdownStats),
        'series' => array_values($breakdownStats)
    ];

    // Stacked Bar Chart for Monthly Breakdown of 8 Letters
    $stackedCategories = $chartCategories;
    $modelsMap = [
        'Domisili'   => \App\Models\DomicileLetter::class,
        'SKTM'       => \App\Models\PovertyLetter::class,
        'Nikah'      => \App\Models\MarriageLetter::class,
        'E-KTP'      => \App\Models\KtpLetter::class,
        'Kehilangan' => \App\Models\LossLetter::class,
        'Penghasilan'=> \App\Models\IncomeLetter::class,
        'Ahli Waris' => \App\Models\HeirLetter::class,
        'Bantuan'    => \App\Models\AssistanceLetter::class,
    ];
    
    $stackedSeries = [];
    foreach ($modelsMap as $label => $modelClass) {
        $monthlyCounts = $months->map(function($month) use ($modelClass) {
            return $modelClass::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        })->toArray();
        $stackedSeries[] = [
            'name' => $label,
            'data' => $monthlyCounts
        ];
    }

    $stackedChartData = [
        'categories' => $stackedCategories,
        'series'     => $stackedSeries
    ];

    $totalSuratKeterangan = array_sum($breakdownStats);

    // Stats per jenis surat keterangan bulan ini
    $keteranganBulanIni = [
        'Domisili'   => \App\Models\DomicileLetter::whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)->count(),
        'SKTM'       => \App\Models\PovertyLetter::whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)->count(),
        'Nikah'      => \App\Models\MarriageLetter::whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)->count(),
        'E-KTP'      => \App\Models\KtpLetter::whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)->count(),
        'Kehilangan' => \App\Models\LossLetter::whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)->count(),
        'Penghasilan'=> \App\Models\IncomeLetter::whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)->count(),
        'Ahli Waris' => \App\Models\HeirLetter::whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)->count(),
        'Bantuan'    => \App\Models\AssistanceLetter::whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)->count(),
    ];

    $suratKeteranganBulanIni = array_sum($keteranganBulanIni);

    // 5 surat keterangan terbaru (gabungan semua jenis)
    $recentLetters = collect([
        ...\App\Models\DomicileLetter::latest()->take(5)->get()->map(fn($r) => ['jenis' => 'Domisili', 'nama' => $r->nama ?? '-', 'created_at' => $r->created_at, 'route' => 'surat-keterangan-domisili']),
        ...\App\Models\PovertyLetter::latest()->take(5)->get()->map(fn($r) => ['jenis' => 'SKTM', 'nama' => $r->nama ?? '-', 'created_at' => $r->created_at, 'route' => 'surat-tidak-mampu']),
        ...\App\Models\MarriageLetter::latest()->take(5)->get()->map(fn($r) => ['jenis' => 'Nikah', 'nama' => $r->nama ?? '-', 'created_at' => $r->created_at, 'route' => 'surat-keterangan-menikah']),
        ...\App\Models\KtpLetter::latest()->take(5)->get()->map(fn($r) => ['jenis' => 'E-KTP', 'nama' => $r->nama ?? '-', 'created_at' => $r->created_at, 'route' => 'surat-pengantar-ktp']),
        ...\App\Models\LossLetter::latest()->take(5)->get()->map(fn($r) => ['jenis' => 'Kehilangan', 'nama' => $r->nama ?? '-', 'created_at' => $r->created_at, 'route' => 'surat-keterangan-kehilangan']),
        ...\App\Models\IncomeLetter::latest()->take(5)->get()->map(fn($r) => ['jenis' => 'Penghasilan', 'nama' => $r->nama ?? '-', 'created_at' => $r->created_at, 'route' => 'surat-keterangan-penghasilan']),
        ...\App\Models\HeirLetter::latest()->take(5)->get()->map(fn($r) => ['jenis' => 'Ahli Waris', 'nama' => $r->nama ?? '-', 'created_at' => $r->created_at, 'route' => 'surat-ahli-waris']),
        ...\App\Models\AssistanceLetter::latest()->take(5)->get()->map(fn($r) => ['jenis' => 'Bantuan', 'nama' => $r->nama ?? '-', 'created_at' => $r->created_at, 'route' => 'surat-permohonan-bantuan']),
    ])->sortByDesc('created_at')->take(5)->values()->toArray();

    return Inertia::render('Dashboard', [
        'stats' => [
            'totalSuratMasuk'         => \App\Models\IncomingMail::count(),
            'totalSuratKeluar'        => \App\Models\OutgoingMail::count(),
            'totalSuratKeterangan'    => $totalSuratKeterangan,
            'suratMasukBulanIni'      => \App\Models\IncomingMail::whereYear('tanggal_diterima', $now->year)
                ->whereMonth('tanggal_diterima', $now->month)
                ->count(),
            'suratKeluarBulanIni'     => \App\Models\OutgoingMail::whereYear('tanggal_surat', $now->year)
                ->whereMonth('tanggal_surat', $now->month)
                ->count(),
            'suratKeteranganBulanIni' => $suratKeteranganBulanIni,
            'totalSemuaSurat'         => \App\Models\IncomingMail::count() + \App\Models\OutgoingMail::count() + $totalSuratKeterangan,
            'breakdown'               => $breakdownStats,
            'keteranganBulanIni'      => $keteranganBulanIni,
        ],
        'recentOutgoing'     => \App\Http\Resources\OutgoingMailResource::collection(
            \App\Models\OutgoingMail::latest()->take(5)->get()
        ),
        'recentIncoming'     => \App\Models\IncomingMail::latest()->take(5)->get(),
        'recentLetters'      => $recentLetters,
        'chartData'          => $chartData,
        'breakdownChartData' => $breakdownChartData,
        'stackedChartData'   => $stackedChartData,
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

    // Surat Keterangan Domisili
    Route::get('/surat-keterangan-domisili', [DomicileLetterController::class, 'index'])->name('surat-keterangan-domisili.index');
    Route::get('/surat-keterangan-domisili/buat', [DomicileLetterController::class, 'create'])->name('surat-keterangan-domisili.create');
    Route::post('/surat-keterangan-domisili', [DomicileLetterController::class, 'store'])->name('surat-keterangan-domisili.store');
    Route::get('/surat-keterangan-domisili/{domicileLetter}', [DomicileLetterController::class, 'show'])->name('surat-keterangan-domisili.show');
    Route::delete('/surat-keterangan-domisili/{domicileLetter}', [DomicileLetterController::class, 'destroy'])->name('surat-keterangan-domisili.destroy');

    // Surat Keterangan Tidak Mampu
    Route::get('/surat-tidak-mampu', [PovertyLetterController::class, 'index'])->name('surat-tidak-mampu.index');
    Route::get('/surat-tidak-mampu/buat', [PovertyLetterController::class, 'create'])->name('surat-tidak-mampu.create');
    Route::post('/surat-tidak-mampu', [PovertyLetterController::class, 'store'])->name('surat-tidak-mampu.store');
    Route::get('/surat-tidak-mampu/{povertyLetter}', [PovertyLetterController::class, 'show'])->name('surat-tidak-mampu.show');
    Route::delete('/surat-tidak-mampu/{povertyLetter}', [PovertyLetterController::class, 'destroy'])->name('surat-tidak-mampu.destroy');

    // Surat Keterangan Menikah
    Route::get('/surat-keterangan-menikah', [MarriageLetterController::class, 'index'])->name('surat-keterangan-menikah.index');
    Route::get('/surat-keterangan-menikah/buat', [MarriageLetterController::class, 'create'])->name('surat-keterangan-menikah.create');
    Route::post('/surat-keterangan-menikah', [MarriageLetterController::class, 'store'])->name('surat-keterangan-menikah.store');
    Route::get('/surat-keterangan-menikah/{marriageLetter}', [MarriageLetterController::class, 'show'])->name('surat-keterangan-menikah.show');
    Route::delete('/surat-keterangan-menikah/{marriageLetter}', [MarriageLetterController::class, 'destroy'])->name('surat-keterangan-menikah.destroy');

    // Surat Pengantar Pencetakan E-KTP
    Route::get('/surat-pengantar-ktp', [KtpLetterController::class, 'index'])->name('surat-pengantar-ktp.index');
    Route::get('/surat-pengantar-ktp/buat', [KtpLetterController::class, 'create'])->name('surat-pengantar-ktp.create');
    Route::post('/surat-pengantar-ktp', [KtpLetterController::class, 'store'])->name('surat-pengantar-ktp.store');
    Route::get('/surat-pengantar-ktp/{ktpLetter}', [KtpLetterController::class, 'show'])->name('surat-pengantar-ktp.show');
    Route::delete('/surat-pengantar-ktp/{ktpLetter}', [KtpLetterController::class, 'destroy'])->name('surat-pengantar-ktp.destroy');

    // Surat Keterangan Kehilangan
    Route::get('/surat-keterangan-kehilangan', [LossLetterController::class, 'index'])->name('surat-keterangan-kehilangan.index');
    Route::get('/surat-keterangan-kehilangan/buat', [LossLetterController::class, 'create'])->name('surat-keterangan-kehilangan.create');
    Route::post('/surat-keterangan-kehilangan', [LossLetterController::class, 'store'])->name('surat-keterangan-kehilangan.store');
    Route::get('/surat-keterangan-kehilangan/{lossLetter}', [LossLetterController::class, 'show'])->name('surat-keterangan-kehilangan.show');
    Route::delete('/surat-keterangan-kehilangan/{lossLetter}', [LossLetterController::class, 'destroy'])->name('surat-keterangan-kehilangan.destroy');

    // Surat Keterangan Penghasilan
    Route::get('/surat-keterangan-penghasilan', [IncomeLetterController::class, 'index'])->name('surat-keterangan-penghasilan.index');
    Route::get('/surat-keterangan-penghasilan/buat', [IncomeLetterController::class, 'create'])->name('surat-keterangan-penghasilan.create');
    Route::post('/surat-keterangan-penghasilan', [IncomeLetterController::class, 'store'])->name('surat-keterangan-penghasilan.store');
    Route::get('/surat-keterangan-penghasilan/{incomeLetter}', [IncomeLetterController::class, 'show'])->name('surat-keterangan-penghasilan.show');
    Route::delete('/surat-keterangan-penghasilan/{incomeLetter}', [IncomeLetterController::class, 'destroy'])->name('surat-keterangan-penghasilan.destroy');

    // Surat Keterangan Ahli Waris Dan Saksi Ahli Waris
    Route::get('/surat-ahli-waris', [HeirLetterController::class, 'index'])->name('surat-ahli-waris.index');
    Route::get('/surat-ahli-waris/buat', [HeirLetterController::class, 'create'])->name('surat-ahli-waris.create');
    Route::post('/surat-ahli-waris', [HeirLetterController::class, 'store'])->name('surat-ahli-waris.store');
    Route::get('/surat-ahli-waris/{heirLetter}', [HeirLetterController::class, 'show'])->name('surat-ahli-waris.show');
    Route::delete('/surat-ahli-waris/{heirLetter}', [HeirLetterController::class, 'destroy'])->name('surat-ahli-waris.destroy');

    // Surat Permohonan Bantuan
    Route::get('/surat-permohonan-bantuan', [AssistanceLetterController::class, 'index'])->name('surat-permohonan-bantuan.index');
    Route::get('/surat-permohonan-bantuan/buat', [AssistanceLetterController::class, 'create'])->name('surat-permohonan-bantuan.create');
    Route::post('/surat-permohonan-bantuan', [AssistanceLetterController::class, 'store'])->name('surat-permohonan-bantuan.store');
    Route::get('/surat-permohonan-bantuan/{assistanceLetter}', [AssistanceLetterController::class, 'show'])->name('surat-permohonan-bantuan.show');
    Route::delete('/surat-permohonan-bantuan/{assistanceLetter}', [AssistanceLetterController::class, 'destroy'])->name('surat-permohonan-bantuan.destroy');

    // CMS Landing Page Settings
    Route::get('/landing-setting', [LandingSettingController::class, 'edit'])->name('landing-setting.edit');
    Route::post('/landing-setting', [LandingSettingController::class, 'update'])->name('landing-setting.update');
});

require __DIR__ . '/auth.php';
