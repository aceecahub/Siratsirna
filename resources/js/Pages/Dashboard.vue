<script setup>
import { computed, ref, onMounted } from 'vue';
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
            suratMasukBulanIni: 0,
            suratKeluarBulanIni: 0,
        }),
    },
    recentOutgoing: {
        type: Array,
        default: () => [],
    },
    recentIncoming: {
        type: Array,
        default: () => [],
    },
    chartData: {
        type: Object,
        default: () => ({ categories: [], series: [] }),
    }
});

const page = usePage();

// Calendar variables
const currentDate = new Date();
const currentMonthYear = currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
const currentDay = currentDate.getDate();

const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
const currentWeek = [];
for (let i = 0; i < 7; i++) {
    const d = new Date(currentDate);
    d.setDate(currentDate.getDate() - currentDate.getDay() + i);
    currentWeek.push({
        dayName: days[i],
        date: d.getDate(),
        isToday: d.getDate() === currentDay
    });
}

// Chart Options
const chartOptions = computed(() => {
    // Check dark mode by looking at html class
    const isDark = typeof window !== 'undefined' && document.documentElement.classList.contains('dark');
    
    return {
        chart: {
            type: 'area',
            height: 350,
            toolbar: { show: false },
            zoom: { enabled: false },
            background: 'transparent',
            fontFamily: 'Inter, sans-serif',
        },
        colors: ['#009675', '#0ea5e9'],
        dataLabels: { enabled: false },
        stroke: {
            curve: 'smooth',
            width: 2,
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: props.chartData.categories,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: {
                    colors: isDark ? '#94a3b8' : '#64748b',
                    fontSize: '11px',
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: isDark ? '#94a3b8' : '#64748b',
                    fontSize: '11px',
                },
                formatter: (value) => { return Math.round(value) }
            },
        },
        grid: {
            borderColor: isDark ? '#1e293b' : '#f1f5f9',
            strokeDashArray: 4,
            yaxis: { lines: { show: true } },
            xaxis: { lines: { show: false } },
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            labels: {
                colors: isDark ? '#cbd5e1' : '#475569',
            }
        },
        theme: { mode: isDark ? 'dark' : 'light' }
    };
});

</script>

