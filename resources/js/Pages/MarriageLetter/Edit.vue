<script setup>
import { computed, reactive, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import MarriageLetterPreview from '@/Components/MarriageLetterPreview.vue';

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
    if (!file) { form[field] = null; uploads[field] = null; return; }
    if (!file.type.startsWith('image/')) { alert('File harus berupa gambar.'); return; }
    form[field] = file;
    uploads[field] = await readFile(file);
}

const paper = ref('f4');
const isCompact = ref(false);

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
    <Head title="Edit Surat Keterangan Menikah" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 mb-1">
                        <Link :href="route('surat-keterangan-menikah.index')" class="hover:text-amber-600 transition">Ket. Menikah</Link>
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

                        <!-- 1. Nomor & Tanggal Surat -->
                        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <h3 class="text-xs font-bold uppercase tracking-wide text-slate-700 dark:text-slate-200 mb-4">Nomor & Tanggal Surat</h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <InputLabel value="Nomor Surat" />
                                    <input v-model="form.nomor_surat" type="text" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.nomor_surat" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Tanggal Surat" />
                                    <input v-model="form.tanggal_surat" type="date" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.tanggal_surat" />
                                </div>
                            </div>
                        </div>

                        <!-- 2. Data Pemohon -->
                        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <h3 class="text-xs font-bold uppercase tracking-wide text-slate-700 dark:text-slate-200 mb-4">Data Pemohon (Warga Desa)</h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <InputLabel value="Nama Lengkap Pemohon" />
                                    <input v-model="form.nama_pemohon" type="text" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.nama_pemohon" />
                                </div>
                                <div>
                                    <InputLabel value="Tempat Lahir" />
                                    <input v-model="form.tempat_lahir" type="text" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.tempat_lahir" />
                                </div>
                                <div>
                                    <InputLabel value="Tanggal Lahir" />
                                    <input v-model="form.tanggal_lahir" type="date" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.tanggal_lahir" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Pekerjaan" />
                                    <input v-model="form.pekerjaan" type="text" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.pekerjaan" />
                                </div>
                                <div>
                                    <InputLabel value="Dusun / Kp" />
                                    <input v-model="form.dusun" type="text" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="RT / RW" />
                                    <div class="flex gap-2">
                                        <input v-model="form.rt" type="text" placeholder="RT" :class="INPUT_CLASS" />
                                        <input v-model="form.rw" type="text" placeholder="RW" :class="INPUT_CLASS" />
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Alamat Pemohon (Opsional)" />
                                    <input v-model="form.alamat_pemohon" type="text" :class="INPUT_CLASS" />
                                </div>
                            </div>
                        </div>

                        <!-- 3. Informasi Pernikahan -->
                        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <h3 class="text-xs font-bold uppercase tracking-wide text-slate-700 dark:text-slate-200 mb-4">Informasi Pernikahan</h3>
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
                                    <InputError class="mt-1" :message="form.errors.tanggal_nikah" />
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
                                    <InputError class="mt-1" :message="form.errors.nama_pasangan" />
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
                                <div>
                                    <InputLabel value="Label Atasan (a.n / opsional)" />
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
                                <div>
                                    <InputLabel value="NIP / NIAPD" />
                                    <input v-model="form.nip_niapd" type="text" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Upload Tanda Tangan Baru" />
                                    <input type="file" accept="image/*" @change="onImageUpload('tanda_tangan', $event)" :class="FIELD_FILE_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Upload Stempel Baru" />
                                    <input type="file" accept="image/*" @change="onImageUpload('stempel', $event)" :class="FIELD_FILE_CLASS" />
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-3 pt-2 pb-6">
                            <button type="button" @click="submit" :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-amber-500 transition cursor-pointer disabled:opacity-50">
                                <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
                            </button>
                            <Link :href="route('surat-keterangan-menikah.show', props.letter.id)" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                                Batal
                            </Link>
                        </div>

                    </div>

                    <!-- LIVE PREVIEW -->
                    <div class="sticky top-20">
                        <MarriageLetterPreview :letter="previewLetter" :paper="paper" :is-compact="isCompact" />
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
