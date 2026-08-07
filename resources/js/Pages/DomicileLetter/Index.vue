<script setup>
import { reactive, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { formatTanggal } from '@/lib/format';

const props = defineProps({
    letters: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const filters = reactive({
    search: props.filters.search ?? '',
});

let searchTimer = null;

watch(
    () => filters.search,
    () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            router.get(route('surat-keterangan-domisili.index'), filters, {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            });
        }, 350);
    },
);

function resetSearch() {
    filters.search = '';
}

function gotoPage(url) {
    if (!url) return;
    router.get(url, {}, { preserveState: true, preserveScroll: true });
}

function deleteLetter(letter) {
    if (
        !window.confirm(
            `Hapus Surat Keterangan Domisili atas nama "${letter.nama_warga}"? Data tidak bisa dikembalikan.`,
        )
    ) {
        return;
    }

    router.delete(route('surat-keterangan-domisili.destroy', letter.id), {
        preserveScroll: true,
    });
}

const total = props.letters.meta?.total ?? 0;
</script>

<template>
    <Head title="Surat Keterangan Domisili" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-primary dark:text-primary">
                        Pelayanan Surat
                    </span>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white">
                        Surat Keterangan Domisili
                    </h1>
                </div>
                <Link
                    :href="route('surat-keterangan-domisili.create')"
                    class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-primary/90 active:scale-95"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Buat Surat Keterangan Domisili
                </Link>
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

                <!-- Search & Stats -->
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="relative max-w-xs w-full">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z" />
                        </svg>
                        <input
                            id="search-domicile"
                            v-model="filters.search"
                            type="search"
                            placeholder="Cari nama warga, NIK, nomor surat..."
                            class="block w-full rounded-md border-slate-300/90 py-2 pl-9 pr-9 text-xs font-medium shadow-sm transition focus:border-primary focus:ring-2 focus:ring-primary/20 dark:border-slate-700/80 dark:bg-slate-900 dark:text-slate-200"
                        />
                        <button
                            v-if="filters.search"
                            type="button"
                            @click="resetSearch"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3.5 w-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                        Total: <span class="font-black text-slate-800 dark:text-slate-200">{{ total }}</span> surat
                    </div>
                </div>

                <!-- Table -->
                <div class="rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-800/80 dark:bg-slate-900/90 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                            <thead>
                                <tr class="bg-slate-50/80 dark:bg-slate-800/60">
                                    <th class="px-4 py-3 text-left text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">No. Surat</th>
                                    <th class="px-4 py-3 text-left text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Nama Warga</th>
                                    <th class="hidden px-4 py-3 text-left text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 sm:table-cell">NIK</th>
                                    <th class="hidden px-4 py-3 text-left text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 md:table-cell">Tanggal Surat</th>
                                    <th class="px-4 py-3 text-right text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                                <tr
                                    v-if="letters.data?.length === 0"
                                >
                                    <td colspan="5" class="py-16 text-center text-xs font-medium text-slate-400 dark:text-slate-500">
                                        <div class="flex flex-col items-center gap-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10 text-slate-300 dark:text-slate-700">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                            <span>Belum ada Surat Keterangan Domisili</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr
                                    v-for="letter in letters.data"
                                    :key="letter.id"
                                    class="group transition hover:bg-primary/5/30 dark:hover:bg-indigo-950/20"
                                >
                                    <td class="px-4 py-3">
                                        <span class="font-mono text-xs font-bold text-primary dark:text-primary">
                                            {{ letter.nomor_surat }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-xs font-bold text-slate-900 dark:text-slate-100">
                                            {{ letter.nama_warga }}
                                        </div>
                                        <div class="text-[10px] font-medium text-slate-500 dark:text-slate-400 sm:hidden">
                                            NIK: {{ letter.nik }}
                                        </div>
                                    </td>
                                    <td class="hidden px-4 py-3 sm:table-cell">
                                        <span class="font-mono text-xs text-slate-600 dark:text-slate-400">{{ letter.nik }}</span>
                                    </td>
                                    <td class="hidden px-4 py-3 md:table-cell">
                                        <span class="text-xs text-slate-600 dark:text-slate-400">
                                            {{ formatTanggal(letter.tanggal_surat) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link
                                                :href="route('surat-keterangan-domisili.show', letter.id)"
                                                class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 px-2.5 py-1.5 text-[10px] font-bold text-primary transition hover:bg-indigo-100 dark:bg-indigo-950/60 dark:text-indigo-300 dark:hover:bg-indigo-950"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.641 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Lihat &amp; Cetak
                                            </Link>
                                            <button
                                                type="button"
                                                @click="deleteLetter(letter)"
                                                class="inline-flex items-center gap-1 rounded-lg bg-red-50 px-2.5 py-1.5 text-[10px] font-bold text-red-700 transition hover:bg-red-100 dark:bg-red-950/40 dark:text-red-400 dark:hover:bg-red-950/70"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="letters.meta?.last_page > 1"
                        class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-4 py-3 dark:border-slate-800"
                    >
                        <span class="text-[10px] font-medium text-slate-500 dark:text-slate-400">
                            Menampilkan {{ letters.meta.from ?? 0 }}–{{ letters.meta.to ?? 0 }} dari {{ letters.meta.total }} surat
                        </span>
                        <div class="flex items-center gap-1.5">
                            <button
                                v-for="link in letters.meta.links"
                                :key="link.label"
                                type="button"
                                @click="gotoPage(link.url)"
                                :disabled="!link.url"
                                v-html="link.label"
                                :class="[
                                    link.active
                                        ? 'bg-primary text-white border-primary font-bold font-bold'
                                        : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-400 dark:border-slate-700 dark:hover:bg-slate-800',
                                    'min-w-[28px] rounded-lg border px-2 py-1 text-[10px] transition disabled:cursor-not-allowed disabled:opacity-40',
                                ]"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
