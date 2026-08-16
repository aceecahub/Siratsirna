<script setup>
import { reactive, ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FlashAlert from '@/Components/FlashAlert.vue';
import UploadModal from '@/Pages/IncomingMail/Partials/UploadModal.vue';
import PreviewModal from '@/Pages/IncomingMail/Partials/PreviewModal.vue';
import { formatTanggal } from '@/lib/format';

const props = defineProps({
    mails:      { type: Object, required: true },
    filters:    { type: Object, default: () => ({}) },
    categories: { type: Array, default: () => [] },
});

const showUpload   = ref(false);
const previewMail  = ref(null);

const filters = reactive({
    search:        props.filters.search        ?? '',
    kategori:      props.filters.kategori      ?? '',
    tanggal_awal:  props.filters.tanggal_awal  ?? '',
    tanggal_akhir: props.filters.tanggal_akhir ?? '',
});

let searchTimer = null;

function applyFilters({ replace = true } = {}) {
    clearTimeout(searchTimer);
    router.get(route('surat-masuk.index'), filters, { preserveState: true, preserveScroll: true, replace });
}

watch(() => filters.search, () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => applyFilters(), 350);
});

function onFilterChange() { applyFilters(); }

function resetFilters() {
    filters.search = filters.kategori = filters.tanggal_awal = filters.tanggal_akhir = '';
    applyFilters();
}

function gotoPage(url) {
    if (!url) return;
    router.get(url, {}, { preserveState: true, preserveScroll: true });
}

function deleteMail(mail) {
    if (!window.confirm(`Hapus surat "${mail.perihal}" beserta berkas scan-nya? Tindakan ini tidak dapat dibatalkan.`)) return;
    router.delete(route('surat-masuk.destroy', mail.id), { preserveScroll: true });
}

const total = props.mails.meta?.total ?? 0;
const hasActiveFilter = () => filters.search || filters.kategori || filters.tanggal_awal || filters.tanggal_akhir;
</script>