<template>
    <Head title="Dashboard Administrasi Surat" />

    <AuthenticatedLayout>
        <div class="px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header Section (Welcome Back) -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                        Welcome Back, {{ page.props.auth.user.name.split(' ')[0] }} 👋
                    </h1>
                    <p class="text-[13px] mt-1 text-slate-500 dark:text-slate-400">
                        Sistem Informasi Surat Menyurat Desa. Let's Make Progress Together!
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        1 Jan - 31 Dec {{ currentDate.getFullYear() }}
                    </div>
                    <Link
                        :href="route('surat-keluar.create')"
                        class="inline-flex items-center gap-1.5 rounded-md bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-primary/90 active:scale-95"
                    >
                        + Add New
                    </Link>
                </div>
            </div>

            <!-- Main Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left Column (Main Content) -->
                <div class="lg:col-span-8 flex flex-col gap-6">
                    
                    <!-- Top Stats Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Card 1 -->
                        <div class="rounded-xl border border-slate-200 bg-white flex flex-col shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <div class="p-4 flex-1">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary text-white shadow-sm shadow-primary/20">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ stats.totalSuratKeluar }}</div>
                                        <div class="text-xs font-medium text-slate-500">Surat Keluar</div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t border-slate-100 dark:border-slate-800/80 px-4 py-2.5">
                                <Link :href="route('surat-keluar.index')" class="flex items-center justify-between text-xs font-medium text-slate-500 hover:text-slate-900 dark:hover:text-white transition">
                                    View details
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                        
                        <!-- Card 2 -->
                        <div class="rounded-xl border border-slate-200 bg-white flex flex-col shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <div class="p-4 flex-1">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm shadow-blue-600/20">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75H6.912a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859M12 3v8.25m0 0l-3-3m3 3l3-3" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ stats.totalSuratMasuk }}</div>
                                        <div class="text-xs font-medium text-slate-500">Surat Masuk</div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t border-slate-100 dark:border-slate-800/80 px-4 py-2.5">
                                <Link :href="route('surat-masuk.index')" class="flex items-center justify-between text-xs font-medium text-slate-500 hover:text-slate-900 dark:hover:text-white transition">
                                    View details
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                                    </svg>
                                </Link>
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div class="rounded-xl border border-slate-200 bg-white flex flex-col shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <div class="p-4 flex-1">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-500 text-white shadow-sm shadow-orange-500/20">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ stats.suratKeluarBulanIni }}</div>
                                        <div class="text-xs font-medium text-slate-500">Terbit Bulan Ini</div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t border-slate-100 dark:border-slate-800/80 px-4 py-2.5">
                                <Link :href="route('surat-keluar.index')" class="flex items-center justify-between text-xs font-medium text-slate-500 hover:text-slate-900 dark:hover:text-white transition">
                                    View details
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Section -->
                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Statistik Surat (6 Bulan)</h3>
                            <div class="flex items-center gap-2 rounded-md border border-slate-200 px-2.5 py-1 text-[10px] font-medium text-slate-500 dark:border-slate-700 dark:text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                                Last 6 Months
                            </div>
                        </div>
                        <div class="mt-4 -ml-2 -mr-2">
                            <VueApexCharts
                                type="area"
                                height="300"
                                :options="chartOptions"
                                :series="props.chartData.series"
                            />
                        </div>
                    </div>

                    <!-- Top Performers (Recent Outgoing) -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Top Performers / Recent Mails</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div 
                                v-for="(mail, idx) in recentOutgoing" 
                                :key="mail.id"
                                class="flex items-center justify-between rounded-xl border border-slate-100 bg-white p-3 shadow-sm dark:border-slate-800 dark:bg-slate-900 transition hover:border-primary/30"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <!-- Avatar placeholder -->
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 dark:bg-slate-800 overflow-hidden font-bold">
                                        <img v-if="idx % 3 === 0" src="https://ui-avatars.com/api/?name=User&background=009675&color=fff" class="w-full h-full object-cover"/>
                                        <img v-else-if="idx % 3 === 1" src="https://ui-avatars.com/api/?name=Admin&background=0ea5e9&color=fff" class="w-full h-full object-cover"/>
                                        <img v-else src="https://ui-avatars.com/api/?name=Staff&background=f97316&color=fff" class="w-full h-full object-cover"/>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="truncate text-sm font-bold text-slate-900 dark:text-white">{{ mail.tujuan }}</div>
                                        <div class="truncate text-[11px] text-slate-500">{{ mail.perihal }}</div>
                                    </div>
                                </div>
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" />
                                            </svg>
                                        </button>
                                    </template>
                                    <template #content>
                                        <Link :href="route('surat-keluar.show', mail.id)" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Detail</Link>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Side Panel) -->
                <div class="lg:col-span-4 flex flex-col gap-6">
                    
                    <!-- Calendar Widget -->
                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ currentMonthYear }}</h3>
                            <div class="flex items-center gap-1">
                                <button class="rounded p-1 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                    </svg>
                                </button>
                                <button class="rounded p-1 hover:bg-slate-100 dark:hover:bg-slate-800 bg-primary text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-center">
                            <div v-for="w in currentWeek" :key="w.dayName" class="flex flex-col items-center gap-1.5 pb-2">
                                <span class="text-[10px] font-medium text-slate-400">{{ w.dayName }}</span>
                                <span :class="[
                                    'flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold',
                                    w.isToday ? 'bg-primary text-white shadow-md shadow-primary/30' : 'text-slate-700 dark:text-slate-300'
                                ]">
                                    {{ w.date }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-100 border-dashed dark:border-slate-800">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-3">Today / Aktivitas Terbaru</div>
                            
                            <div class="space-y-4">
                                <div v-for="(mail, idx) in recentIncoming" :key="mail.id" class="flex items-start gap-3">
                                    <div :class="[
                                        'mt-1 flex h-10 w-1 shrink-0 rounded-full',
                                        idx % 3 === 0 ? 'bg-primary' : (idx % 3 === 1 ? 'bg-blue-500' : 'bg-orange-500')
                                    ]"></div>
                                    <div class="min-w-0 flex-1 bg-slate-50 p-3 rounded-lg dark:bg-slate-800/50">
                                        <div class="text-xs font-bold text-slate-900 truncate dark:text-white">{{ mail.perihal }}</div>
                                        <div class="mt-1 text-[10px] text-slate-500">{{ mail.kategori }} &bull; No. {{ mail.nomor_agenda }}</div>
                                    </div>
                                </div>
                                <div v-if="recentIncoming.length === 0" class="text-xs text-slate-400 text-center py-2">
                                    Tidak ada aktivitas
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sub Card (Job Applications mock) -->
                    <div class="rounded-xl bg-orange-50 p-5 dark:bg-orange-950/20">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-orange-600 mb-2">SUMMARY</div>
                        <div class="text-lg font-bold text-slate-900 dark:text-white">Laporan Persuratan</div>
                        <div class="text-[11px] text-slate-600 dark:text-slate-400 mt-1">Cetak laporan data arsip bulan ini.</div>
                        <button class="mt-4 rounded-md bg-white px-4 py-2 text-xs font-bold text-slate-900 shadow-sm transition hover:bg-slate-50">
                            Unduh Laporan
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
