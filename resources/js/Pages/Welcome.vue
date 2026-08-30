<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const props = defineProps({
    canLogin: {
        type: Boolean,
        default: true,
    },
    laravelVersion: {
        type: String,
        default: '',
    },
    phpVersion: {
        type: String,
        default: '',
    },
    landingSettings: {
        type: Object,
        default: () => ({}),
    },
});

const mobileMenuOpen = ref(false);

function scrollToSection(id) {
    mobileMenuOpen.value = false;
    const el = document.getElementById(id);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth' });
    }
}

// Computed Dynamic Fallbacks
const villageName = computed(() => props.landingSettings?.village_name || 'Desa Sirnaputra');
const subdistrictName = computed(() => props.landingSettings?.subdistrict_name || 'Kecamatan Cigalontang');
const regencyName = computed(() => props.landingSettings?.regency_name || 'Kabupaten Tasikmalaya');
const heroTitle = computed(() => props.landingSettings?.hero_title || 'Harmoni Alam, Kemajuan Digital, & Kesejahteraan Warga');
const heroSubtitle = computed(() => props.landingSettings?.hero_subtitle || 'Selamat datang di portal informasi dan pelayanan publik Desa Sirnaputra, Kecamatan Cigalontang, Kabupaten Tasikmalaya. Mewujudkan tata kelola desa yang transparan, maju, mandiri, dan berbudaya.');
const heroImage = computed(() => props.landingSettings?.hero_image || '/images/desa_sirnaputra_hero.png');

const stats = computed(() => props.landingSettings?.stats || {
    penduduk: '4.852',
    kk: '1.240',
    dusun: '6',
    rt_rw: '24 / 8',
});

const kadesName = computed(() => props.landingSettings?.kades_name || 'H. Ahmad Supriatna');
const kadesPosition = computed(() => props.landingSettings?.kades_position || 'Kepala Desa Sirnaputra');
const kadesPeriod = computed(() => props.landingSettings?.kades_period || 'Masa Jabatan 2021 - 2027');
const kadesPhoto = computed(() => props.landingSettings?.kades_photo || null);
const kadesSpeech = computed(() => props.landingSettings?.kades_speech || "Assalamu’alaikum Warahmatullahi Wabarakatuh. Sampurasun! Selamat datang di portal digital Desa Sirnaputra. Website ini kami dedikasikan sebagai wujud keterbukaan informasi publik dan kemudahan akses layanan administrasi bagi seluruh warga Desa Sirnaputra, baik yang berdomisili di desa maupun di perantauan.");

const servicesList = computed(() => props.landingSettings?.services || [
    {
        id: 1,
        icon: '📝',
        title: 'Surat Keterangan Usaha (SKU)',
        desc: 'Dokumen resmi untuk verifikasi usaha warga dalam rangka pengajuan KUR, kredit mikro, atau legalitas bisnis.',
        estimate: 'Estimasi: 1 hari kerja',
    },
    {
        id: 2,
        icon: '🏠',
        title: 'Surat Keterangan Domisili',
        desc: 'Surat bukti tempat tinggal bagi warga lokal maupun pendatang di wilayah RT/RW Desa Sirnaputra.',
        estimate: 'Estimasi: Langsung (Satu Tempat)',
    },
    {
        id: 3,
        icon: '🎓',
        title: 'Surat Keterangan Tidak Mampu (SKTM)',
        desc: 'Persyaratan rujukan beasiswa sekolah, bantuan BPJS KIS kesehatan, dan keringanan biaya sosial.',
        estimate: 'Estimasi: 1 hari kerja',
    },
    {
        id: 4,
        icon: '📜',
        title: 'Surat Pengantar KTP / KK',
        desc: 'Pengantar pembuatan atau pembaharuan dokumen identitas kependudukan ke Kecamatan Cigalontang.',
        estimate: 'Estimasi: Diproses Cepat',
    },
    {
        id: 5,
        icon: '✉️',
        title: 'Surat Keterangan Kematian / Kelahiran',
        desc: 'Pencatatan mutasi data keluarga resmi untuk pembaruan Kartu Keluarga & pencatatan sipil.',
        estimate: 'Estimasi: 1 hari kerja',
    },
    {
        id: 6,
        icon: '🛡️',
        title: 'Aspirasi & Pengaduan Warga',
        desc: 'Kanal resmi penyampaian saran, kritik, dan laporan kejadian fasilitas umum ke Balai Desa Sirnaputra.',
        estimate: 'Respons Mandiri WA',
    },
]);

