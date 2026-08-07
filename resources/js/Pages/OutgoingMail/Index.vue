<script setup>
import { reactive, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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
            router.get(route('surat-keluar.index'), filters, {
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

function deleteMail(mail) {
    if (
        !window.confirm(
            `Hapus surat "${mail.perihal}"? Seluruh berkas (logo, tanda tangan, stempel) akan ikut terhapus.`,
        )
    ) {
        return;
    }

    router.delete(route('surat-keluar.destroy', mail.id), {
        preserveScroll: true,
    });
}

const total = props.mails.meta?.total ?? 0;
</script>

<template>
    <Head title="Arsip Surat Keluar" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-primary dark:text-primary">
                        Administrasi Surat
                    </span>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white">
                        Arsip Surat Keluar
                    </h1>
                </div>
                <Link
                    :href="route('surat-keluar.create')"
                    class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-primary/90 active:scale-95"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Buat Surat Keluar Baru
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

                <!-- Main Card container -->
                <div class="rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
                    <!-- Search Header -->
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 p-5 dark:border-slate-800/80">
                        <div class="relative flex-1 max-w-md">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                            </div>
                            <input
                                v-model="filters.search"
                                type="text"
                                placeholder="Cari nomor surat, perihal, atau tujuan..."
                                class="w-full rounded-md border-slate-200/90 pl-9 pr-8 text-xs font-medium shadow-sm transition focus:border-primary focus:ring-2 focus:ring-primary/20 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200"
                            />
                            <button
                                v-if="filters.search"
                                @click="resetSearch"
                                type="button"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-sm font-bold"
                            >
                                &times;
                            </button>
                        </div>
                        <div class="inline-flex items-center gap-1.5 rounded-md border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-600 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-300">
                            <span>Total Surat:</span>
                            <span class="rounded-md bg-primary/10 px-2 py-0.5 text-primary dark:bg-primary/20 dark:text-emerald-300 font-extrabold">{{ total }}</span>
                        </div>
                    </div>

                    <!-- Mobile Card View (Tampilan Khusus HP / Handphone) -->
                    <div class="block sm:hidden divide-y divide-slate-100 dark:divide-slate-800">
                        <div
                            v-for="mail in mails.data"
                            :key="mail.id"
                            class="p-4 space-y-2 hover:bg-slate-50 dark:hover:bg-slate-800/40"
                        >
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono text-xs font-bold text-primary dark:text-primary">
                                    {{ mail.nomor_surat }}
                                </span>
                                <span
                                    class="inline-flex rounded-md px-2 py-0.5 text-[10px] font-bold"
                                    :class="
                                        mail.sifat === 'Rahasia'
                                            ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300'
                                            : mail.sifat === 'Penting'
                                              ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300'
                                              : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
                                    "
                                >
                                    {{ mail.sifat }}
                                </span>
                            </div>
                            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white leading-snug">
                                {{ mail.perihal }}
                            </h3>
                            <div class="text-xs text-slate-500 space-y-0.5">
                                <div><strong class="text-slate-700 dark:text-slate-300">Tujuan:</strong> {{ mail.tujuan }}</div>
                                <div><strong class="text-slate-700 dark:text-slate-300">Tanggal:</strong> {{ formatTanggal(mail.tanggal_surat) }}</div>
                            </div>
                            <div class="pt-2 flex items-center justify-end gap-2">
                                <Link
                                    :href="route('surat-keluar.show', mail.id)"
                                    class="flex-1 text-center inline-flex items-center justify-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white shadow-sm hover:bg-primary/50"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" />
                                    </svg>
                                    Cetak PDF
                                </Link>
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
                            Tidak ada surat keluar ditemukan.
                        </div>
                    </div>

                    <!-- Desktop Table View -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                            <thead class="bg-slate-50/80 dark:bg-slate-950">
                                <tr>
                                    <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Nomor Surat</th>
                                    <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Tanggal</th>
                                    <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Perihal</th>
                                    <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Tujuan</th>
                                    <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Sifat</th>
                                    <th class="px-5 py-3.5 text-right text-[11px] font-bold uppercase tracking-wider text-slate-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white dark:divide-slate-800 dark:bg-slate-900">
                                <tr v-for="mail in mails.data" :key="mail.id" class="transition hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                                    <td class="whitespace-nowrap px-5 py-4 text-xs font-mono font-bold text-primary dark:text-primary">
                                        {{ mail.nomor_surat }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-xs font-medium text-slate-600 dark:text-slate-300">
                                        {{ formatTanggal(mail.tanggal_surat) }}
                                    </td>
                                    <td class="max-w-xs truncate px-5 py-4 text-xs font-bold text-slate-900 dark:text-white">
                                        {{ mail.perihal }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-xs font-medium text-slate-600 dark:text-slate-300">
                                        {{ mail.tujuan }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <span
                                            class="inline-flex rounded-md px-2.5 py-1 text-[10px] font-bold"
                                            :class="
                                                mail.sifat === 'Rahasia'
                                                    ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300'
                                                    : mail.sifat === 'Penting'
                                                      ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300'
                                                      : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
                                            "
                                        >
                                            {{ mail.sifat }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right text-xs">
                                        <div class="flex justify-end items-center gap-2">
                                            <Link
                                                :href="route('surat-keluar.show', mail.id)"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 shadow-sm"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" />
                                                </svg>
                                                Cetak PDF
                                            </Link>
                                            <button
                                                type="button"
                                                @click="deleteMail(mail)"
                                                class="rounded-lg border border-red-200 bg-white p-1.5 text-xs font-semibold text-red-600 shadow-sm transition hover:bg-red-50 dark:border-red-950 dark:bg-slate-800 dark:text-red-400 dark:hover:bg-red-900/40"
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
                                    <td colspan="6" class="px-5 py-16 text-center text-xs text-slate-500">
                                        Tidak ada surat keluar ditemukan.
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
                                    ? 'border-emerald-600 bg-emerald-600 text-white'
                                    : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300'
                            "
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