<template>
    <Head title="Arsip Surat Masuk" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                        <span>Beranda</span>
                        <span class="text-slate-300 dark:text-slate-600">/</span>
                        <span class="text-slate-700 dark:text-slate-300 font-medium">Surat Masuk</span>
                    </div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Arsip Surat Masuk</h1>
                </div>
                <button
                    type="button"
                    @click="showUpload = true"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-primary/90 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary/40"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                    Arsip Surat Masuk
                </button>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-5">

                <FlashAlert :message="$page.props.flash?.success" type="success" />

                <!-- Main Card -->
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">

                    <!-- Toolbar -->
                    <div class="border-b border-slate-100 dark:border-slate-800 p-4">
                        <div class="flex flex-wrap items-end gap-3">
                            <!-- Search -->
                            <div class="relative flex-1 min-w-[200px] max-w-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400 pointer-events-none"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                                <input
                                    v-model="filters.search"
                                    type="text"
                                    placeholder="Cari nomor, pengirim, perihal..."
                                    class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-xs font-medium text-slate-800 placeholder-slate-400 transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900"
                                />
                            </div>
                            <!-- Kategori -->
                            <div class="min-w-[140px]">
                                <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Kategori</label>
                                <select
                                    v-model="filters.kategori"
                                    @change="onFilterChange"
                                    class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 px-3 text-xs font-medium text-slate-800 transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900"
                                >
                                    <option value="">Semua</option>
                                    <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                                </select>
                            </div>
                            <!-- Tanggal Dari -->
                            <div>
                                <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Dari</label>
                                <input v-model="filters.tanggal_awal" type="date" @change="onFilterChange"
                                    class="rounded-lg border border-slate-200 bg-slate-50 py-2 px-3 text-xs font-medium text-slate-800 transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            </div>
                            <!-- Tanggal Sampai -->
                            <div>
                                <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 mb-1">Sampai</label>
                                <input v-model="filters.tanggal_akhir" type="date" @change="onFilterChange"
                                    class="rounded-lg border border-slate-200 bg-slate-50 py-2 px-3 text-xs font-medium text-slate-800 transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:bg-slate-900" />
                            </div>
                            <!-- Reset -->
                            <button v-if="hasActiveFilter()" @click="resetFilters" type="button"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                Reset
                            </button>
                            <!-- Count -->
                            <div class="ml-auto flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                <span class="font-semibold text-slate-800 dark:text-slate-200 tabular-nums">{{ total }}</span> surat
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Table -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">No. Agenda</th>
                                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Diterima</th>
                                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pengirim</th>
                                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Perihal</th>
                                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Kategori</th>
                                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">File</th>
                                    <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr v-if="mails.data.length === 0">
                                    <td colspan="7" class="py-20 text-center">
                                        <div class="flex flex-col items-center gap-3 text-slate-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor" class="h-12 w-12 text-slate-300 dark:text-slate-700"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75H6.912a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859M12 3v8.25m0 0l-3-3m3 3l3-3" /></svg>
                                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Tidak ada surat masuk</p>
                                            <p class="text-xs text-slate-400 dark:text-slate-500">Coba ubah filter atau arsipkan surat baru.</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-for="mail in mails.data" :key="mail.id" class="group hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="whitespace-nowrap px-5 py-3.5">
                                        <span class="font-mono text-xs font-semibold text-primary dark:text-primary">#{{ mail.nomor_agenda }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3.5">
                                        <div class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ formatTanggal(mail.tanggal_diterima) }}</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">Surat: {{ formatTanggal(mail.tanggal_surat) }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 max-w-[160px]">
                                        <div class="text-xs font-medium text-slate-800 dark:text-slate-200 truncate">{{ mail.pengirim }}</div>
                                        <div v-if="mail.nomor_surat_asal" class="text-[10px] text-slate-400 font-mono mt-0.5">{{ mail.nomor_surat_asal }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 max-w-[220px]">
                                        <div class="text-xs font-semibold text-slate-900 dark:text-white truncate">{{ mail.perihal }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3.5">
                                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700 ring-1 ring-inset ring-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:ring-blue-800/40">
                                            {{ mail.kategori }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1 text-xs text-slate-500 dark:text-slate-400">
                                            <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold uppercase text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ mail.file_extension }}</span>
                                            {{ mail.file_size_formatted }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1.5 opacity-60 group-hover:opacity-100 transition-opacity">
                                            <button type="button" @click="previewMail = mail"
                                                class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-[11px] font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 transition">
                                                Preview
                                            </button>
                                            <a :href="route('surat-masuk.download', mail.id)" target="_blank"
                                                class="inline-flex items-center gap-1 rounded-md bg-primary px-2.5 py-1.5 text-[11px] font-medium text-white hover:bg-primary/90 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                                Unduh
                                            </a>
                                            <button type="button" @click="deleteMail(mail)"
                                                class="inline-flex items-center rounded-md border border-red-200 bg-white px-2 py-1.5 text-[11px] font-medium text-red-600 hover:bg-red-50 dark:border-red-900/50 dark:bg-slate-800 dark:text-red-400 transition" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="block sm:hidden divide-y divide-slate-100 dark:divide-slate-800">
                        <div v-if="mails.data.length === 0" class="py-16 text-center text-sm text-slate-400">Tidak ada surat masuk ditemukan.</div>
                        <div v-for="mail in mails.data" :key="mail.id" class="p-4 space-y-2.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono text-xs font-semibold text-primary">#{{ mail.nomor_agenda }}</span>
                                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700 ring-1 ring-inset ring-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:ring-blue-800/40">{{ mail.kategori }}</span>
                            </div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white leading-snug">{{ mail.perihal }}</p>
                            <div class="text-xs text-slate-500 space-y-0.5">
                                <div><span class="font-medium text-slate-700 dark:text-slate-300">Pengirim:</span> {{ mail.pengirim }}</div>
                                <div><span class="font-medium text-slate-700 dark:text-slate-300">Diterima:</span> {{ formatTanggal(mail.tanggal_diterima) }}</div>
                            </div>
                            <div class="flex gap-2 pt-1">
                                <button type="button" @click="previewMail = mail" class="flex-1 rounded-lg border border-slate-200 bg-white py-2 text-xs font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">Preview</button>
                                <a :href="route('surat-masuk.download', mail.id)" target="_blank" class="flex-1 text-center rounded-lg bg-primary py-2 text-xs font-medium text-white">Unduh</a>
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
                            <button
                                v-for="(link, i) in mails.links" :key="i"
                                type="button"
                                :disabled="!link.url"
                                @click="gotoPage(link.url)"
                                :class="[
                                    'min-w-[32px] rounded-md border px-2.5 py-1 text-[11px] font-medium transition disabled:opacity-40 disabled:cursor-not-allowed',
                                    link.active
                                        ? 'border-primary bg-primary text-white'
                                        : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400 dark:hover:bg-slate-800'
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <UploadModal :categories="categories" :show="showUpload" @close="showUpload = false" />
        <PreviewModal :mail="previewMail" @close="previewMail = null" />
    </AuthenticatedLayout>
</template>