const potentialsList = computed(() => props.landingSettings?.potentials || [
    {
        id: 1,
        icon: '🌾',
        category: 'Sektor Pertanian',
        title: 'Padi Organik Terasering',
        desc: 'Lahan pertanian sawah terasering yang subur menghasilkan beras kualitas tinggi.',
    },
    {
        id: 2,
        icon: '☕',
        category: 'Perkebunan',
        title: 'Kopi & Teh Pegunungan',
        desc: 'Hasil kebun lereng gunung Cigalontang dengan aroma khas dan cita rasa autentik.',
    },
    {
        id: 3,
        icon: '🧺',
        category: 'UMKM Kerajinan',
        title: 'Anyaman Bambu Tradisional',
        desc: 'Produk ekonomi kreatif hasil olah tangan perajin lokal siap dipasarkan secara luas.',
    },
    {
        id: 4,
        icon: '🏞️',
        category: 'Pariwisata Alam',
        title: 'Wisata Curug & Panorama',
        desc: 'Destinasi alam asri dan udara sejuk pegunungan yang memikat para wisatawan.',
    },
]);

const newsList = computed(() => props.landingSettings?.news || [
    {
        id: 1,
        category: 'Pemerintahan',
        date: '01 Agustus 2026',
        title: 'Penyaluran Bantuan Langsung Tunai (BLT) Dana Desa Tahap III Berjalan Lancar',
        excerpt: 'Pemerintah Desa Sirnaputra telah merampungkan penyaluran BLT Dana Desa kepada Keluarga Penerima Manfaat (KPM) bertempat di Balai Desa Sirnaputra.',
    },
    {
        id: 2,
        category: 'KKN & Pemberdayaan',
        date: '28 Juli 2026',
        title: 'Pelatihan Digitalisasi Administrasi & Pemasaran UMKM Bersama Mahasiswa KKN',
        excerpt: 'Program KKN perguruan tinggi berkolaborasi dengan perangkat desa dalam mengedukasi pelaku usaha mikro mengenai branding digital dan persuratan mandiri.',
    },
    {
        id: 3,
        category: 'Gotong Royong',
        date: '20 Juli 2026',
        title: 'Giat Kerja Bakti Masal Perbaikan Saluran Irigasi Sawah Dusun II',
        excerpt: 'Ratusan warga secara berswadaya memperbaiki alur irigasi guna mengantisipasi musim tanam padi dan memastikan pasokan air lancar.',
    },
]);

const officersList = computed(() => props.landingSettings?.officers || [
    {
        id: 1,
        icon: '👔',
        name: 'H. Ahmad Supriatna',
        position: 'Kepala Desa',
    },
    {
        id: 2,
        icon: '📋',
        name: 'Ridwan Permana, S.IP',
        position: 'Sekretaris Desa (Sekdes)',
    },
    {
        id: 3,
        icon: '💳',
        name: 'Siti Nurhaliza, S.E',
        position: 'Kaur Keuangan',
    },
    {
        id: 4,
        icon: '🤝',
        name: 'Dedi Kurniawan, S.Sos',
        position: 'Kasi Pelayanan',
    },
]);

const balaiDesaTitle = computed(() => props.landingSettings?.balai_desa_title || 'Balai Desa Sirnaputra');
const balaiDesaDesc = computed(() => props.landingSettings?.balai_desa_desc || 'Gedung Balai Desa Sirnaputra dirancang ramah publik dengan ruang pelayanan persuratan terpadu, aula kemasyarakatan, dan akses jaringan internet terbuka untuk mendukung aktivitas masyarakat.');
const balaiDesaPhoto = computed(() => props.landingSettings?.balai_desa_photo || '/images/balai_desa.png');

