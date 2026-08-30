<script setup>
import { reactive, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FlashAlert from '@/Components/FlashAlert.vue';
import { formatTanggal } from '@/lib/format';

const props = defineProps({
    mails:   { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const filters = reactive({ search: props.filters.search ?? '' });
let searchTimer = null;

watch(() => filters.search, () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get(route('surat-keluar.index'), filters, { preserveState: true, preserveScroll: true, replace: true });
    }, 350);
});

function gotoPage(url) {
    if (!url) return;
    router.get(url, {}, { preserveState: true, preserveScroll: true });
}

function deleteMail(mail) {
    if (!window.confirm(`Hapus surat "${mail.perihal}"? Seluruh berkas akan ikut terhapus.`)) return;
    router.delete(route('surat-keluar.destroy', mail.id), { preserveScroll: true });
}

const total = props.mails.meta?.total ?? 0;

const sifatBadge = (sifat) => {
    if (sifat === 'Rahasia') return 'bg-red-50 text-red-700 ring-red-200 dark:bg-red-950/40 dark:text-red-300 dark:ring-red-800/40';
    if (sifat === 'Penting') return 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:ring-amber-800/40';
    return 'bg-slate-100 text-slate-600 ring-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:ring-slate-700';
};
</script>

<template>
    <Head title="Arsip Surat Keluar" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                        <span>Beranda</span>
                        <span class="text-slate-300 dark:text-slate-600">/</span>
                        <span class="text-slate-700 dark:text-slate-300 font-medium">Surat Keluar</span>
                    </div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Arsip Surat Keluar</h1>
                </div>
                <Link
                    :href="route('surat-keluar.create')"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-primary/90 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary/40"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Buat Surat Keluar
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-5">

                <FlashAlert :message="$page.props.flash?.success" type="success" />

                <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">

                    <!-- Toolbar -->
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 px-4 py-3">
                        <div class="relative flex-1 min-w-[200px] max-w-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400 pointer-events-none"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                            <input
                                v-model="filters.search"
                                type="text"
                                placeholder="Cari nomor surat, perihal, tujuan..."
                                class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-xs font-medium text-slate-800 placeholder-slate-400 transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900"
                            />
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                            <span class="font-semibold text-slate-800 dark:text-slate-200 tabular-nums">{{ total }}</span> surat
                        </div>
                    </div>

                    <!-- Desktop Table -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Nomor Surat</th>
                                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tanggal</th>
                                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Perihal</th>
                                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tujuan</th>
                                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Sifat</th>
                                    <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr v-if="mails.data.length === 0">
                                    <td colspan="6" class="py-20 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor" class="h-12 w-12 text-slate-300 dark:text-slate-700"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Tidak ada surat keluar</p>
                                            <Link :href="route('surat-keluar.create')" class="text-xs text-primary hover:underline font-medium">Buat surat keluar pertama →</Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-for="mail in mails.data" :key="mail.id" class="group hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="whitespace-nowrap px-5 py-3.5">
                                        <span class="font-mono text-xs font-semibold text-primary dark:text-primary">{{ mail.nomor_surat }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-xs text-slate-600 dark:text-slate-400">
                                        {{ formatTanggal(mail.tanggal_surat) }}
                                    </td>
                                    <td class="px-5 py-3.5 max-w-[200px]">
                                        <div class="text-xs font-semibold text-slate-900 dark:text-white truncate">{{ mail.perihal }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-xs text-slate-600 dark:text-slate-400">
                                        {{ mail.tujuan }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3.5">
                                        <span :class="['inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-semibold ring-1 ring-inset', sifatBadge(mail.sifat)]">
                                            {{ mail.sifat }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1.5 opacity-60 group-hover:opacity-100 transition-opacity">
                                            <Link :href="route('surat-keluar.show', mail.id)"
                                                class="inline-flex items-center gap-1.5 rounded-md bg-slate-900 px-3 py-1.5 text-[11px] font-medium text-white hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" /></svg>
                                                Cetak PDF
                                            </Link>
                                            <button type="button" @click="deleteMail(mail)"
                                                class="inline-flex items-center rounded-md border border-red-200 bg-white px-2 py-1.5 text-red-600 hover:bg-red-50 dark:border-red-900/50 dark:bg-slate-800 dark:text-red-400 transition" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile -->
                    <div class="block sm:hidden divide-y divide-slate-100 dark:divide-slate-800">
                        <div v-if="mails.data.length === 0" class="py-12 text-center text-sm text-slate-400">Tidak ada surat keluar.</div>
                        <div v-for="mail in mails.data" :key="mail.id" class="p-4 space-y-2.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono text-xs font-semibold text-primary">{{ mail.nomor_surat }}</span>
                                <span :class="['inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-semibold ring-1 ring-inset', sifatBadge(mail.sifat)]">{{ mail.sifat }}</span>
                            </div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white leading-snug">{{ mail.perihal }}</p>
                            <div class="text-xs text-slate-500"><span class="font-medium text-slate-700 dark:text-slate-300">Tujuan:</span> {{ mail.tujuan }}</div>
                            <div class="flex gap-2 pt-1">
                                <Link :href="route('surat-keluar.show', mail.id)" class="flex-1 text-center rounded-lg bg-slate-900 py-2 text-xs font-medium text-white dark:bg-slate-100 dark:text-slate-900">Cetak PDF</Link>
                                <button type="button" @click="deleteMail(mail)" class="rounded-lg border border-red-200 bg-white px-3 py-2 text-xs text-red-600 dark:border-red-900/50 dark:bg-slate-800 dark:text-red-400">Hapus</button>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="mails.meta?.last_page > 1" class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 py-3 dark:border-slate-800">
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">
                            Menampilkan <span class="font-semibold text-slate-700 dark:text-slate-300">{{ mails.meta.from }}–{{ mails.meta.to }}</span> dari {{ mails.meta.total }}
                        </span>
                        <div class="flex items-center gap-1">
                            <button v-for="(link, i) in mails.links" :key="i" type="button" :disabled="!link.url" @click="gotoPage(link.url)"
                                :class="['min-w-[32px] rounded-md border px-2.5 py-1 text-[11px] font-medium transition disabled:opacity-40 disabled:cursor-not-allowed', link.active ? 'border-primary bg-primary text-white' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400 dark:hover:bg-slate-800']"
                                v-html="link.label" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
