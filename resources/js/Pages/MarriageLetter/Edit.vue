<script setup>
import { computed, reactive, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SignaturePad from '@/Components/SignaturePad.vue';
import MarriageLetterPreview from '@/Components/MarriageLetterPreview.vue';
import { printSheet } from '@/lib/print';

const props = defineProps({
    letter: { type: Object, required: true }
});

const INPUT_CLASS = 'mt-1.5 block w-full rounded-xl border-slate-300/90 text-xs font-medium shadow-sm transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 dark:border-slate-700/80 dark:bg-slate-900 dark:text-slate-200';
const FIELD_FILE_CLASS = 'mt-1 block w-full cursor-pointer rounded-lg border border-dashed border-slate-300 bg-slate-50 p-2 text-xs text-slate-500 file:mr-3 file:rounded-md file:border-0 file:bg-amber-600 file:px-3 file:py-1.5 file:text-[11px] file:font-semibold file:text-white hover:file:bg-amber-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400';

const form = useForm({
    kabupaten: props.letter.kabupaten || 'Tasikmalaya',
    kecamatan: props.letter.kecamatan || 'Cigalontang',
    nama_desa: props.letter.nama_desa || 'Sirnaputra',
    alamat: props.letter.alamat || '',
    kode_pos: props.letter.kode_pos || '',
    kontak: props.letter.kontak || '',
    email_desa: props.letter.email_desa || '',
    logo_kiri: null,
    logo_kanan: null,

    nomor_surat: props.letter.nomor_surat || '',
    tanggal_surat: props.letter.tanggal_surat || new Date().toISOString().slice(0, 10),

    // Pemohon
    nama_pemohon: props.letter.nama_pemohon || '',
    tempat_lahir: props.letter.tempat_lahir || '',
    tanggal_lahir: props.letter.tanggal_lahir || '',
    pekerjaan: props.letter.pekerjaan || '',
    alamat_pemohon: props.letter.alamat_pemohon || '',
    rt: props.letter.rt || '',
    rw: props.letter.rw || '',
    dusun: props.letter.dusun || '',

    // Nikah
    jenis_nikah: props.letter.jenis_nikah || 'siri',
    hari_nikah: props.letter.hari_nikah || '',
    tanggal_nikah: props.letter.tanggal_nikah || '',
    keterangan_nikah: props.letter.keterangan_nikah || '',

    // Pasangan
    nama_pasangan: props.letter.nama_pasangan || '',
    tempat_lahir_pasangan: props.letter.tempat_lahir_pasangan || '',
    tanggal_lahir_pasangan: props.letter.tanggal_lahir_pasangan || '',
    alamat_pasangan: props.letter.alamat_pasangan || '',

    // Penutup & TTD
    kalimat_penutup: props.letter.kalimat_penutup || '',
    label_atasan: props.letter.label_atasan || '',
    jabatan_penandatangan: props.letter.jabatan_penandatangan || '',
    nama_penandatangan: props.letter.nama_penandatangan || '',
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

function readFile(file) {
    return new Promise(resolve => {
        const r = new FileReader();
        r.onload  = () => resolve(r.result);
        r.onerror = () => resolve(null);
        r.readAsDataURL(file);
    });
}

async function onImageUpload(field, event) {
    const file = event.target.files[0];
    if (!file) { form[field] = null; uploads[field] = props.letter[field] || null; return; }
    if (!file.type.startsWith('image/')) { alert('File harus berupa gambar.'); return; }
    form[field] = file;
    uploads[field] = await readFile(file);
}

const paper = ref('f4');
const compact = ref(false);

const previewLetter = computed(() => ({
    kabupaten: form.kabupaten, kecamatan: form.kecamatan, nama_desa: form.nama_desa,
    alamat: form.alamat, kode_pos: form.kode_pos, kontak: form.kontak, email_desa: form.email_desa,
    logo_kiri: uploads.logo_kiri, logo_kanan: uploads.logo_kanan,
    nomor_surat: form.nomor_surat, tanggal_surat: form.tanggal_surat,
    nama_pemohon: form.nama_pemohon, tempat_lahir: form.tempat_lahir,
    tanggal_lahir: form.tanggal_lahir, pekerjaan: form.pekerjaan,
    alamat_pemohon: form.alamat_pemohon, rt: form.rt, rw: form.rw, dusun: form.dusun,
    jenis_nikah: form.jenis_nikah, hari_nikah: form.hari_nikah,
    tanggal_nikah: form.tanggal_nikah, keterangan_nikah: form.keterangan_nikah,
    nama_pasangan: form.nama_pasangan, tempat_lahir_pasangan: form.tempat_lahir_pasangan,
    tanggal_lahir_pasangan: form.tanggal_lahir_pasangan, alamat_pasangan: form.alamat_pasangan,
    kalimat_penutup: form.kalimat_penutup, label_atasan: form.label_atasan,
    jabatan_penandatangan: form.jabatan_penandatangan, nama_penandatangan: form.nama_penandatangan,
    nip_niapd: form.nip_niapd, tanda_tangan: uploads.tanda_tangan, stempel: uploads.stempel,
}));

function submit() {
    form.post(route('surat-keterangan-menikah.update', props.letter.id), {
        preserveScroll: true,
        forceFormData: true,
    });
}
</script>

<template>
    <Head title="Edit Surat Keterangan Nikah" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 mb-1">
                        <Link :href="route('surat-keterangan-menikah.index')" class="hover:text-primary transition">Ket. Menikah</Link>
                        <span class="text-slate-300 dark:text-slate-600">/</span>
                        <span class="text-slate-700 dark:text-slate-300 font-medium">Edit Surat</span>
                    </div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Edit Surat Keterangan Menikah</h1>
                </div>
            </div>
        </template>

        <div class="py-8 print:p-0">
            <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8 print:max-w-none print:px-0">
                <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-2 print:grid-cols-1">

                    <div class="space-y-4 print:hidden">

                        <!-- 1. Data Pemohon -->
                        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <h3 class="text-xs font-bold uppercase tracking-wide text-slate-700 dark:text-slate-200 mb-4">Data Pemohon (Suami / Istri)</h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <InputLabel value="Nama Lengkap Pemohon" />
                                    <input v-model="form.nama_pemohon" type="text" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.nama_pemohon" />
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
                                    <InputLabel value="Dusun / RT / RW" />
                                    <input v-model="form.dusun" type="text" placeholder="Dusun" :class="INPUT_CLASS" />
                                </div>
                            </div>
                        </div>

                        <!-- 2. Nomor & Tanggal Surat -->
                        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <h3 class="text-xs font-bold uppercase tracking-wide text-slate-700 dark:text-slate-200 mb-4">Nomor & Tanggal Surat</h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
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

                        <!-- 3. Rincian Pernikahan -->
                        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <h3 class="text-xs font-bold uppercase tracking-wide text-slate-700 dark:text-slate-200 mb-4">Rincian Pernikahan</h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <InputLabel value="Jenis Pernikahan" />
                                    <input v-model="form.jenis_nikah" type="text" placeholder="cth. siri" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.jenis_nikah" />
                                </div>
                                <div>
                                    <InputLabel value="Hari Nikah" />
                                    <input v-model="form.hari_nikah" type="text" placeholder="cth. Senin" :class="INPUT_CLASS" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Tanggal Nikah" />
                                    <input v-model="form.tanggal_nikah" type="date" :class="INPUT_CLASS" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Keterangan Pernikahan (Opsional)" />
                                    <textarea v-model="form.keterangan_nikah" rows="2" :class="INPUT_CLASS" />
                                </div>
                            </div>
                        </div>

                        <!-- 4. Data Pasangan -->
                        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <h3 class="text-xs font-bold uppercase tracking-wide text-slate-700 dark:text-slate-200 mb-4">Data Pasangan (Suami / Istri)</h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <InputLabel value="Nama Lengkap Pasangan" />
                                    <input v-model="form.nama_pasangan" type="text" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Tempat Lahir Pasangan" />
                                    <input v-model="form.tempat_lahir_pasangan" type="text" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Tanggal Lahir Pasangan" />
                                    <input v-model="form.tanggal_lahir_pasangan" type="date" :class="INPUT_CLASS" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Alamat Pasangan" />
                                    <input v-model="form.alamat_pasangan" type="text" :class="INPUT_CLASS" />
                                </div>
                            </div>
                        </div>

                        <!-- 5. Tanda Tangan -->
                        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <h3 class="text-xs font-bold uppercase tracking-wide text-slate-700 dark:text-slate-200 mb-4">Penandatangan Surat</h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <InputLabel value="Label Atasan (cth: a.n Kepala Desa ...)" :optional="true" />
                                    <input v-model="form.label_atasan" type="text" placeholder="cth. a.n Kepala Desa" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Jabatan Penandatangan" />
                                    <input v-model="form.jabatan_penandatangan" type="text" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Nama Penandatangan" />
                                    <input v-model="form.nama_penandatangan" type="text" :class="INPUT_CLASS" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="NIP / NIAPD" :optional="true" />
                                    <input v-model="form.nip_niapd" type="text" :class="INPUT_CLASS" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Tanda Tangan Digital (Gambar Langsung / Unggah Foto)" :optional="true" />
                                    <div class="mt-1">
                                        <SignaturePad
                                            :height="140"
                                            @change="(file, dataUrl) => { form.tanda_tangan = file; uploads.tanda_tangan = dataUrl; }"
                                        />
                                    </div>
                                    <div v-if="uploads.tanda_tangan" class="mt-2 flex items-center gap-3 bg-white p-2 rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-700">
                                        <span class="text-[11px] font-bold text-slate-500">TTD Saat Ini:</span>
                                        <img :src="uploads.tanda_tangan" class="h-10 object-contain" alt="Preview TTD" />
                                    </div>
                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                        <span>Atau unggah berkas gambar TTD:</span>
                                        <input type="file" accept="image/*" @change="onImageUpload('tanda_tangan', $event)"
                                            class="text-xs text-gray-500 file:rounded file:border-0 file:bg-slate-200 file:px-2 file:py-1 file:text-xs file:font-semibold" />
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Upload Stempel PNG Transparan" :optional="true" />
                                    <input type="file" accept="image/*" @change="onImageUpload('stempel', $event)" :class="FIELD_FILE_CLASS" />
                                    <img v-if="uploads.stempel" :src="uploads.stempel" class="mt-2 h-12 object-contain opacity-60" alt="Stempel" />
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-3 pt-2 pb-6">
                            <button type="button" @click="submit" :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-amber-500 transition cursor-pointer disabled:opacity-50">
                                <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
                            </button>
                            <Link :href="route('surat-keterangan-menikah.index')" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                                Batal
                            </Link>
                        </div>
                    </div>

                    <!-- LIVE PREVIEW -->
                    <div class="sticky top-20">
                        <div class="mb-3 flex items-center justify-between rounded-xl border border-slate-200 bg-white p-2.5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Kertas:</span>
                                <select v-model="paper" class="rounded-lg border-slate-300 py-1 text-xs font-bold dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                                    <option value="f4">F4 / Folio</option>
                                    <option value="a4">A4</option>
                                </select>
                            </div>
                            <button type="button" @click="printSheet()"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 px-3 py-1.5 text-xs font-bold text-white shadow-md transition hover:from-amber-500 active:scale-95">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" />
                                </svg>
                                Cetak Preview
                            </button>
                        </div>
                        <MarriageLetterPreview :letter="previewLetter" :paper="paper" :compact="compact" :scaled="true" />
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
