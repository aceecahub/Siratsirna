<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import HeirLetterPreview from '@/Components/HeirLetterPreview.vue';
import { formatTanggal } from '@/lib/format';
import { printSheet } from '@/lib/print';

const props = defineProps({
    letter: { type: Object, required: true },
});

const paper   = ref('f4');
const compact = ref(false);
</script>

<template>
    <Head :title="`Surat Ket. Ahli Waris – Almarhum ${letter.nama_almarhum}`" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-blue-600 dark:text-blue-400">{{ letter.nomor_surat }}</span>
                        <span class="rounded bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700 dark:bg-blue-900/60 dark:text-blue-300">Ahli Waris</span>
                    </div>
                    <h1 class="text-xl font-black text-slate-900 dark:text-white mt-1">Almarhum: {{ letter.nama_almarhum }}</h1>
                </div>
                <div class="flex items-center gap-2 print:hidden">
                    <Link :href="route('surat-ahli-waris.index')"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        &larr; Kembali
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8 print:p-0">
            <div class="mx-auto max-w-[980px] px-4 sm:px-6 lg:px-8 print:max-w-none print:px-0">

                <div v-if="$page.props.flash?.success"
                    class="mb-6 rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800 dark:border-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-200 shadow-sm">
                    {{ $page.props.flash.success }}
                </div>

                <div class="mb-6 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm print:hidden dark:border-slate-800 dark:bg-slate-900 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 dark:border-slate-800 dark:bg-slate-800/80">
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Kertas:</span>
                            <select v-model="paper" class="rounded-lg border-slate-300 py-1 text-xs font-bold text-slate-800 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                                <option value="f4">F4 / Folio (210×330 mm)</option>
                                <option value="a4">A4 (210×297 mm)</option>
                            </select>
                        </div>
                        <button type="button" @click="compact = !compact"
                            :class="[compact ? 'bg-blue-100 border-blue-300 text-blue-800 dark:bg-blue-950 dark:border-blue-700 dark:text-blue-300' : 'bg-white border-slate-200 text-slate-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-300',
                                'inline-flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-bold transition shadow-sm']">
                            Mode Ringkas
                        </button>
                        <span class="hidden text-xs font-medium text-slate-500 dark:text-slate-400 sm:block">
                            {{ formatTanggal(letter.tanggal_surat) }}
                        </span>
                    </div>
                    <button type="button" @click="printSheet()"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md transition hover:from-blue-500 active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" />
                        </svg>
                        Cetak / Ekspor PDF
                    </button>
                </div>

                <div class="rounded-2xl bg-slate-200/70 p-4 sm:p-6 print:p-0 print:bg-transparent dark:bg-slate-900/60 shadow-inner">
                    <HeirLetterPreview :letter="letter" :paper="paper" :compact="compact" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
