<?php

namespace Database\Seeders;

use App\Models\LandingSetting;
use Illuminate\Database\Seeder;

class LandingSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            'village_name' => 'Desa Sirnaputra',
            'subdistrict_name' => 'Kecamatan Cigalontang',
            'regency_name' => 'Kabupaten Tasikmalaya',
            'province_name' => 'Provinsi Jawa Barat',

            'hero_badge' => 'Portal Resmi & Layanan Digital Warga Desa Sirnaputra',
            'hero_title' => 'Harmoni Alam, Kemajuan Digital, & Kesejahteraan Warga',
            'hero_subtitle' => 'Selamat datang di portal informasi dan pelayanan publik Desa Sirnaputra, Kecamatan Cigalontang, Kabupaten Tasikmalaya. Mewujudkan tata kelola desa yang transparan, maju, mandiri, dan berbudaya.',
            'hero_image' => '/images/desa_sirnaputra_hero.png',
            'hero_cta_primary' => 'Layanan Administrasi Surat',
            'hero_cta_secondary' => 'Jelajahi Profil Desa',

            'stats' => [
                'penduduk' => '4.852',
                'kk' => '1.240',
                'dusun' => '6',
                'rt_rw' => '24 / 8',
            ],

            'kades_name' => 'H. Ahmad Supriatna',
            'kades_position' => 'Kepala Desa Sirnaputra',
            'kades_period' => 'Masa Jabatan 2021 - 2027',
            'kades_photo' => null,
            'kades_speech' => "Assalamu’alaikum Warahmatullahi Wabarakatuh. Sampurasun! Selamat datang di portal digital Desa Sirnaputra. Website ini kami dedikasikan sebagai wujud keterbukaan informasi publik dan kemudahan akses layanan administrasi bagi seluruh warga Desa Sirnaputra, baik yang berdomisili di desa maupun di perantauan.",

            'visi_misi_pillars' => [
                [
                    'icon' => '🌿',
                    'title' => 'Pertanian & Agrowisata',
                    'desc' => 'Pengembangan komoditas padi & hasil bumi berkelanjutan.',
                ],
                [
                    'icon' => '⚡',
                    'title' => 'E-Gov Persuratan',
                    'desc' => 'Pengurusan surat online cepat & tanpa birokrasi rumit.',
                ],
                [
                    'icon' => '🤝',
                    'title' => 'Pemberdayaan UMKM',
                    'desc' => 'Dukungan modal & pemasaran produk usaha warga.',
                ],
                [
                    'icon' => '🏛️',
                    'title' => 'Transparansi Dana Desa',
                    'desc' => 'Akuntabilitas anggaran pembangunan publik.',
                ],
            ],

            'services' => [
                [
                    'id' => 1,
                    'icon' => '📝',
                    'title' => 'Surat Keterangan Usaha (SKU)',
                    'desc' => 'Dokumen resmi untuk verifikasi usaha warga dalam rangka pengajuan KUR, kredit mikro, atau legalitas bisnis.',
                    'estimate' => 'Estimasi: 1 hari kerja',
                ],
                [
                    'id' => 2,
                    'icon' => '🏠',
                    'title' => 'Surat Keterangan Domisili',
                    'desc' => 'Surat bukti tempat tinggal bagi warga lokal maupun pendatang di wilayah RT/RW Desa Sirnaputra.',
                    'estimate' => 'Estimasi: Langsung (Satu Tempat)',
                ],
                [
                    'id' => 3,
                    'icon' => '🎓',
                    'title' => 'Surat Keterangan Tidak Mampu (SKTM)',
                    'desc' => 'Persyaratan rujukan beasiswa sekolah, bantuan BPJS KIS kesehatan, dan keringanan biaya sosial.',
                    'estimate' => 'Estimasi: 1 hari kerja',
                ],
                [
                    'id' => 4,
                    'icon' => '📜',
                    'title' => 'Surat Pengantar KTP / KK',
                    'desc' => 'Pengantar pembuatan atau pembaharuan dokumen identitas kependudukan ke Kecamatan Cigalontang.',
                    'estimate' => 'Estimasi: Diproses Cepat',
                ],
                [
                    'id' => 5,
                    'icon' => '✉️',
                    'title' => 'Surat Keterangan Kematian / Kelahiran',
                    'desc' => 'Pencatatan mutasi data keluarga resmi untuk pembaruan Kartu Keluarga & pencatatan sipil.',
                    'estimate' => 'Estimasi: 1 hari kerja',
                ],
                [
                    'id' => 6,
                    'icon' => '🛡️',
                    'title' => 'Aspirasi & Pengaduan Warga',
                    'desc' => 'Kanal resmi penyampaian saran, kritik, dan laporan kejadian fasilitas umum ke Balai Desa Sirnaputra.',
                    'estimate' => 'Respons Mandiri WA',
                ],
            ],

            'potentials' => [
                [
                    'id' => 1,
                    'icon' => '🌾',
                    'category' => 'Sektor Pertanian',
                    'title' => 'Padi Organik Terasering',
                    'desc' => 'Lahan pertanian sawah terasering yang subur menghasilkan beras kualitas tinggi.',
                ],
                [
                    'id' => 2,
                    'icon' => '☕',
                    'category' => 'Perkebunan',
                    'title' => 'Kopi & Teh Pegunungan',
                    'desc' => 'Hasil kebun lereng gunung Cigalontang dengan aroma khas dan cita rasa autentik.',
                ],
                [
                    'id' => 3,
                    'icon' => '🧺',
                    'category' => 'UMKM Kerajinan',
                    'title' => 'Anyaman Bambu Tradisional',
                    'desc' => 'Produk ekonomi kreatif hasil olah tangan perajin lokal siap dipasarkan secara luas.',
                ],
                [
                    'id' => 4,
                    'icon' => '🏞️',
                    'category' => 'Pariwisata Alam',
                    'title' => 'Wisata Curug & Panorama',
                    'desc' => 'Destinasi alam asri dan udara sejuk pegunungan yang memikat para wisatawan.',
                ],
            ],

            'news' => [
                [
                    'id' => 1,
                    'category' => 'Pemerintahan',
                    'date' => '01 Agustus 2026',
                    'title' => 'Penyaluran Bantuan Langsung Tunai (BLT) Dana Desa Tahap III Berjalan Lancar',
                    'excerpt' => 'Pemerintah Desa Sirnaputra telah merampungkan penyaluran BLT Dana Desa kepada Keluarga Penerima Manfaat (KPM) bertempat di Balai Desa Sirnaputra.',
                ],
                [
                    'id' => 2,
                    'category' => 'KKN & Pemberdayaan',
                    'date' => '28 Juli 2026',
                    'title' => 'Pelatihan Digitalisasi Administrasi & Pemasaran UMKM Bersama Mahasiswa KKN',
                    'excerpt' => 'Program KKN perguruan tinggi berkolaborasi dengan perangkat desa dalam mengedukasi pelaku usaha mikro mengenai branding digital dan persuratan mandiri.',
                ],
                [
                    'id' => 3,
                    'category' => 'Gotong Royong',
                    'date' => '20 Juli 2026',
                    'title' => 'Giat Kerja Bakti Masal Perbaikan Saluran Irigasi Sawah Dusun II',
                    'excerpt' => 'Ratusan warga secara berswadaya memperbaiki alur irigasi guna mengantisipasi musim tanam padi dan memastikan pasokan air lancar.',
                ],
            ],

            'officers' => [
                [
                    'id' => 1,
                    'icon' => '👔',
                    'name' => 'H. Ahmad Supriatna',
                    'position' => 'Kepala Desa',
                    'photo' => null,
                ],
                [
                    'id' => 2,
                    'icon' => '📋',
                    'name' => 'Ridwan Permana, S.IP',
                    'position' => 'Sekretaris Desa (Sekdes)',
                    'photo' => null,
                ],
                [
                    'id' => 3,
                    'icon' => '💳',
                    'name' => 'Siti Nurhaliza, S.E',
                    'position' => 'Kaur Keuangan',
                    'photo' => null,
                ],
                [
                    'id' => 4,
                    'icon' => '🤝',
                    'name' => 'Dedi Kurniawan, S.Sos',
                    'position' => 'Kasi Pelayanan',
                    'photo' => null,
                ],
            ],

            'balai_desa_title' => 'Balai Desa Sirnaputra',
            'balai_desa_desc' => 'Gedung Balai Desa Sirnaputra dirancang ramah publik dengan ruang pelayanan persuratan terpadu, aula kemasyarakatan, dan akses jaringan internet terbuka untuk mendukung aktivitas masyarakat.',
            'balai_desa_photo' => '/images/balai_desa.png',

            'contact_address' => 'Jl. Raya Sirnaputra No. 01, Desa Sirnaputra, Kec. Cigalontang, Kabupaten Tasikmalaya, Jawa Barat 46464.',
            'contact_hours' => "Senin - Jumat: 08:00 - 15:30 WIB\nSabtu & Minggu: Libur (Layanan Online Tetap Aktif)",
            'contact_phone' => '0812-3456-7890',
            'contact_email' => 'pemdes@sirnaputra.desa.id',
            'contact_maps_url' => 'https://maps.google.com/maps?q=Cigalontang%20Tasikmalaya&t=&z=13&ie=UTF8&iwloc=&output=embed',
        ];

        foreach ($defaultSettings as $key => $value) {
            LandingSetting::setValue($key, $value);
        }
    }
}
