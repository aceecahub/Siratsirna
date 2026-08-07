<script setup>
import { reactive, ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UploadModal from '@/Pages/IncomingMail/Partials/UploadModal.vue';
import PreviewModal from '@/Pages/IncomingMail/Partials/PreviewModal.vue';
import { formatTanggal } from '@/lib/format';

const props = defineProps({
    mails: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const showUpload = ref(false);
const previewMail = ref(null);

const filters = reactive({
    search: props.filters.search ?? '',
    kategori: props.filters.kategori ?? '',
    tanggal_awal: props.filters.tanggal_awal ?? '',
    tanggal_akhir: props.filters.tanggal_akhir ?? '',
});

let searchTimer = null;

function applyFilters({ replace = true } = {}) {
    clearTimeout(searchTimer);

    router.get(route('surat-masuk.index'), filters, {
        preserveState: true,
        preserveScroll: true,
        replace,
    });
}

watch(
    () => filters.search,
    () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => applyFilters(), 350);
    },
);

function onFilterChange() {
    applyFilters();
}

function resetFilters() {
    filters.search = '';
    filters.kategori = '';
    filters.tanggal_awal = '';
    filters.tanggal_akhir = '';
    applyFilters();
}

function gotoPage(url) {
    if (!url) return;

    router.get(url, {}, { preserveState: true, preserveScroll: true });
}

function deleteMail(mail) {
    if (
        !window.confirm(
            `Hapus surat "${mail.perihal}" beserta berkas scan-nya? Tindakan ini tidak dapat dibatalkan.`,
        )
    ) {
        return;
    }

    router.delete(route('surat-masuk.destroy', mail.id), {
        preserveScroll: true,
    });
}

const total = props.mails.meta?.total ?? 0;
</script>

<template>
    <Head title="Arsip Surat Masuk" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-primary dark:text-primary">
                        Administrasi Surat
                    </span>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white">
                        Arsip Surat Masuk
                    </h1>
                </div>
                <button
                    type="button"
                    @click="showUpload = true"
                    class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-primary/90 active:scale-95"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Arsip Surat Masuk
                </button>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Alert Success -->
                <div
                    v-if="$page.props.flash?.success"
                    class="rounded-md border border-primary/30 bg-primary/5 px-4 py-3 text-xs font-semibold text-primary dark:border-primary/20 dark:bg-primary/10 dark:text-emerald-200 shadow-sm"
                >
                    {{ $page.props.flash.success }}
                </div>

                <!-- Main Container Card -->
                <div class="rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
                    <!-- Filters Grid -->
                    <div class="grid grid-cols-1 gap-4 border-b border-slate-100 p-5 md:grid-cols-12 dark:border-slate-800">
                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Cari Surat Masuk</label>
                            <input
                                v-model="filters.search"
                                type="text"
                                placeholder="Nomor agenda, pengirim, perihal..."
                                class="w-full rounded-md border-slate-200 text-xs font-medium focus:border-primary focus:ring-primary/20 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200"
                            />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Kategori</label>
                            <select
                                v-model="filters.kategori"
                                @change="onFilterChange"
                                class="w-full rounded-md border-slate-200 text-xs font-bold text-slate-800 focus:border-primary focus:ring-primary/20 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200"
                            >
                                <option value="">Semua Kategori</option>
                                <option
                                    v-for="category in categories"
                                    :key="category"
                                    :value="category"
                                >
                                    {{ category }}
                                </option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Diterima Dari</label>
                            <input
                                v-model="filters.tanggal_awal"
                                type="date"
                                @change="onFilterChange"
                                class="w-full rounded-md border-slate-200 text-xs font-medium focus:border-primary focus:ring-primary/20 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200"
                            />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Sampai</label>
                            <input
                                v-model="filters.tanggal_akhir"
                                type="date"
                                @change="onFilterChange"
                                class="w-full rounded-md border-slate-200 text-xs font-medium focus:border-primary focus:ring-primary/20 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200"
                            />
                        </div>

                        <div class="flex items-end md:col-span-2">
                            <button
                                type="button"
                                @click="resetFilters"
                                class="w-full rounded-md border border-slate-300 bg-white py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                            >
                                Reset Filter
                            </button>
                        </div>
                    </div>

                    <!-- Mobile Card View (Tampilan Handphone) -->
                    <div class="block sm:hidden divide-y divide-slate-100 dark:divide-slate-800">
                        <div
                            v-for="mail in mails.data"
                            :key="mail.id"
                            class="p-4 space-y-2 hover:bg-slate-50 dark:hover:bg-slate-800/40"
                        >
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono text-xs font-bold text-primary dark:text-primary">
                                    No. Agenda #{{ mail.nomor_agenda }}
                                </span>
                                <span class="rounded bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                                    {{ mail.kategori }}
                                </span>
                            </div>
                            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white leading-snug">
                                {{ mail.perihal }}
                            </h3>
                            <div class="text-xs text-slate-500 space-y-0.5">
                                <div><strong class="text-slate-700 dark:text-slate-300">Pengirim:</strong> {{ mail.pengirim }}</div>
                                <div v-if="mail.nomor_surat_asal"><strong class="text-slate-700 dark:text-slate-300">No. Asal:</strong> {{ mail.nomor_surat_asal }}</div>
                                <div><strong class="text-slate-700 dark:text-slate-300">Diterima:</strong> {{ formatTanggal(mail.tanggal_diterima) }}</div>
                            </div>
                            <div class="pt-2 flex items-center justify-end gap-2">
                                <button
                                    type="button"
                                    @click="previewMail = mail"
                                    class="flex-1 rounded-lg border border-slate-300 bg-white py-2 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                                >
                                    Preview
                                </button>
                                <a
                                    :href="route('surat-masuk.download', mail.id)"
                                    target="_blank"
                                    class="flex-1 text-center rounded-lg bg-blue-600 py-2 text-xs font-bold text-white shadow-sm hover:bg-primary/50"
                                >
                                    Unduh
                                </a>
                                <button
                                    type="button"
                                    @click="deleteMail(mail)"
                                    class="rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-600 shadow-sm dark:border-red-950 dark:bg-slate-800 dark:text-red-400"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                        <div v-if="mails.data.length === 0" class="p-8 text-center text-xs text-slate-500">
                            Tidak ada surat masuk ditemukan.
                        </div>
                    </div>

                    <!-- Desktop Table View -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                            <thead class="bg-slate-50/80 dark:bg-slate-950">
                                <tr>
                                    <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Nomor Agenda</th>
                                    <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Diterima</th>
                                    <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Pengirim</th>
                                    <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Perihal</th>
                                    <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Kategori</th>
                                    <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">File</th>
                                    <th class="px-5 py-3.5 text-right text-[11px] font-bold uppercase tracking-wider text-slate-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white dark:divide-slate-800 dark:bg-slate-900">
                                <tr v-for="mail in mails.data" :key="mail.id" class="transition hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                                    <td class="whitespace-nowrap px-5 py-4 text-xs font-mono font-bold text-primary dark:text-primary">
                                        #{{ mail.nomor_agenda }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-xs font-medium text-slate-600 dark:text-slate-300">
                                        <div>{{ formatTanggal(mail.tanggal_diterima) }}</div>
                                        <div class="text-[10px] text-slate-400">Surat: {{ formatTanggal(mail.tanggal_surat) }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-xs font-medium text-slate-800 dark:text-slate-200">
                                        {{ mail.pengirim }}
                                        <div v-if="mail.nomor_surat_asal" class="text-[10px] text-slate-400">No. {{ mail.nomor_surat_asal }}</div>
                                    </td>
                                    <td class="max-w-xs truncate px-5 py-4 text-xs font-bold text-slate-900 dark:text-white">
                                        {{ mail.perihal }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <span class="inline-flex rounded-md bg-blue-50 px-2.5 py-1 text-[10px] font-bold text-primary dark:bg-blue-950 dark:text-blue-300">
                                            {{ mail.kategori }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-xs text-slate-500">
                                        <span class="inline-flex items-center gap-1.5">
                                            <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                                {{ mail.file_extension }}
                                            </span>
                                            {{ mail.file_size_formatted }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right text-xs">
                                        <div class="flex justify-end items-center gap-2">
                                            <button
                                                type="button"
                                                @click="previewMail = mail"
                                                class="rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                                            >
                                                Preview
                                            </button>
                                            <a
                                                :href="route('surat-masuk.download', mail.id)"
                                                target="_blank"
                                                class="rounded-lg bg-blue-600 px-2.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-primary/50"
                                            >
                                                Unduh
                                            </a>
                                            <button
                                                type="button"
                                                @click="deleteMail(mail)"
                                                class="rounded-lg border border-red-200 bg-white p-1.5 text-xs font-semibold text-red-600 shadow-sm hover:bg-red-50 dark:border-red-950 dark:bg-slate-800 dark:text-red-400"
                                                title="Hapus Surat"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="mails.data.length === 0">
                                    <td colspan="7" class="px-5 py-16 text-center text-xs text-slate-500">
                                        Tidak ada surat masuk yang cocok dengan filter.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="mails.meta?.last_page > 1"
                        class="flex flex-wrap items-center justify-center gap-1 border-t border-slate-100 p-4 dark:border-slate-800"
                    >
                        <button
                            v-for="(link, index) in mails.links"
                            :key="index"
                            type="button"
                            :disabled="!link.url"
                            @click="gotoPage(link.url)"
                            class="rounded-lg border px-3 py-1.5 text-xs font-bold transition disabled:opacity-40"
                            :class="
                                link.active
                                    ? 'border-blue-600 bg-blue-600 text-white'
                                    : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300'
                            "
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>

        <UploadModal
            :categories="categories"
            :show="showUpload"
            @close="showUpload = false"
        />

        <PreviewModal
            :mail="previewMail"
            @close="previewMail = null"
        />
    </AuthenticatedLayout>
</template>
