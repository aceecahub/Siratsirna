<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FlashAlert from '@/Components/FlashAlert.vue';
import LetterPreview from '@/Components/LetterPreview.vue';
import { printSheet } from '@/lib/print';

const props = defineProps({ mail: { type: Object, required: true } });

const paper = ref('f4');
const isCompact = ref(false);

async function printLetter() { await printSheet(); }

const sifatBadge = (sifat) => {
    if (sifat === 'Rahasia') return 'bg-red-50 text-red-700 ring-red-200 dark:bg-red-950/40 dark:text-red-300 dark:ring-red-800/40';
    if (sifat === 'Penting') return 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:ring-amber-800/40';
    return 'bg-slate-100 text-slate-600 ring-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:ring-slate-700';
};
</script>

<template>
    <Head :title="`Surat Keluar – ${mail.perihal}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 mb-1">
                        <Link :href="route('surat-keluar.index')" class="hover:text-primary transition">Surat Keluar</Link>
                        <span class="text-slate-300 dark:text-slate-600">/</span>
                        <span class="font-mono text-slate-700 dark:text-slate-300">{{ mail.nomor_surat }}</span>
                        <span v-if="mail.sifat" :class="['inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-semibold ring-1 ring-inset', sifatBadge(mail.sifat)]">{{ mail.sifat }}</span>
                    </div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">{{ mail.perihal }}</h1>
                </div>
                <div class="flex items-center gap-2 print:hidden">
                    <Link :href="route('surat-keluar.index')"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 shadow-sm hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 transition">
                        ← Kembali
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
                                <option value="a4">A4 (210×297mm)</option>
                                <option value="f4">F4 / Folio (210×330mm)</option>
                            </select>
                        </div>
                        <button type="button" @click="isCompact = !isCompact"
                            :class="['inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-[11px] font-semibold border transition', isCompact ? 'bg-primary/10 border-primary/30 text-primary' : 'bg-white border-slate-200 text-slate-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400']">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25" /></svg>
                            Mode Ringkas{{ isCompact ? ' (Aktif)' : '' }}
                        </button>
                    </div>
                    <button type="button" @click="printLetter"
                        class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" /></svg>
                        Cetak / Ekspor PDF
                    </button>
                </div>

                <!-- Letter Preview -->
                <div class="rounded-xl bg-slate-100 p-4 sm:p-6 print:p-0 print:bg-transparent dark:bg-slate-800/50 shadow-inner">
                    <LetterPreview :letter="mail" :paper="paper" :compact="isCompact" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
