<script setup>
import { computed, reactive, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SignaturePad from '@/Components/SignaturePad.vue';
import PovertyLetterPreview from '@/Components/PovertyLetterPreview.vue';
import { printSheet } from '@/lib/print';

const props = defineProps({
    letter: { type: Object, required: true }
});

const INPUT_CLASS = 'mt-1.5 block w-full rounded-xl border-slate-300/90 text-xs font-medium shadow-sm transition focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 dark:border-slate-700/80 dark:bg-slate-900 dark:text-slate-200';

const form = useForm({
    kabupaten: props.letter.kabupaten || 'Tasikmalaya',
    kecamatan: props.letter.kecamatan || 'Cigalontang',
    nama_desa: props.letter.nama_desa || 'Sirnaputra',
    alamat: props.letter.alamat || 'Jl. Raya Sirnaputra No. 01',
    kode_pos: props.letter.kode_pos || '46463',
    kontak: props.letter.kontak || '',
    email_desa: props.letter.email_desa || '',
    logo_kiri: null,
    logo_kanan: null,
    nomor_surat: props.letter.nomor_surat || '',
    tanggal_surat: props.letter.tanggal_surat || new Date().toISOString().slice(0, 10),
    nama_warga: props.letter.nama_warga || '',
    tempat_lahir: props.letter.tempat_lahir || '',
    tanggal_lahir: props.letter.tanggal_lahir || '',
    pekerjaan: props.letter.pekerjaan || '',
    alamat_warga: props.letter.alamat_warga || '',
    rt: props.letter.rt || '',
    rw: props.letter.rw || '',
    dusun: props.letter.dusun || '',
    keterangan: props.letter.keterangan || '',
    stempel: null,
    penandatangan_list: props.letter.penandatangan_list || [],
});

const uploads = reactive({
    logo_kiri: props.letter.logo_kiri || '/images/logo-tasikmalaya.png',
    logo_kanan: props.letter.logo_kanan || null,
    stempel: props.letter.stempel || null,
});

const paper = ref('f4');
const isCompact = ref(false);

const previewLetter = computed(() => ({
    kabupaten: form.kabupaten, kecamatan: form.kecamatan, nama_desa: form.nama_desa,
    alamat: form.alamat, kode_pos: form.kode_pos, kontak: form.kontak, email_desa: form.email_desa,
    logo_kiri: uploads.logo_kiri, logo_kanan: uploads.logo_kanan,
    nomor_surat: form.nomor_surat, tanggal_surat: form.tanggal_surat,
    nama_warga: form.nama_warga, tempat_lahir: form.tempat_lahir,
    tanggal_lahir: form.tanggal_lahir, pekerjaan: form.pekerjaan,
    alamat_warga: form.alamat_warga, rt: form.rt, rw: form.rw, dusun: form.dusun,
    keterangan: form.keterangan, stempel: uploads.stempel,
    penandatangan_list: form.penandatangan_list,
}));

function submit() {
    form.post(route('surat-tidak-mampu.update', props.letter.id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Edit Surat Keterangan Tidak Mampu" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 mb-1">
                        <Link :href="route('surat-tidak-mampu.index')" class="hover:text-primary transition">SKTM</Link>
                        <span class="text-slate-300 dark:text-slate-600">/</span>
                        <span class="text-slate-700 dark:text-slate-300 font-medium">Edit Surat</span>
                    </div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Edit Surat Ket. Tidak Mampu (SKTM)</h1>
                </div>
            </div>
        </template>

        <div class="py-8 print:p-0">
            <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8 print:max-w-none print:px-0">
                <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-2 print:grid-cols-1">

                    <div class="space-y-4 print:hidden">

                        <!-- Data Warga -->
                        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
                            <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 px-5 py-3.5 bg-slate-50/50 dark:bg-slate-800/50">
                                <div class="flex h-6 w-6 items-center justify-center rounded-md bg-rose-600 text-white text-[10px] font-bold">1</div>
                                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wide">Data Pemohon / Warga</h3>
                            </div>
                            <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <InputLabel value="Nama Lengkap Warga" />
                                    <input v-model="form.nama_warga" type="text" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.nama_warga" />
                                </div>
                                <div>
                                    <InputLabel value="Tempat Lahir" />
                                    <input v-model="form.tempat_lahir" type="text" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Tanggal Lahir" />
                                    <input v-model="form.tanggal_lahir" type="date" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Pekerjaan" />
                                    <input v-model="form.pekerjaan" type="text" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Dusun / Kampung" />
                                    <input v-model="form.dusun" type="text" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="RT" />
                                    <input v-model="form.rt" type="text" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="RW" />
                                    <input v-model="form.rw" type="text" :class="INPUT_CLASS" />
                                </div>
                            </div>
                        </div>

                        <!-- Nomor & Tanggal Surat -->
                        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
                            <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 px-5 py-3.5">
                                <div class="flex h-6 w-6 items-center justify-center rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-bold">2</div>
                                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wide">Nomor, Tanggal & Keterangan</h3>
                            </div>
                            <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <InputLabel value="Nomor Surat" />
                                    <input v-model="form.nomor_surat" type="text" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Tanggal Surat" />
                                    <input v-model="form.tanggal_surat" type="date" :class="INPUT_CLASS" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Tujuan / Keterangan Keperluan SKTM" />
                                    <textarea v-model="form.keterangan" rows="3" :class="INPUT_CLASS" placeholder="cth. Persyaratan Pengajuan KIP / Beasiswa / Bantuan Medis" />
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-3 pt-2">
                            <button type="button" @click="submit" :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-xl bg-rose-600 px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-rose-500 transition cursor-pointer">
                                <span>Simpan Perubahan</span>
                            </button>
                            <Link :href="route('surat-tidak-mampu.show', props.letter.id)" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                                Batal
                            </Link>
                        </div>

                    </div>

                    <!-- LIVE PREVIEW -->
                    <div class="sticky top-20">
                        <PovertyLetterPreview :letter="previewLetter" :paper="paper" :is-compact="isCompact" />
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
