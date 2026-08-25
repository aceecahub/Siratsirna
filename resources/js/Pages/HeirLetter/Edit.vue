<script setup>
import { computed, reactive, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import HeirLetterPreview from '@/Components/HeirLetterPreview.vue';

const props = defineProps({
    letter: { type: Object, required: true }
});

const INPUT_CLASS = 'mt-1 block w-full rounded-lg border-slate-200 bg-white text-xs font-medium shadow-sm transition focus:border-primary focus:ring-2 focus:ring-primary/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200';

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

    nama_almarhum: props.letter.nama_almarhum || '',
    nik_almarhum: props.letter.nik_almarhum || '',
    tanggal_meninggal: props.letter.tanggal_meninggal || '',
    lokasi_meninggal: props.letter.lokasi_meninggal || '',

    ahli_waris_list: props.letter.ahli_waris_list || [],
    saksi_list: props.letter.saksi_list || [],

    jabatan_penandatangan: props.letter.jabatan_penandatangan || 'Kepala Desa',
    nama_penandatangan: props.letter.nama_penandatangan || 'H. AHMAD SUPRIATNA',
    nip_niapd: props.letter.nip_niapd || '',
    tanda_tangan: null,
    stempel: null,
});

const uploads = reactive({
    logo_kiri: props.letter.logo_kiri || '/images/logo-tasikmalaya.png',
    logo_kanan: props.letter.logo_kanan || null,
    tanda_tangan: props.letter.tanda_tangan || null,
    stempel: props.letter.stempel || null,
});

const paper = ref('f4');
const isCompact = ref(false);

const previewLetter = computed(() => ({
    kabupaten: form.kabupaten, kecamatan: form.kecamatan, nama_desa: form.nama_desa,
    alamat: form.alamat, kode_pos: form.kode_pos, kontak: form.kontak, email_desa: form.email_desa,
    logo_kiri: uploads.logo_kiri, logo_kanan: uploads.logo_kanan,
    nomor_surat: form.nomor_surat, tanggal_surat: form.tanggal_surat,
    nama_almarhum: form.nama_almarhum, nik_almarhum: form.nik_almarhum,
    tanggal_meninggal: form.tanggal_meninggal, lokasi_meninggal: form.lokasi_meninggal,
    ahli_waris_list: form.ahli_waris_list, saksi_list: form.saksi_list,
    jabatan_penandatangan: form.jabatan_penandatangan, nama_penandatangan: form.nama_penandatangan,
    nip_niapd: form.nip_niapd, tanda_tangan: uploads.tanda_tangan, stempel: uploads.stempel,
}));

function submit() {
    form.post(route('surat-ahli-waris.update', props.letter.id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Edit Surat Keterangan Ahli Waris" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 mb-1">
                        <Link :href="route('surat-ahli-waris.index')" class="hover:text-primary transition">Ahli Waris</Link>
                        <span class="text-slate-300 dark:text-slate-600">/</span>
                        <span class="text-slate-700 dark:text-slate-300 font-medium">Edit Surat</span>
                    </div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Edit Surat Keterangan Ahli Waris</h1>
                </div>
            </div>
        </template>

        <div class="py-8 print:p-0">
            <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8 print:max-w-none print:px-0">
                <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-2 print:grid-cols-1">

                    <div class="space-y-4 print:hidden">

                        <!-- Data Almarhum -->
                        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
                            <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 px-5 py-3.5 bg-slate-50/50 dark:bg-slate-800/50">
                                <div class="flex h-6 w-6 items-center justify-center rounded-md bg-emerald-600 text-white text-[10px] font-bold">1</div>
                                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wide">Data Almarhum / Pewaris</h3>
                            </div>
                            <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <InputLabel value="Nama Almarhum / Almarhumah" />
                                    <input v-model="form.nama_almarhum" type="text" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Tanggal Meninggal" />
                                    <input v-model="form.tanggal_meninggal" type="date" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Tempat / Lokasi Meninggal" />
                                    <input v-model="form.lokasi_meninggal" type="text" :class="INPUT_CLASS" />
                                </div>
                            </div>
                        </div>

                        <!-- Nomor & Tanggal Surat -->
                        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
                            <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 px-5 py-3.5">
                                <div class="flex h-6 w-6 items-center justify-center rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-bold">2</div>
                                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wide">Nomor & Tanggal Surat</h3>
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
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-3 pt-2">
                            <button type="button" @click="submit" :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-emerald-500 transition cursor-pointer">
                                <span>Simpan Perubahan</span>
                            </button>
                            <Link :href="route('surat-ahli-waris.show', props.letter.id)" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                                Batal
                            </Link>
                        </div>

                    </div>

                    <!-- LIVE PREVIEW -->
                    <div class="sticky top-20">
                        <HeirLetterPreview :letter="previewLetter" :paper="paper" :is-compact="isCompact" />
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
