import fs from 'fs';

const path = 'routes/web.php';
let content = fs.readFileSync(path, 'utf8');

// The new logic to inject
const newLogic = `
    $outgoingCounts = $months->map(function ($month) {
        return \\App\\Models\\OutgoingMail::whereYear('tanggal_surat', $month->year)
            ->whereMonth('tanggal_surat', $month->month)
            ->count();
    })->toArray();

    // Combined counts for Surat Keterangan Warga (Domisili, SKTM, Menikah, E-KTP, Kehilangan, Penghasilan, Ahli Waris, Bantuan)
    $keteranganCounts = $months->map(function($month) {
        $y = $month->year;
        $m = $month->month;
        return \\App\\Models\\DomicileLetter::whereYear('created_at', $y)->whereMonth('created_at', $m)->count() +
            \\App\\Models\\PovertyLetter::whereYear('created_at', $y)->whereMonth('created_at', $m)->count() +
            \\App\\Models\\MarriageLetter::whereYear('created_at', $y)->whereMonth('created_at', $m)->count() +
            \\App\\Models\\KtpLetter::whereYear('created_at', $y)->whereMonth('created_at', $m)->count() +
            \\App\\Models\\LossLetter::whereYear('created_at', $y)->whereMonth('created_at', $m)->count() +
            \\App\\Models\\IncomeLetter::whereYear('created_at', $y)->whereMonth('created_at', $m)->count() +
            \\App\\Models\\HeirLetter::whereYear('created_at', $y)->whereMonth('created_at', $m)->count() +
            \\App\\Models\\AssistanceLetter::whereYear('created_at', $y)->whereMonth('created_at', $m)->count();
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
        'Domisili' => \\App\\Models\\DomicileLetter::count(),
        'SKTM' => \\App\\Models\\PovertyLetter::count(),
        'Nikah' => \\App\\Models\\MarriageLetter::count(),
        'E-KTP' => \\App\\Models\\KtpLetter::count(),
        'Kehilangan' => \\App\\Models\\LossLetter::count(),
        'Penghasilan' => \\App\\Models\\IncomeLetter::count(),
        'Ahli Waris' => \\App\\Models\\HeirLetter::count(),
        'Bantuan' => \\App\\Models\\AssistanceLetter::count(),
    ];

    $breakdownChartData = [
        'labels' => array_keys($breakdownStats),
        'series' => array_values($breakdownStats)
    ];

    $totalSuratKeterangan = array_sum($breakdownStats);

    return Inertia::render('Dashboard', [
        'stats' => [
            'totalSuratMasuk' => \\App\\Models\\IncomingMail::count(),
            'totalSuratKeluar' => \\App\\Models\\OutgoingMail::count(),
            'totalSuratKeterangan' => $totalSuratKeterangan,
            'suratMasukBulanIni' => \\App\\Models\\IncomingMail::whereYear('tanggal_diterima', $now->year)
                ->whereMonth('tanggal_diterima', $now->month)
                ->count(),
            'suratKeluarBulanIni' => \\App\\Models\\OutgoingMail::whereYear('tanggal_surat', $now->year)
                ->whereMonth('tanggal_surat', $now->month)
                ->count(),
            'breakdown' => $breakdownStats,
        ],
        'recentOutgoing' => \\App\\Http\\Resources\\OutgoingMailResource::collection(
            \\App\\Models\\OutgoingMail::latest()->take(5)->get()
        ),
        'recentIncoming' => \\App\\Models\\IncomingMail::latest()->take(5)->get(),
        'chartData' => $chartData,
        'breakdownChartData' => $breakdownChartData,
    ]);
`;

// Locate the outgoingCounts definition to replace everything until the end of the dashboard route
const startTarget = "$outgoingCounts = $months->map(function ($month) {";
const endTarget = "})->middleware(['auth', 'verified'])->name('dashboard');";

const startIndex = content.indexOf(startTarget);
const endIndex = content.indexOf(endTarget);

if (startIndex !== -1 && endIndex !== -1) {
    content = content.substring(0, startIndex) + newLogic.trim() + "\\n" + content.substring(endIndex);
    fs.writeFileSync(path, content);
    console.log("Updated web.php successfully.");
} else {
    console.log("Could not find targets to replace.");
}
