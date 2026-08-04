<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { formatTanggal } from '@/lib/format';

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
});

const todayDateFormatted = computed(() => {
    return new Date().toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
});
</script>

<template>
    <Head title="Dashboard Administrasi Surat" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                        Portal Administrasi Desa
                    </span>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                        Dashboard Persuratan
                    </h1>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-emerald-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        {{ todayDateFormatted }}
                    </span>
                    <Link
                        :href="route('surat-keluar.create')"
                        class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 px-4 py-2 text-xs font-bold text-white shadow-md shadow-emerald-600/20 transition hover:from-emerald-500 hover:to-teal-500 active:scale-95"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Buat Surat Keluar
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
                <!-- Welcome Banner -->
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-emerald-950 to-slate-900 p-6 sm:p-8 text-white shadow-xl">
                    <div class="absolute -right-10 -bottom-10 h-64 w-64 rounded-full bg-emerald-500/10 blur-3xl"></div>
                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="space-y-2 max-w-2xl">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-300 backdrop-blur-sm border border-emerald-500/30">
                                <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                System Operational &bull; Desa Siratsirna
                            </span>
                            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                                Selamat Datang, {{ $page.props.auth.user.name }}!
                            </h2>
                            <p class="text-sm text-slate-300 leading-relaxed">
                                Kelola agenda surat masuk dan buat surat keluar resmi secara otomatis dengan tanda tangan basah digital dan stempel siap cetak 1 halaman.
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-3 shrink-0">
                            <Link
                                :href="route('surat-keluar.create')"
                                class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-5 py-3 text-xs font-bold text-slate-950 transition hover:bg-emerald-400 shadow-lg shadow-emerald-500/30"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Generator Surat Baru
                            </Link>
                            <Link
                                :href="route('surat-masuk.index')"
                                class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-5 py-3 text-xs font-bold text-white transition hover:bg-white/20 backdrop-blur-sm border border-white/15"
                            >
                                Arsip Surat Masuk
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 transition hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Surat Keluar</span>
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-baseline justify-between">
                            <span class="text-3xl font-black text-slate-900 dark:text-white">{{ stats.totalSuratKeluar }}</span>
                            <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">+{{ stats.suratKeluarBulanIni }} bulan ini</span>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 transition hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Surat Masuk</span>
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75H6.912a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859M12 3v8.25m0 0l-3-3m3 3l3-3" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-baseline justify-between">
                            <span class="text-3xl font-black text-slate-900 dark:text-white">{{ stats.totalSuratMasuk }}</span>
                            <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">+{{ stats.suratMasukBulanIni }} bulan ini</span>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 transition hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Keluar Bulan Ini</span>
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-baseline justify-between">
                            <span class="text-3xl font-black text-slate-900 dark:text-white">{{ stats.suratKeluarBulanIni }}</span>
                            <span class="text-xs text-slate-500">Surat diterbitkan</span>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 transition hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Format Cetak</span>
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-100 text-teal-700 dark:bg-teal-950 dark:text-teal-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-baseline justify-between">
                            <span class="text-sm font-bold text-slate-900 dark:text-white">F4 / Folio & A4</span>
                            <span class="inline-flex items-center rounded bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">1 Halaman Pas</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Outgoing & Recent Incoming Sections -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Recent Outgoing Mails -->
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 text-emerald-600">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                    </svg>
                                    Surat Keluar Terbaru
                                </h3>
                                <p class="text-xs text-slate-500">Surat keluar resmi Desa Siratsirna</p>
                            </div>
                            <Link :href="route('surat-keluar.index')" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                                Lihat Semua &rarr;
                            </Link>
                        </div>

                        <div v-if="recentOutgoing.length > 0" class="space-y-3">
                            <div
                                v-for="mail in recentOutgoing"
                                :key="mail.id"
                                class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/60 p-3.5 transition hover:bg-slate-100/80 dark:border-slate-800 dark:bg-slate-800/40 dark:hover:bg-slate-800"
                            >
                                <div class="min-w-0 flex-1 pr-3">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs font-bold text-emerald-700 dark:text-emerald-400">{{ mail.nomor_surat }}</span>
                                        <span v-if="mail.sifat" class="rounded bg-slate-200/80 px-1.5 py-0.5 text-[10px] font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                            {{ mail.sifat }}
                                        </span>
                                    </div>
                                    <h4 class="mt-0.5 text-xs font-bold text-slate-900 truncate dark:text-white">{{ mail.perihal }}</h4>
                                    <div class="mt-1 flex items-center gap-3 text-[11px] text-slate-500">
                                        <span>Kepada: {{ mail.tujuan }}</span>
                                        <span>&bull;</span>
                                        <span>{{ mail.tanggal_surat }}</span>
                                    </div>
                                </div>
                                <Link
                                    :href="route('surat-keluar.show', mail.id)"
                                    class="inline-flex shrink-0 items-center gap-1 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900"
                                >
                                    Cetak
                                </Link>
                            </div>
                        </div>
                        <div v-else class="rounded-xl border border-dashed border-slate-200 p-8 text-center text-xs text-slate-500 dark:border-slate-800">
                            Belum ada surat keluar yang terbit.
                        </div>
                    </div>

                    <!-- Recent Incoming Mails -->
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 text-blue-600">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75H6.912a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859M12 3v8.25m0 0l-3-3m3 3l3-3" />
                                    </svg>
                                    Surat Masuk Terbaru
                                </h3>
                                <p class="text-xs text-slate-500">Agenda penerimaan dokumen</p>
                            </div>
                            <Link :href="route('surat-masuk.index')" class="text-xs font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400">
                                Lihat Semua &rarr;
                            </Link>
                        </div>

                        <div v-if="recentIncoming.length > 0" class="space-y-3">
                            <div
                                v-for="mail in recentIncoming"
                                :key="mail.id"
                                class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/60 p-3.5 transition hover:bg-slate-100/80 dark:border-slate-800 dark:bg-slate-800/40 dark:hover:bg-slate-800"
                            >
                                <div class="min-w-0 flex-1 pr-3">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs font-bold text-blue-700 dark:text-blue-400">No. Agenda #{{ mail.nomor_agenda }}</span>
                                        <span class="rounded bg-blue-100 px-1.5 py-0.5 text-[10px] font-semibold text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                                            {{ mail.kategori }}
                                        </span>
                                    </div>
                                    <h4 class="mt-0.5 text-xs font-bold text-slate-900 truncate dark:text-white">{{ mail.perihal }}</h4>
                                    <div class="mt-1 flex items-center gap-3 text-[11px] text-slate-500">
                                        <span>Dari: {{ mail.pengirim }}</span>
                                    </div>
                                </div>
                                <a
                                    :href="route('surat-masuk.download', mail.id)"
                                    target="_blank"
                                    class="inline-flex shrink-0 items-center gap-1 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                                >
                                    Unduh File
                                </a>
                            </div>
                        </div>
                        <div v-else class="rounded-xl border border-dashed border-slate-200 p-8 text-center text-xs text-slate-500 dark:border-slate-800">
                            Belum ada agenda surat masuk tersimpan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
