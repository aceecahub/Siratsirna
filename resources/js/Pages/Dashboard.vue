<script setup>
import { computed, ref } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { formatTanggal } from '@/lib/format';
import VueApexCharts from "vue3-apexcharts";

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalSuratMasuk: 0,
            totalSuratKeluar: 0,
            totalSuratKeterangan: 0,
            suratMasukBulanIni: 0,
            suratKeluarBulanIni: 0,
            suratKeteranganBulanIni: 0,
            totalSemuaSurat: 0,
            breakdown: {},
            keteranganBulanIni: {},
        }),
    },
    recentOutgoing: { type: Array, default: () => [] },
    recentIncoming: { type: Array, default: () => [] },
    recentLetters:  { type: Array, default: () => [] },
    chartData:          { type: Object, default: () => ({ categories: [], series: [] }) },
    breakdownChartData: { type: Object, default: () => ({ labels: [], series: [] }) },
    stackedChartData:   { type: Object, default: () => ({ categories: [], series: [] }) },
});

const page = usePage();

// ─── Calendar ──────────────────────────────────────────────────────────────────
const currentDate = new Date();
const currentMonthYear = currentDate.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
const currentDay = currentDate.getDate();
const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
const currentWeek = [];
for (let i = 0; i < 7; i++) {
    const d = new Date(currentDate);
    d.setDate(currentDate.getDate() - currentDate.getDay() + i);
    currentWeek.push({ dayName: days[i], date: d.getDate(), isToday: d.getDate() === currentDay });
}