const contactAddress = computed(() => props.landingSettings?.contact_address || 'Jl. Raya Sirnaputra No. 01, Desa Sirnaputra, Kec. Cigalontang, Kabupaten Tasikmalaya, Jawa Barat 46464.');
const contactHours = computed(() => props.landingSettings?.contact_hours || 'Senin - Jumat: 08:00 - 15:30 WIB\nSabtu & Minggu: Libur (Layanan Online Tetap Aktif)');
const contactPhone = computed(() => props.landingSettings?.contact_phone || '0812-3456-7890');
const contactEmail = computed(() => props.landingSettings?.contact_email || 'pemdes@sirnaputra.desa.id');
const contactMapsUrl = computed(() => props.landingSettings?.contact_maps_url || 'https://maps.google.com/maps?q=Cigalontang%20Tasikmalaya&t=&z=13&ie=UTF8&iwloc=&output=embed');
</script>

<template>
    <Head :title="`Website Resmi ${villageName} - ${regencyName}`" />

    <div class="min-h-screen bg-slate-50 text-slate-900 selection:bg-emerald-500 selection:text-white dark:bg-slate-950 dark:text-slate-100 font-sans antialiased transition-colors duration-200">
        <!-- Top Decorative Accent Bar -->
        <div class="h-1.5 w-full bg-gradient-to-r from-emerald-600 via-teal-500 to-emerald-400"></div>

        <!-- Sticky Header / Navbar -->
        <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/85 backdrop-blur-md dark:border-slate-800/80 dark:bg-slate-900/85 transition-all shadow-sm">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
                <!-- Brand Logo -->
                <div class="flex items-center gap-3 cursor-pointer" @click="scrollToSection('hero')">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-tr from-emerald-600 via-teal-600 to-emerald-500 text-white shadow-md shadow-emerald-500/20 ring-1 ring-emerald-400/30">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-base font-black tracking-tight bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 bg-clip-text text-transparent dark:from-white dark:via-slate-100 dark:to-emerald-300 uppercase">
                            {{ villageName }}
                        </span>
                        <span class="block text-[10px] font-extrabold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                            {{ subdistrictName }}
                        </span>
                    </div>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden items-center gap-6 lg:flex">
                    <button @click="scrollToSection('hero')" class="text-xs font-bold text-slate-700 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400 transition">
                        Beranda
                    </button>
                    <button @click="scrollToSection('profil')" class="text-xs font-bold text-slate-700 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400 transition">
                        Profil Desa
                    </button>
                    <button @click="scrollToSection('layanan')" class="text-xs font-bold text-slate-700 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400 transition">
                        Layanan Digital
                    </button>
                    <button @click="scrollToSection('potensi')" class="text-xs font-bold text-slate-700 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400 transition">
                        Potensi & Wisata
                    </button>
                    <button @click="scrollToSection('berita')" class="text-xs font-bold text-slate-700 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400 transition">
                        Berita Desa
                    </button>
                    <button @click="scrollToSection('perangkat')" class="text-xs font-bold text-slate-700 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400 transition">
                        Perangkat Desa
                    </button>
                    <button @click="scrollToSection('kontak')" class="text-xs font-bold text-slate-700 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400 transition">
                        Kontak & Peta
                    </button>
                </nav>

                <!-- Action Buttons & Dark Toggle -->
                <div class="hidden items-center gap-3 lg:flex">
                    <ThemeToggle />
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center gap-2 lg:hidden">
                    <ThemeToggle compact />
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="rounded-xl border border-slate-200 p-2 text-slate-700 hover:bg-slate-100 dark:border-slate-800 dark:text-slate-300 dark:hover:bg-slate-800 transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                            <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Dropdown Nav -->
            <div v-if="mobileMenuOpen" class="border-b border-slate-200 bg-white px-4 pt-3 pb-5 dark:border-slate-800 dark:bg-slate-900 lg:hidden space-y-3">
                <button @click="scrollToSection('hero')" class="block w-full text-left py-2 text-xs font-bold text-slate-700 dark:text-slate-300">
                    Beranda
                </button>
                <button @click="scrollToSection('profil')" class="block w-full text-left py-2 text-xs font-bold text-slate-700 dark:text-slate-300">
                    Profil Desa
                </button>
                <button @click="scrollToSection('layanan')" class="block w-full text-left py-2 text-xs font-bold text-slate-700 dark:text-slate-300">
                    Layanan Digital
                </button>
                <button @click="scrollToSection('potensi')" class="block w-full text-left py-2 text-xs font-bold text-slate-700 dark:text-slate-300">
                    Potensi & Wisata
                </button>
                <button @click="scrollToSection('berita')" class="block w-full text-left py-2 text-xs font-bold text-slate-700 dark:text-slate-300">
                    Berita Desa
                </button>
                <button @click="scrollToSection('perangkat')" class="block w-full text-left py-2 text-xs font-bold text-slate-700 dark:text-slate-300">
                    Perangkat Desa
                </button>
                <button @click="scrollToSection('kontak')" class="block w-full text-left py-2 text-xs font-bold text-slate-700 dark:text-slate-300">
                    Kontak & Peta
                </button>
            </div>
        </header>

        <!-- 1. HERO SECTION -->
        <section id="hero" class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28">
            <div class="pointer-events-none absolute top-0 right-1/4 h-96 w-96 rounded-full bg-emerald-400/15 blur-3xl dark:bg-emerald-500/10"></div>
            <div class="pointer-events-none absolute bottom-0 left-1/3 h-96 w-96 rounded-full bg-teal-400/15 blur-3xl dark:bg-teal-500/10"></div>

            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid items-center gap-12 lg:grid-cols-12">
                    <!-- Left Hero Content -->
                    <div class="lg:col-span-7 space-y-6 text-left">

                        <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-slate-900 dark:text-white leading-[1.15]">
                            {{ heroTitle }}
                        </h1>

                        <p class="text-sm sm:text-base font-normal text-slate-600 dark:text-slate-300 leading-relaxed max-w-2xl">
                            {{ heroSubtitle }}
                        </p>

                        <div class="flex flex-wrap items-center gap-4 pt-2">
                            <button
                                @click="scrollToSection('layanan')"
                                class="inline-flex items-center gap-2.5 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-500 px-6 py-3.5 text-xs font-bold text-white shadow-xl shadow-emerald-600/25 transition hover:from-emerald-500 hover:to-teal-500 active:scale-95 ring-1 ring-emerald-400/30 cursor-pointer"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                Layanan Administrasi Surat
                            </button>

                            <button
                                @click="scrollToSection('profil')"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white/90 px-6 py-3.5 text-xs font-bold text-slate-800 shadow-sm hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-900/90 dark:text-slate-200 dark:hover:bg-slate-800 transition active:scale-95 cursor-pointer"
                            >
                                Jelajahi Profil Desa
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-emerald-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Right Hero Visual Showcase -->
                    <div class="lg:col-span-5 relative">
                        <div class="relative mx-auto max-w-md overflow-hidden rounded-3xl border border-slate-200/80 bg-white p-3 shadow-2xl shadow-emerald-900/10 dark:border-slate-800 dark:bg-slate-900">
                            <div class="relative overflow-hidden rounded-2xl aspect-[4/3]">
                                <img
                                    :src="heroImage"
                                    :alt="`Pemandangan ${villageName}`"
                                    class="h-full w-full object-cover transition duration-700 hover:scale-105"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>

                                <div class="absolute bottom-4 left-4 right-4 text-left text-white">
                                    <span class="inline-block rounded-md bg-emerald-600/90 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider backdrop-blur-md">
                                        {{ regencyName }}
                                    </span>
                                    <h3 class="mt-1 text-base font-black tracking-tight text-white">
                                        Keindahan {{ villageName }}
                                    </h3>
                                    <p class="text-[11px] text-slate-200 font-medium">
                                        {{ subdistrictName }}, {{ regencyName }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. STATISTIK DEMOGRAFI DESA -->
        <section class="border-y border-slate-200/80 bg-white py-12 dark:border-slate-800/80 dark:bg-slate-900/50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-6 sm:grid-cols-4">
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-6 text-center dark:border-slate-800 dark:bg-slate-900/80 shadow-sm">
                        <span class="block text-3xl sm:text-4xl font-black tracking-tight text-emerald-600 dark:text-emerald-400">
                            {{ stats.penduduk }}
                        </span>
                        <span class="mt-1 block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                            Jumlah Penduduk
                        </span>
                    </div>

                    <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-6 text-center dark:border-slate-800 dark:bg-slate-900/80 shadow-sm">
                        <span class="block text-3xl sm:text-4xl font-black tracking-tight text-teal-600 dark:text-teal-400">
                            {{ stats.kk }}
                        </span>
                        <span class="mt-1 block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                            Kepala Keluarga (KK)
                        </span>
                    </div>

                    <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-6 text-center dark:border-slate-800 dark:bg-slate-900/80 shadow-sm">
                        <span class="block text-3xl sm:text-4xl font-black tracking-tight text-emerald-600 dark:text-emerald-400">
                            {{ stats.dusun }}
                        </span>
                        <span class="mt-1 block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                            Kedusunan
                        </span>
                    </div>

                    <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-6 text-center dark:border-slate-800 dark:bg-slate-900/80 shadow-sm">
                        <span class="block text-3xl sm:text-4xl font-black tracking-tight text-teal-600 dark:text-teal-400">
                            {{ stats.rt_rw }}
                        </span>
                        <span class="mt-1 block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                            RT / RW
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. SAMBUTAN KEPALA DESA & VISI MISI -->
        <section id="profil" class="py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-14">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                        Profil & Kepemimpinan
                    </span>
                    <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl">
                        Sambutan Kepala {{ villageName }}
                    </h2>
                    <div class="mt-3 h-1 w-16 bg-gradient-to-r from-emerald-600 to-teal-500 mx-auto rounded-full"></div>
                </div>

                <div class="grid items-center gap-12 lg:grid-cols-12">
                    <!-- Kades Card / Photo -->
                    <div class="lg:col-span-5">
                        <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 shadow-xl dark:border-slate-800 dark:bg-slate-900 text-center">
                            <div class="mx-auto h-40 w-40 overflow-hidden rounded-full bg-gradient-to-tr from-emerald-600 to-teal-500 p-1.5 shadow-lg shadow-emerald-500/20">
                                <img
                                    v-if="kadesPhoto"
                                    :src="kadesPhoto"
                                    :alt="kadesName"
                                    class="h-full w-full rounded-full object-cover"
                                />
                                <div v-else class="h-full w-full rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-4xl font-black text-emerald-600">
                                    👨‍💼
                                </div>
                            </div>

                            <h3 class="mt-5 text-xl font-black text-slate-900 dark:text-white">
                                {{ kadesName }}
                            </h3>
                            <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
                                {{ kadesPosition }}
                            </p>
                            <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                                {{ kadesPeriod }}
                            </p>
                        </div>
                    </div>

                    <!-- Speech Content -->
                    <div class="lg:col-span-7 space-y-6 text-left">
                        <blockquote class="rounded-2xl border-l-4 border-emerald-600 bg-white p-6 shadow-sm dark:bg-slate-900 dark:border-emerald-500">
                            <p class="text-sm sm:text-base font-medium italic text-slate-700 dark:text-slate-200 leading-relaxed">
                                "{{ kadesSpeech }}"
                            </p>
                        </blockquote>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. LAYANAN DIGITAL DESA -->
        <section id="layanan" class="bg-slate-100/70 py-20 dark:bg-slate-900/60 border-y border-slate-200/80 dark:border-slate-800/80">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-14">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                        E-Gov Layanan Mandiri
                    </span>
                    <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl">
                        Administrasi Surat Keterangan Desa
                    </h2>
                    <p class="mt-3 text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                        Warga dapat mengajukan permohonan surat keterangan administrasi secara praktis melalui sistem terintegrasi Balai {{ villageName }}.
                    </p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="service in servicesList"
                        :key="service.id || service.title"
                        class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 hover:border-emerald-500/50 hover:shadow-xl transition-all duration-300"
                    >
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 font-bold text-xl group-hover:scale-110 transition-transform">
                            {{ service.icon || '📝' }}
                        </div>
                        <h3 class="mt-4 text-base font-black text-slate-900 dark:text-white">
                            {{ service.title }}
                        </h3>
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            {{ service.desc }}
                        </p>
                        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-emerald-600 dark:text-emerald-400">
                            <span>{{ service.estimate }}</span>
                            <span>Ajukan &rarr;</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. POTENSI & KOMODITAS UNGGULAN -->
        <section id="potensi" class="py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-14">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                        Potensi Kekayaan Lokal
                    </span>
                    <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl">
                        Komoditas & Keunggulan {{ villageName }}
                    </h2>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="item in potentialsList"
                        :key="item.id || item.title"
                        class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 hover:shadow-lg transition"
                    >
                        <div class="h-40 bg-gradient-to-tr from-emerald-600 to-teal-500 p-4 flex items-center justify-center text-5xl">
                            {{ item.icon || '🌾' }}
                        </div>
                        <div class="p-5 text-left">
                            <span class="rounded-md bg-emerald-50 px-2.5 py-1 text-[10px] font-extrabold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                {{ item.category }}
                            </span>
                            <h3 class="mt-2 text-base font-black text-slate-900 dark:text-white">
                                {{ item.title }}
                            </h3>
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                {{ item.desc }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. BERITA & PENGUMUMAN TERKINI -->
        <section id="berita" class="bg-slate-100/70 py-20 dark:bg-slate-900/60 border-y border-slate-200/80 dark:border-slate-800/80">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-12 gap-4">
                    <div class="text-left">
                        <span class="text-xs font-extrabold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                            Warta {{ villageName }}
                        </span>
                        <h2 class="mt-1 text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                            Berita & Pengumuman Terkini
                        </h2>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <article
                        v-for="item in newsList"
                        :key="item.id || item.title"
                        class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 hover:shadow-xl transition"
                    >
                        <div class="p-6 text-left">
                            <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 font-semibold mb-3">
                                <span class="rounded-md bg-emerald-100 px-2.5 py-0.5 font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                    {{ item.category }}
                                </span>
                                <time>{{ item.date }}</time>
                            </div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition cursor-pointer">
                                {{ item.title }}
                            </h3>
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400 line-clamp-3">
                                {{ item.excerpt }}
                            </p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- 7. STRUKTUR PERANGKAT DESA -->
        <section id="perangkat" class="py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-14">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                        Pamong & Aparatur
                    </span>
                    <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl">
                        Struktur Perangkat {{ villageName }}
                    </h2>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="officer in officersList"
                        :key="officer.id || officer.name"
                        class="rounded-3xl border border-slate-200 bg-white p-5 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900"
                    >
                        <div class="mx-auto h-24 w-24 overflow-hidden rounded-full bg-emerald-100 dark:bg-emerald-950 text-3xl flex items-center justify-center mb-4">
                            <img
                                v-if="officer.photo"
                                :src="officer.photo"
                                :alt="officer.name"
                                class="h-full w-full object-cover"
                            />
                            <span v-else>{{ officer.icon || '👔' }}</span>
                        </div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white">
                            {{ officer.name }}
                        </h3>
                        <p class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400">
                            {{ officer.position }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8. BALAI DESA SHOWCASE -->
        <section class="py-12 bg-white dark:bg-slate-900 border-y border-slate-200/80 dark:border-slate-800/80">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-50 shadow-xl dark:border-slate-800 dark:bg-slate-950">
                    <div class="grid lg:grid-cols-12 items-center">
                        <div class="lg:col-span-6 p-8 sm:p-12 text-left">
                            <span class="rounded-md bg-emerald-100 px-3 py-1 text-xs font-extrabold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                Pusat Pelayanan Warga
                            </span>
                            <h2 class="mt-4 text-2xl sm:text-4xl font-black text-slate-900 dark:text-white">
                                {{ balaiDesaTitle }}
                            </h2>
                            <p class="mt-3 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                                {{ balaiDesaDesc }}
                            </p>
                        </div>

                        <div class="lg:col-span-6 h-72 sm:h-96 relative overflow-hidden">
                            <img
                                :src="balaiDesaPhoto"
                                :alt="balaiDesaTitle"
                                class="h-full w-full object-cover"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 9. KONTAK & PETA -->
        <section id="kontak" class="py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-14">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                        Lokasi & Informasi
                    </span>
                    <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white sm:text-4xl">
                        Hubungi Balai {{ villageName }}
                    </h2>
                </div>

                <div class="grid gap-8 lg:grid-cols-12">
                    <!-- Contact Cards -->
                    <div class="lg:col-span-5 space-y-4 text-left">
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 flex items-start gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400">
                                📍
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-slate-900 dark:text-white">Alamat Kantor Desa</h4>
                                <p class="mt-1 text-xs text-slate-600 dark:text-slate-400 leading-relaxed whitespace-pre-line">
                                    {{ contactAddress }}
                                </p>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 flex items-start gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-teal-100 text-teal-700 dark:bg-teal-950 dark:text-teal-400">
                                ⏰
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-slate-900 dark:text-white">Jam Operasional Pelayanan</h4>
                                <p class="mt-1 text-xs text-slate-600 dark:text-slate-400 whitespace-pre-line">
                                    {{ contactHours }}
                                </p>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 flex items-start gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400">
                                💬
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-slate-900 dark:text-white">Hotline & Email Resmi</h4>
                                <p class="mt-1 text-xs text-slate-600 dark:text-slate-400">
                                    WhatsApp: <a :href="`https://wa.me/62${contactPhone.replace(/\D/g, '')}`" target="_blank" class="font-bold text-emerald-600 underline">{{ contactPhone }}</a><br>
                                    Email: <span class="font-bold text-slate-800 dark:text-slate-200">{{ contactEmail }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Map Box -->
                    <div class="lg:col-span-7">
                        <div class="h-full min-h-[300px] overflow-hidden rounded-3xl border border-slate-200 bg-slate-200 dark:border-slate-800 dark:bg-slate-800 relative">
                            <iframe
                                title="Peta Lokasi Desa"
                                class="h-full w-full border-0"
                                loading="lazy"
                                allowfullscreen
                                :src="contactMapsUrl"
                            ></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="border-t border-slate-200 bg-white py-12 dark:border-slate-800 dark:bg-slate-900">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-8 lg:grid-cols-12 pb-8 border-b border-slate-200/80 dark:border-slate-800/80">
                    <div class="lg:col-span-5 text-left space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-600 text-white font-bold">
                                🏛️
                            </div>
                            <span class="text-base font-black tracking-tight text-slate-900 dark:text-white uppercase">
                                {{ villageName }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md leading-relaxed">
                            Website Resmi dan Portal Pelayanan Administrasi {{ villageName }}, {{ subdistrictName }}, {{ regencyName }}.
                        </p>
                    </div>

                    <div class="lg:col-span-7 grid grid-cols-2 sm:grid-cols-3 gap-6 text-left">
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Navigasi</h4>
                            <ul class="mt-3 space-y-2 text-xs text-slate-500 dark:text-slate-400 font-medium">
                                <li><button @click="scrollToSection('hero')" class="hover:text-emerald-600">Beranda</button></li>
                                <li><button @click="scrollToSection('profil')" class="hover:text-emerald-600">Profil Desa</button></li>
                                <li><button @click="scrollToSection('layanan')" class="hover:text-emerald-600">Layanan Digital</button></li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Informasi</h4>
                            <ul class="mt-3 space-y-2 text-xs text-slate-500 dark:text-slate-400 font-medium">
                                <li><button @click="scrollToSection('potensi')" class="hover:text-emerald-600">Potensi Desa</button></li>
                                <li><button @click="scrollToSection('berita')" class="hover:text-emerald-600">Berita Desa</button></li>
                                <li><button @click="scrollToSection('perangkat')" class="hover:text-emerald-600">Perangkat Desa</button></li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Portal Admin</h4>
                            <ul class="mt-3 space-y-2 text-xs text-slate-500 dark:text-slate-400 font-medium">
                                <li><Link :href="route('login')" class="hover:text-emerald-600 font-bold text-emerald-600">Log In Petugas</Link></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-slate-500 dark:text-slate-400">
                    <p>&copy; {{ new Date().getFullYear() }} Pemerintah {{ villageName }}. Seluruh Hak Cipta Dilindungi.</p>
                    <p class="text-[11px]">Dikembangkan untuk Pelayanan Publik & KKN {{ villageName }}</p>
                </div>
            </div>
        </footer>
    </div>
</template>
