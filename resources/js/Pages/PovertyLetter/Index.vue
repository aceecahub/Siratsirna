<script setup>
import { reactive, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FlashAlert from '@/Components/FlashAlert.vue';
import { formatTanggal } from '@/lib/format';

const props = defineProps({
    letters: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const filters = reactive({ search: props.filters.search ?? '' });
let timer = null;

watch(() => filters.search, () => {
    clearTimeout(timer);
    timer = setTimeout(() => {
        router.get(route('surat-tidak-mampu.index'), filters, { preserveState: true, preserveScroll: true, replace: true });
    }, 350);
});

function gotoPage(url) {
    if (url) router.get(url, {}, { preserveState: true, preserveScroll: true });
}

function deleteLetter(letter) {
    if (!window.confirm(`Hapus surat atas nama "${letter.nama_warga}"? Data tidak bisa dikembalikan.`)) return;
    router.delete(route('surat-tidak-mampu.destroy', letter.id), { preserveScroll: true });
}

const total = props.letters.meta?.total ?? 0;
</script>

<template>
    <Head title="Surat Keterangan Tidak Mampu" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                        <span>Beranda</span>
                        <span class="text-slate-300 dark:text-slate-600">/</span>
                        <span>Pembuatan Surat</span>
                        <span class="text-slate-300 dark:text-slate-600">/</span>
                        <span class="text-slate-700 dark:text-slate-300 font-medium">SKTM</span>
                    </div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Surat Keterangan Tidak Mampu</h1>
                </div>
                <Link :href="route('surat-tidak-mampu.create')"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-primary/90 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary/40">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Buat Surat
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-5">
                <FlashAlert :message="$page.props.flash?.success" type="success" />

                <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 px-4 py-3">
                        <div class="relative flex-1 min-w-[200px] max-w-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400 pointer-events-none"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                            <input v-model="filters.search" type="text" placeholder="Cari nama, nomor surat..."
                                class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-xs font-medium text-slate-800 placeholder-slate-400 transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">
                            <span class="font-semibold text-slate-800 dark:text-slate-200 tabular-nums">{{ total }}</span> surat
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">No. Surat</th>
                                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Nama Warga</th>
                                    <th class="hidden px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 md:table-cell">Pekerjaan</th>
                                    <th class="hidden px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 md:table-cell">Tanggal</th>
                                    <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr v-if="letters.data?.length === 0">
                                    <td colspan="5" class="py-20 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor" class="h-12 w-12 text-slate-300 dark:text-slate-700"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Belum ada surat SKTM</p>
                                            <Link :href="route('surat-tidak-mampu.create')" class="text-xs text-primary hover:underline font-medium">Buat surat pertama →</Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-for="letter in letters.data" :key="letter.id" class="group hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="whitespace-nowrap px-5 py-3.5">
                                        <span class="font-mono text-xs font-semibold text-primary dark:text-primary">{{ letter.nomor_surat }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="text-xs font-semibold text-slate-900 dark:text-white">{{ letter.nama_warga }}</div>
                                    </td>
                                    <td class="hidden px-5 py-3.5 text-xs text-slate-600 dark:text-slate-400 md:table-cell">
                                        {{ letter.pekerjaan ?? '-' }}
                                    </td>
                                    <td class="hidden whitespace-nowrap px-5 py-3.5 text-xs text-slate-600 dark:text-slate-400 md:table-cell">
                                        {{ formatTanggal(letter.tanggal_surat) }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1.5 opacity-60 group-hover:opacity-100 transition-opacity">
                                            <Link :href="route('surat-tidak-mampu.show', letter.id)"
                                                class="inline-flex items-center gap-1.5 rounded-md bg-slate-900 px-3 py-1.5 text-[11px] font-medium text-white hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.641 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                Lihat & Cetak
                                            </Link>
                                            <button type="button" @click="deleteLetter(letter)"
                                                class="inline-flex items-center rounded-md border border-red-200 bg-white px-2 py-1.5 text-red-600 hover:bg-red-50 dark:border-red-900/50 dark:bg-slate-800 dark:text-red-400 transition" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="letters.meta?.last_page > 1" class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 py-3 dark:border-slate-800">
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">
                            Menampilkan <span class="font-semibold text-slate-700 dark:text-slate-300">{{ letters.meta.from ?? 0 }}–{{ letters.meta.to ?? 0 }}</span> dari {{ letters.meta.total }}
                        </span>
                        <div class="flex items-center gap-1">
                            <button v-for="link in letters.meta.links" :key="link.label" type="button" :disabled="!link.url" @click="gotoPage(link.url)" v-html="link.label"
                                :class="['min-w-[32px] rounded-md border px-2.5 py-1 text-[11px] font-medium transition disabled:opacity-40 disabled:cursor-not-allowed', link.active ? 'border-primary bg-primary text-white' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400 dark:hover:bg-slate-800']" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
