<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FlashAlert from '@/Components/FlashAlert.vue';
import LossLetterPreview from '@/Components/LossLetterPreview.vue';
import { formatTanggal } from '@/lib/format';
import { printSheet } from '@/lib/print';

const props = defineProps({ letter: { type: Object, required: true } });

const paper = ref('f4');
const isCompact = ref(false);

async function printLetter() { await printSheet(); }
</script>

<template>
    <Head :title="`Surat Ket. Kehilangan – ${letter.nama_warga}`" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 mb-1">
                        <Link :href="route('surat-keterangan-kehilangan.index')" class="hover:text-primary transition">Kehilangan</Link>
                        <span class="text-slate-300 dark:text-slate-600">/</span>
                        <span class="font-mono text-slate-700 dark:text-slate-300">{{ letter.nomor_surat }}</span>
                    </div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">{{ letter.nama_warga }}</h1>
                </div>
                <div class="flex items-center gap-2 print:hidden">
                    <Link :href="route('surat-keterangan-kehilangan.index')"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 shadow-sm hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 transition">
                        ← Kembali
                    </Link>
                    <Link :href="route('surat-keterangan-kehilangan.edit', letter.id)"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-amber-200 bg-amber-50 px-3.5 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-100 dark:border-amber-900/50 dark:bg-amber-950/50 dark:text-amber-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                        Edit Surat
                    </Link>
                    <button type="button" @click="printLetter"
                        class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" /></svg>
                        Cetak / PDF
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6 print:p-0">
            <div class="mx-auto max-w-[980px] px-4 sm:px-6 lg:px-8 print:max-w-none print:px-0 space-y-4">

                <FlashAlert :message="$page.props.flash?.success" type="success" />

                <!-- Print Controls -->
                <div class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm print:hidden dark:border-slate-800 dark:bg-slate-900 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2">
                            <label class="text-[11px] font-semibold text-slate-600 dark:text-slate-300">Kertas:</label>
                            <select v-model="paper"
                                class="rounded-md border-slate-200 py-1 text-[11px] font-semibold text-slate-800 focus:border-primary focus:ring-primary dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                <option value="f4">F4 / Folio (210×330mm)</option>
                                <option value="a4">A4 (210×297mm)</option>
                            </select>
                        </div>
                        <button type="button" @click="isCompact = !isCompact"
                            :class="['inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-[11px] font-semibold border transition', isCompact ? 'bg-primary/10 border-primary/30 text-primary' : 'bg-white border-slate-200 text-slate-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400']">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25" /></svg>
                            Mode Ringkas{{ isCompact ? ' (Aktif)' : '' }}
                        </button>
                        <div class="hidden text-[11px] text-slate-500 dark:text-slate-400 sm:block">
                            Dibuat: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ formatTanggal(letter.tanggal_surat) }}</span>
                        </div>
                    </div>
                    <button type="button" @click="printLetter"
                        class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" /></svg>
                        Cetak / Ekspor PDF
                    </button>
                </div>

                <!-- Letter Preview -->
                <div class="rounded-xl bg-slate-100 p-4 sm:p-6 print:p-0 print:bg-transparent dark:bg-slate-800/50 shadow-inner">
                    <LossLetterPreview :letter="letter" :paper="paper" :compact="isCompact" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