// ─── 8 Surat Keterangan cards config ──────────────────────────────────────────
const suratKeteranganCards = computed(() => [
    { label: 'Domisili',    key: 'Domisili',   route: 'surat-keterangan-domisili.index', createRoute: 'surat-keterangan-domisili.create', color: '#009675', bg: 'bg-emerald-50 dark:bg-emerald-950/20',  border: 'border-emerald-200 dark:border-emerald-800', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { label: 'SKTM',        key: 'SKTM',       route: 'surat-tidak-mampu.index',         createRoute: 'surat-tidak-mampu.create',         color: '#6366f1', bg: 'bg-indigo-50 dark:bg-indigo-950/20',    border: 'border-indigo-200 dark:border-indigo-800',   icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
    { label: 'Menikah',     key: 'Nikah',      route: 'surat-keterangan-menikah.index',  createRoute: 'surat-keterangan-menikah.create',  color: '#ec4899', bg: 'bg-pink-50 dark:bg-pink-950/20',        border: 'border-pink-200 dark:border-pink-800',       icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' },
    { label: 'E-KTP',       key: 'E-KTP',      route: 'surat-pengantar-ktp.index',       createRoute: 'surat-pengantar-ktp.create',       color: '#0ea5e9', bg: 'bg-sky-50 dark:bg-sky-950/20',          border: 'border-sky-200 dark:border-sky-800',         icon: 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2' },
    { label: 'Kehilangan',  key: 'Kehilangan', route: 'surat-keterangan-kehilangan.index', createRoute: 'surat-keterangan-kehilangan.create', color: '#f97316', bg: 'bg-orange-50 dark:bg-orange-950/20',  border: 'border-orange-200 dark:border-orange-800',  icon: 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { label: 'Penghasilan', key: 'Penghasilan',route: 'surat-keterangan-penghasilan.index', createRoute: 'surat-keterangan-penghasilan.create', color: '#eab308', bg: 'bg-yellow-50 dark:bg-yellow-950/20', border: 'border-yellow-200 dark:border-yellow-800', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { label: 'Ahli Waris',  key: 'Ahli Waris', route: 'surat-ahli-waris.index',           createRoute: 'surat-ahli-waris.create',           color: '#8b5cf6', bg: 'bg-violet-50 dark:bg-violet-950/20',    border: 'border-violet-200 dark:border-violet-800',  icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { label: 'Bantuan',     key: 'Bantuan',    route: 'surat-permohonan-bantuan.index',   createRoute: 'surat-permohonan-bantuan.create',   color: '#14b8a6', bg: 'bg-teal-50 dark:bg-teal-950/20',        border: 'border-teal-200 dark:border-teal-800',       icon: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z' },
]);

// ─── Badge color per jenis ─────────────────────────────────────────────────────
const badgeColor = {
    'Domisili':   'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
    'SKTM':       'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300',
    'Nikah':      'bg-pink-100 text-pink-700 dark:bg-pink-900/40 dark:text-pink-300',
    'E-KTP':      'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300',
    'Kehilangan': 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300',
    'Penghasilan':'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
    'Ahli Waris': 'bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300',
    'Bantuan':    'bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300',
};

// ─── Charts ───────────────────────────────────────────────────────────────────
const isDark = computed(() => typeof window !== 'undefined' && document.documentElement.classList.contains('dark'));

const areaChartOptions = computed(() => ({
    chart: { type: 'area', height: 280, toolbar: { show: false }, zoom: { enabled: false }, background: 'transparent', fontFamily: 'Inter, sans-serif' },
    colors: ['#009675', '#0ea5e9', '#f97316'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02, stops: [0, 90, 100] } },
    xaxis: {
        categories: props.chartData.categories,
        axisBorder: { show: false }, axisTicks: { show: false },
        labels: { style: { colors: isDark.value ? '#94a3b8' : '#64748b', fontSize: '11px' } }
    },
    yaxis: {
        labels: { style: { colors: isDark.value ? '#94a3b8' : '#64748b', fontSize: '11px' }, formatter: v => Math.round(v) },
    },
    grid: { borderColor: isDark.value ? '#1e293b' : '#f1f5f9', strokeDashArray: 4, yaxis: { lines: { show: true } }, xaxis: { lines: { show: false } } },
    legend: { position: 'top', horizontalAlign: 'right', labels: { colors: isDark.value ? '#cbd5e1' : '#475569' } },
    tooltip: { theme: isDark.value ? 'dark' : 'light' },
}));

const donutChartOptions = computed(() => ({
    chart: { type: 'donut', background: 'transparent', fontFamily: 'Inter, sans-serif' },
    colors: ['#009675', '#6366f1', '#ec4899', '#0ea5e9', '#f97316', '#eab308', '#8b5cf6', '#14b8a6'],
    labels: props.breakdownChartData.labels,
    legend: { position: 'bottom', labels: { colors: isDark.value ? '#cbd5e1' : '#475569' }, fontSize: '11px' },
    dataLabels: { enabled: false },
    plotOptions: { pie: { donut: { size: '68%', labels: { show: true, total: { show: true, label: 'Total', color: isDark.value ? '#cbd5e1' : '#475569', fontSize: '12px', fontWeight: 600 }, value: { color: isDark.value ? '#f1f5f9' : '#0f172a', fontSize: '20px', fontWeight: 700 } } } } },
    tooltip: { theme: isDark.value ? 'dark' : 'light' },
    stroke: { width: 2, colors: [isDark.value ? '#0f172a' : '#fff'] },
}));

const stackedChartOptions = computed(() => ({
    chart: { type: 'bar', stacked: true, height: 280, toolbar: { show: false }, background: 'transparent', fontFamily: 'Inter, sans-serif' },
    colors: ['#009675', '#6366f1', '#ec4899', '#0ea5e9', '#f97316', '#eab308', '#8b5cf6', '#14b8a6'],
    plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 4, borderRadiusApplication: 'end', borderRadiusWhenStacked: 'last' } },
    dataLabels: { enabled: false },
    xaxis: {
        categories: props.stackedChartData.categories,
        axisBorder: { show: false }, axisTicks: { show: false },
        labels: { style: { colors: isDark.value ? '#94a3b8' : '#64748b', fontSize: '11px' } }
    },
    yaxis: { labels: { style: { colors: isDark.value ? '#94a3b8' : '#64748b', fontSize: '11px' }, formatter: v => Math.round(v) } },
    grid: { borderColor: isDark.value ? '#1e293b' : '#f1f5f9', strokeDashArray: 4, yaxis: { lines: { show: true } } },
    legend: { position: 'top', horizontalAlign: 'right', labels: { colors: isDark.value ? '#cbd5e1' : '#475569' }, fontSize: '11px' },
    fill: { opacity: 1 },
    tooltip: { theme: isDark.value ? 'dark' : 'light' },
}));

// ─── Dropdown Buat Surat ───────────────────────────────────────────────────────
const showDropdown = ref(false);
const quickCreateLinks = [
    { label: 'Surat Keluar',      route: 'surat-keluar.create' },
    { label: 'Ket. Domisili',     route: 'surat-keterangan-domisili.create' },
    { label: 'SKTM',              route: 'surat-tidak-mampu.create' },
    { label: 'Ket. Menikah',      route: 'surat-keterangan-menikah.create' },
    { label: 'Pengantar E-KTP',   route: 'surat-pengantar-ktp.create' },
    { label: 'Ket. Kehilangan',   route: 'surat-keterangan-kehilangan.create' },
    { label: 'Ket. Penghasilan',  route: 'surat-keterangan-penghasilan.create' },
    { label: 'Ahli Waris',        route: 'surat-ahli-waris.create' },
    { label: 'Permohonan Bantuan',route: 'surat-permohonan-bantuan.create' },
];

function formatDate(dt) {
    if (!dt) return '-';
    const d = new Date(dt);
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <Head title="Dashboard Administrasi Surat" />
    <AuthenticatedLayout>
        <div class="px-4 py-8 sm:px-6 lg:px-8 space-y-6">

            <!-- ── Header ───────────────────────────────────────── -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                        Selamat Datang, {{ page.props.auth.user.name.split(' ')[0] }} 👋
                    </h1>
                    <p class="text-[13px] mt-1 text-slate-500 dark:text-slate-400">
                        Sistem Informasi Surat Menyurat Desa — Ringkasan seluruh aktivitas persuratan.
                    </p>
                </div>
                <!-- Dropdown Buat Surat -->
                <div class="relative" @mouseleave="showDropdown = false">
                    <button
                        @click="showDropdown = !showDropdown"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-sm shadow-primary/30 transition hover:bg-primary/90 active:scale-95"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Buat Surat
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3 w-3 transition-transform" :class="showDropdown ? 'rotate-180' : ''"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </button>
                    <Transition
                        enter-active-class="transition ease-out duration-150"
                        enter-from-class="opacity-0 scale-95 -translate-y-1"
                        enter-to-class="opacity-100 scale-100 translate-y-0"
                        leave-active-class="transition ease-in duration-100"
                        leave-from-class="opacity-100 scale-100 translate-y-0"
                        leave-to-class="opacity-0 scale-95 -translate-y-1"
                    >
                        <div v-if="showDropdown" class="absolute right-0 top-full mt-2 z-50 w-52 rounded-xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900">
                            <div class="p-1.5">
                                <Link
                                    v-for="q in quickCreateLinks" :key="q.route"
                                    :href="route(q.route)"
                                    class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-primary dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white transition"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5 text-slate-400 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                    {{ q.label }}
                                </Link>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>

            <!-- ── Top 4 Stat Cards ──────────────────────────────── -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Surat Masuk -->
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm shadow-blue-600/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75H6.912a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859M12 3v8.25m0 0l-3-3m3 3l3-3" /></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ stats.totalSuratMasuk }}</div>
                        <div class="text-xs font-medium text-slate-500 truncate">Surat Masuk</div>
                        <div class="text-[10px] text-blue-600 font-semibold mt-0.5">+{{ stats.suratMasukBulanIni }} bulan ini</div>
                    </div>
                </div>
                <!-- Surat Keluar -->
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary text-white shadow-sm shadow-primary/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ stats.totalSuratKeluar }}</div>
                        <div class="text-xs font-medium text-slate-500 truncate">Surat Keluar</div>
                        <div class="text-[10px] text-primary font-semibold mt-0.5">+{{ stats.suratKeluarBulanIni }} bulan ini</div>
                    </div>
                </div>
                <!-- Surat Keterangan -->
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-600 text-white shadow-sm shadow-violet-600/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ stats.totalSuratKeterangan }}</div>
                        <div class="text-xs font-medium text-slate-500 truncate">Surat Keterangan</div>
                        <div class="text-[10px] text-violet-600 font-semibold mt-0.5">+{{ stats.suratKeteranganBulanIni }} bulan ini</div>
                    </div>
                </div>
                <!-- Total Semua -->
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-orange-500 text-white shadow-sm shadow-orange-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" /></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ stats.totalSemuaSurat }}</div>
                        <div class="text-xs font-medium text-slate-500 truncate">Total Semua Surat</div>
                        <div class="text-[10px] text-orange-500 font-semibold mt-0.5">Sepanjang waktu</div>
                    </div>
                </div>
            </div>

            <!-- ── 8 Surat Keterangan Breakdown Cards ────────────── -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Surat Keterangan Warga</h2>
                    <span class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">8 Jenis Surat</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
                    <Link
                        v-for="card in suratKeteranganCards"
                        :key="card.key"
                        :href="route(card.route)"
                        :class="['group relative rounded-xl border p-3 transition hover:shadow-md hover:-translate-y-0.5', card.bg, card.border]"
                    >
                        <div class="flex flex-col items-center text-center gap-2">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg text-white shadow-sm" :style="{ background: card.color }">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4.5 w-4.5 h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" :d="card.icon" />
                                </svg>
                            </div>
                            <div class="text-xl font-extrabold text-slate-900 dark:text-white">{{ stats.breakdown?.[card.key] ?? 0 }}</div>
                            <div class="text-[10px] font-semibold text-slate-600 dark:text-slate-300 leading-tight">{{ card.label }}</div>
                            <div class="text-[9px] font-medium" :style="{ color: card.color }">+{{ stats.keteranganBulanIni?.[card.key] ?? 0 }} bln ini</div>
                        </div>
                        <!-- Quick create tooltip -->
                        <div class="absolute inset-x-0 -bottom-1 flex justify-center opacity-0 group-hover:opacity-100 transition">
                            <Link :href="route(card.createRoute)" class="rounded-full px-2 py-0.5 text-[9px] font-bold text-white shadow-lg" :style="{ background: card.color }" @click.stop>+ Buat</Link>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- ── Charts Row: Area + Donut ───────────────────────── -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Area Chart (2/3 width) -->
                <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Tren Persuratan 6 Bulan</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">Surat Masuk, Keluar & Keterangan Warga</p>
                        </div>
                        <div class="flex items-center gap-1.5 rounded-md border border-slate-200 px-2.5 py-1 text-[10px] font-medium text-slate-500 dark:border-slate-700 dark:text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                            6 Bulan Terakhir
                        </div>
                    </div>
                    <VueApexCharts type="area" height="260" :options="areaChartOptions" :series="chartData.series" />
                </div>

                <!-- Donut Chart (1/3 width) -->
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="mb-4">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Distribusi Surat Keterangan</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Perbandingan 8 jenis surat</p>
                    </div>
                    <VueApexCharts type="donut" height="260" :options="donutChartOptions" :series="breakdownChartData.series" />
                </div>
            </div>

            <!-- ── Stacked Bar Chart (Full Width) ────────────────── -->
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Breakdown Bulanan — Surat Keterangan</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Rincian per jenis surat keterangan 6 bulan terakhir</p>
                    </div>
                </div>
                <VueApexCharts type="bar" height="260" :options="stackedChartOptions" :series="stackedChartData.series" />
            </div>

            <!-- ── Bottom: Aktivitas Terbaru + Kalender ───────────── -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Surat Keterangan Terbaru (2/3) -->
                <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Aktivitas Terbaru</h3>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Surat Keterangan</span>
                    </div>

                    <!-- Table header -->
                    <div class="grid grid-cols-12 gap-2 px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <div class="col-span-1">#</div>
                        <div class="col-span-4">Nama</div>
                        <div class="col-span-4">Jenis Surat</div>
                        <div class="col-span-3">Tanggal</div>
                    </div>

                    <div class="space-y-1.5">
                        <template v-if="recentLetters.length > 0">
                            <div
                                v-for="(letter, idx) in recentLetters" :key="idx"
                                class="grid grid-cols-12 gap-2 items-center rounded-lg px-3 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition"
                            >
                                <div class="col-span-1 text-xs font-bold text-slate-400">{{ idx + 1 }}</div>
                                <div class="col-span-4 text-xs font-semibold text-slate-900 dark:text-white truncate">{{ letter.nama }}</div>
                                <div class="col-span-4">
                                    <span :class="['inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold', badgeColor[letter.jenis] ?? 'bg-slate-100 text-slate-600']">
                                        {{ letter.jenis }}
                                    </span>
                                </div>
                                <div class="col-span-3 text-[10px] text-slate-500">{{ formatDate(letter.created_at) }}</div>
                            </div>
                        </template>
                        <div v-else class="py-8 text-center text-xs text-slate-400">
                            Belum ada surat keterangan yang dibuat
                        </div>
                    </div>

                    <!-- Surat Masuk terbaru -->
                    <div class="mt-5 pt-4 border-t border-dashed border-slate-200 dark:border-slate-800">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Surat Masuk Terbaru</span>
                            <Link :href="route('surat-masuk.index')" class="text-[10px] font-semibold text-primary hover:underline">Lihat semua →</Link>
                        </div>
                        <div class="space-y-2">
                            <div v-for="mail in recentIncoming" :key="mail.id" class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 01-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 001.183 1.981l6.478 3.488m8.839 2.51l-4.66-2.51m0 0l-1.023-.55a2.25 2.25 0 00-2.134 0l-1.022.55m0 0l-4.661 2.51m16.5 1.615a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V8.844a2.25 2.25 0 011.183-1.98l7.5-4.04a2.25 2.25 0 012.134 0l7.5 4.04a2.25 2.25 0 011.183 1.98V19.5z" /></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-xs font-semibold text-slate-900 dark:text-white truncate">{{ mail.perihal }}</div>
                                    <div class="text-[10px] text-slate-500">{{ mail.kategori }} · No. {{ mail.nomor_agenda }}</div>
                                </div>
                            </div>
                            <div v-if="recentIncoming.length === 0" class="py-4 text-center text-xs text-slate-400">Belum ada surat masuk</div>
                        </div>
                    </div>
                </div>

                <!-- Right: Kalender + Quick Access -->
                <div class="flex flex-col gap-4">
                    <!-- Kalender -->
                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ currentMonthYear }}</h3>
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-center">
                            <div v-for="w in currentWeek" :key="w.dayName" class="flex flex-col items-center gap-1.5">
                                <span class="text-[10px] font-medium text-slate-400">{{ w.dayName }}</span>
                                <span :class="['flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold', w.isToday ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-700 dark:text-slate-300']">{{ w.date }}</span>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-dashed border-slate-200 dark:border-slate-800 text-center">
                            <div class="text-[10px] text-slate-400 font-medium">{{ new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}</div>
                        </div>
                    </div>

                    <!-- Quick Access Menu -->
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-3">Akses Cepat</div>
                        <div class="grid grid-cols-2 gap-2">
                            <Link :href="route('surat-masuk.index')" class="flex flex-col items-center gap-1.5 rounded-lg border border-slate-100 dark:border-slate-800 p-3 text-center hover:border-blue-300 hover:bg-blue-50 dark:hover:bg-blue-950/20 transition group">
                                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-100 text-blue-600 dark:bg-blue-900/30 group-hover:bg-blue-600 group-hover:text-white transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75H6.912a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859M12 3v8.25m0 0l-3-3m3 3l3-3" /></svg>
                                </div>
                                <span class="text-[10px] font-semibold text-slate-700 dark:text-slate-300">Surat Masuk</span>
                            </Link>
                            <Link :href="route('surat-keluar.index')" class="flex flex-col items-center gap-1.5 rounded-lg border border-slate-100 dark:border-slate-800 p-3 text-center hover:border-primary/40 hover:bg-primary/5 transition group">
                                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-primary/10 text-primary dark:bg-primary/20 group-hover:bg-primary group-hover:text-white transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                                </div>
                                <span class="text-[10px] font-semibold text-slate-700 dark:text-slate-300">Surat Keluar</span>
                            </Link>
                            <Link v-for="card in suratKeteranganCards.slice(0, 4)" :key="'qa-'+card.key" :href="route(card.route)"
                                class="flex flex-col items-center gap-1.5 rounded-lg border border-slate-100 dark:border-slate-800 p-3 text-center hover:shadow-sm transition group"
                                :style="{'--card-color': card.color}"
                            >
                                <div class="flex h-8 w-8 items-center justify-center rounded-md text-white" :style="{ background: card.color }">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" :d="card.icon" /></svg>
                                </div>
                                <span class="text-[10px] font-semibold text-slate-700 dark:text-slate-300 leading-tight">{{ card.label }}</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Summary Card -->
                    <div class="rounded-xl bg-gradient-to-br from-primary to-emerald-700 p-5 text-white shadow-lg shadow-primary/20">
                        <div class="text-[10px] font-bold uppercase tracking-wider opacity-75 mb-2">RINGKASAN</div>
                        <div class="text-xl font-bold">{{ stats.totalSemuaSurat }}</div>
                        <div class="text-sm font-medium opacity-90 mt-1">Total Surat Terarsip</div>
                        <div class="mt-3 flex items-center gap-3 text-[11px]">
                            <div class="flex-1 bg-white/10 rounded-lg p-2 text-center">
                                <div class="font-bold">{{ stats.suratMasukBulanIni + stats.suratKeluarBulanIni + stats.suratKeteranganBulanIni }}</div>
                                <div class="opacity-75 text-[10px]">Bulan ini</div>
                            </div>
                            <div class="flex-1 bg-white/10 rounded-lg p-2 text-center">
                                <div class="font-bold">{{ stats.totalSuratKeterangan }}</div>
                                <div class="opacity-75 text-[10px]">Keterangan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
