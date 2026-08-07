<script setup>
import { computed, reactive, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import AssistanceLetterPreview from '@/Components/AssistanceLetterPreview.vue';
import { printSheet } from '@/lib/print';

const INPUT_CLASS = 'mt-1.5 block w-full rounded-xl border-slate-300/90 text-xs font-medium shadow-sm transition focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 dark:border-slate-700/80 dark:bg-slate-900 dark:text-slate-200';
const SECTION_CLASS = 'rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/90';
const SECTION_TITLE = 'mb-5 flex items-center gap-2.5 border-b border-slate-100 pb-3 text-xs font-black uppercase tracking-wider text-slate-800 dark:border-slate-800 dark:text-slate-200';

const form = useForm({
    kabupaten:  '',
    kecamatan:  '',
    nama_desa:  '',
    alamat:     '',
    kode_pos:   '',
    kontak:     '',
    email_desa: '',
    logo_kiri:  null,
    logo_kanan: null,

    nomor_surat:    '',
    tanggal_surat:  new Date().toISOString().slice(0, 10),
    sifat_surat:    'Penting',
    lampiran_surat: '1 Set',
    perihal_surat:  '',

    tujuan_nama: 'Bupati tasikmalaya',
    tujuan_cq:   'Cq. Dinas Sosial PPKB,P3A',
    tujuan_di:   'Singaparna, Tasikmalaya',

    no_kk:         '',
    nik:           '',
    nama_pasien:   '',
    jenis_kelamin: 'Perempuan',
    tempat_lahir:  '',
    tanggal_lahir: '',
    pekerjaan:     'Belum/Tidak Bekerja',
    alamat_pasien: '',
    rt:            '',
    rw:            '',
    dusun:         '',
    id_dtks:       '-',

    teks_permohonan: '',
    kalimat_penutup: '',

    jabatan_ttd_kiri: 'Pendamping PKH /TKSK Kec.Cigalontang',
    nama_ttd_kiri:    '',

    label_ttd_tengah:   'Mengetahui,',
    jabatan_ttd_tengah: 'Camat Cigalontang',
    nama_ttd_tengah:    '',

    label_ttd_kanan:   '',
    jabatan_ttd_kanan: 'Kepala Desa Sirnaputra',
    nama_ttd_kanan:    'Suhendar',

    tanda_tangan: null,
    stempel:      null,
});

const lampiranItems = reactive([
    { text: 'Surat keterangan tidak mampu ( diketahui oleh Camat )' },
    { text: 'Fotocopy KTP' },
    { text: 'Fotocopy Kartu Keluarga' },
    { text: 'Diagnosa/Surat Rujukan' },
]);

function addLampiran() {
    lampiranItems.push({ text: '' });
}
function removeLampiran(idx) {
    if (lampiranItems.length > 1) lampiranItems.splice(idx, 1);
}

const uploads = reactive({ logo_kiri: null, logo_kanan: null, tanda_tangan: null, stempel: null });

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

const paper   = ref('f4');
const compact = ref(false);

const previewLetter = computed(() => ({
    kabupaten:  form.kabupaten,  kecamatan:  form.kecamatan,  nama_desa:  form.nama_desa,
    alamat:     form.alamat,     kode_pos:   form.kode_pos,   kontak:     form.kontak,
    email_desa: form.email_desa,
    logo_kiri:  uploads.logo_kiri,
    logo_kanan: uploads.logo_kanan,

    nomor_surat:    form.nomor_surat,
    tanggal_surat:  form.tanggal_surat,
    sifat_surat:    form.sifat_surat,
    lampiran_surat: form.lampiran_surat,
    perihal_surat:  form.perihal_surat,

    tujuan_nama: form.tujuan_nama,
    tujuan_cq:   form.tujuan_cq,
    tujuan_di:   form.tujuan_di,

    no_kk:         form.no_kk,
    nik:           form.nik,
    nama_pasien:   form.nama_pasien,
    jenis_kelamin: form.jenis_kelamin,
    tempat_lahir:  form.tempat_lahir,
    tanggal_lahir: form.tanggal_lahir,
    pekerjaan:     form.pekerjaan,
    alamat_pasien: form.alamat_pasien,
    rt:            form.rt,
    rw:            form.rw,
    dusun:         form.dusun,
    id_dtks:       form.id_dtks,

    teks_permohonan: form.teks_permohonan,
    lampiran_list:   lampiranItems.map(i => i.text),
    kalimat_penutup: form.kalimat_penutup,

    jabatan_ttd_kiri: form.jabatan_ttd_kiri,
    nama_ttd_kiri:    form.nama_ttd_kiri,

    label_ttd_tengah:   form.label_ttd_tengah,
    jabatan_ttd_tengah: form.jabatan_ttd_tengah,
    nama_ttd_tengah:    form.nama_ttd_tengah,

    label_ttd_kanan:   form.label_ttd_kanan,
    jabatan_ttd_kanan: form.jabatan_ttd_kanan,
    nama_ttd_kanan:    form.nama_ttd_kanan,

    tanda_tangan: uploads.tanda_tangan,
    stempel:      uploads.stempel,
}));

function submit() {
    form.transform((data) => ({
        ...data,
        lampiran_list: JSON.stringify(lampiranItems.map(i => i.text)),
    })).post(route('surat-permohonan-bantuan.store'), {
        preserveScroll: true,
        forceFormData: true,
    });
}
</script>

<template>
    <Head title="Buat Surat Permohonan Bantuan" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-rose-600 to-pink-500 text-white shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    </svg>
                </div>
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-rose-600 dark:text-rose-400">Pembuatan Surat</div>
                    <h2 class="text-xl font-black text-slate-900 dark:text-white">Buat Surat Permohonan Bantuan</h2>
                </div>
            </div>
        </template>

        <div class="py-10 print:p-0">
            <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8 print:max-w-none print:px-0">
                <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-2 print:grid-cols-1">

                    <!-- FORM -->
                    <div class="space-y-6 print:hidden">

                        <!-- 1. Kop Surat -->
                        <section :class="SECTION_CLASS">
                            <h3 :class="SECTION_TITLE">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-rose-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                </svg>
                                Kop Surat
                            </h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <InputLabel value="Logo Kabupaten / Kiri" :optional="true" />
                                    <input type="file" accept="image/*" @change="onImageUpload('logo_kiri', $event)"
                                        class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-rose-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white" />
                                    <img v-if="uploads.logo_kiri" :src="uploads.logo_kiri" class="mt-2 h-12 object-contain" alt="Logo Kiri" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Logo Desa / Kanan" :optional="true" />
                                    <input type="file" accept="image/*" @change="onImageUpload('logo_kanan', $event)"
                                        class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-rose-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white" />
                                    <img v-if="uploads.logo_kanan" :src="uploads.logo_kanan" class="mt-2 h-12 object-contain" alt="Logo Kanan" />
                                </div>
                                <div>
                                    <InputLabel value="Kabupaten" />
                                    <input v-model="form.kabupaten" type="text" placeholder="cth. Tasikmalaya" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.kabupaten" />
                                </div>
                                <div>
                                    <InputLabel value="Kecamatan" />
                                    <input v-model="form.kecamatan" type="text" placeholder="cth. Cigalontang" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.kecamatan" />
                                </div>
                                <div>
                                    <InputLabel value="Nama Desa" />
                                    <input v-model="form.nama_desa" type="text" placeholder="cth. Sirnaputra" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.nama_desa" />
                                </div>
                                <div>
                                    <InputLabel value="Kode Pos" :optional="true" />
                                    <input v-model="form.kode_pos" type="text" placeholder="cth. 46463" :class="INPUT_CLASS" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Alamat Kantor Desa" />
                                    <textarea v-model="form.alamat" rows="2" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.alamat" />
                                </div>
                                <div>
                                    <InputLabel value="Kontak / Telp" :optional="true" />
                                    <input v-model="form.kontak" type="text" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Email Desa" :optional="true" />
                                    <input v-model="form.email_desa" type="email" :class="INPUT_CLASS" />
                                </div>
                            </div>
                        </section>

                        <!-- 2. Header Grid (Nomor & Tujuan Surat) -->
                        <section :class="SECTION_CLASS">
                            <h3 :class="SECTION_TITLE">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-rose-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5" />
                                </svg>
                                Nomor &amp; Tujuan Surat
                            </h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <InputLabel value="Nomor Surat" />
                                    <input v-model="form.nomor_surat" type="text" placeholder="cth. 012 /475/Ds/VI/2025" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.nomor_surat" />
                                </div>
                                <div>
                                    <InputLabel value="Tanggal Surat" />
                                    <input v-model="form.tanggal_surat" type="date" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.tanggal_surat" />
                                </div>
                                <div>
                                    <InputLabel value="Sifat Surat" />
                                    <input v-model="form.sifat_surat" type="text" placeholder="Penting" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Lampiran" />
                                    <input v-model="form.lampiran_surat" type="text" placeholder="1 Set" :class="INPUT_CLASS" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Perihal (Override)" :optional="true" />
                                    <input v-model="form.perihal_surat" type="text" placeholder="Kosongkan untuk otomatis: Permohonan Bantuan Biaya Pengobatan a/n [Nama Pasien]" :class="INPUT_CLASS" />
                                </div>
                                <div class="sm:col-span-2 border-t border-slate-100 pt-3 dark:border-slate-800">
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Tujuan Kepada Yth:</span>
                                </div>
                                <div>
                                    <InputLabel value="Nama Penerima / Pejabat" />
                                    <input v-model="form.tujuan_nama" type="text" placeholder="cth. Bupati tasikmalaya" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Cq. Dinas / Instansi" :optional="true" />
                                    <input v-model="form.tujuan_cq" type="text" placeholder="cth. Cq. Dinas Sosial PPKB,P3A" :class="INPUT_CLASS" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Lokasi (Di)" :optional="true" />
                                    <input v-model="form.tujuan_di" type="text" placeholder="cth. Singaparna, Tasikmalaya" :class="INPUT_CLASS" />
                                </div>
                            </div>
                        </section>

                        <!-- 3. Identitas Pasien / Pemohon -->
                        <section :class="SECTION_CLASS">
                            <h3 :class="SECTION_TITLE">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-rose-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                Identitas Pasien / Pemohon
                            </h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <InputLabel value="Nama Lengkap Pasien" />
                                    <input v-model="form.nama_pasien" type="text" placeholder="cth. Haura Rahma Saqi Rizky Rohendi" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.nama_pasien" />
                                </div>
                                <div>
                                    <InputLabel value="NO KK (Opsional)" :optional="true" />
                                    <input v-model="form.no_kk" type="text" placeholder="cth. 3206271907240003" :class="INPUT_CLASS" maxlength="30" />
                                </div>
                                <div>
                                    <InputLabel value="NIK Pasien (Opsional)" :optional="true" />
                                    <input v-model="form.nik" type="text" placeholder="cth. 3206276201220004" :class="INPUT_CLASS" maxlength="30" />
                                </div>
                                <div>
                                    <InputLabel value="Jenis Kelamin" />
                                    <select v-model="form.jenis_kelamin" :class="INPUT_CLASS">
                                        <option value="Perempuan">Perempuan</option>
                                        <option value="Laki-Laki">Laki-Laki</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel value="Pekerjaan" />
                                    <input v-model="form.pekerjaan" type="text" placeholder="cth. Belum/Tidak Bekerja" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Tempat Lahir" />
                                    <input v-model="form.tempat_lahir" type="text" placeholder="cth. Tasikmalaya" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Tanggal Lahir" />
                                    <input v-model="form.tanggal_lahir" type="date" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="ID DTKS" :optional="true" />
                                    <input v-model="form.id_dtks" type="text" placeholder="-" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Dusun / Kampung" :optional="true" />
                                    <input v-model="form.dusun" type="text" placeholder="cth. Pacihayan" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="RT" :optional="true" />
                                    <input v-model="form.rt" type="text" placeholder="cth. 001" :class="INPUT_CLASS" maxlength="5" />
                                </div>
                                <div>
                                    <InputLabel value="RW" :optional="true" />
                                    <input v-model="form.rw" type="text" placeholder="cth. 001" :class="INPUT_CLASS" maxlength="5" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Alamat Lengkap (Override)" :optional="true" />
                                    <textarea v-model="form.alamat_pasien" rows="2" :class="INPUT_CLASS" placeholder="Kosongkan untuk diisi otomatis dari Dusun/RT/RW/Desa" />
                                </div>
                            </div>
                        </section>

                        <!-- 4. Lampiran Pertimbangan (Dinamis) -->
                        <section :class="SECTION_CLASS">
                            <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                                <h3 :class="SECTION_TITLE" class="!mb-0 !border-0 !pb-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-rose-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                                    </svg>
                                    Lampiran Pertimbangan (Dinamis)
                                </h3>
                                <button type="button" @click="addLampiran"
                                    class="inline-flex items-center gap-1 rounded-lg bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-700 hover:bg-rose-100 dark:bg-rose-950 dark:text-rose-300">
                                    + Lampiran
                                </button>
                            </div>
                            <div class="space-y-3">
                                <div v-for="(item, idx) in lampiranItems" :key="idx" class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-500 w-6">{{ idx + 1 }}.</span>
                                    <input v-model="item.text" type="text" placeholder="Detail Lampiran" :class="INPUT_CLASS" class="flex-1 !mt-0" />
                                    <button v-if="lampiranItems.length > 1" type="button" @click="removeLampiran(idx)" class="text-xs text-red-600 hover:underline px-1">Hapus</button>
                                </div>
                            </div>
                        </section>

                        <!-- 5. TTD 3 Kolom -->
                        <section :class="SECTION_CLASS">
                            <h3 :class="SECTION_TITLE">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-rose-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                </svg>
                                Penandatangan (3 Kolom TTD)
                            </h3>
                            <div class="space-y-4">
                                <!-- Kolom Kiri -->
                                <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-3 dark:border-slate-800 dark:bg-slate-900/40">
                                    <span class="text-xs font-bold text-rose-700 dark:text-rose-400">Kolom 1 (Kiri): PKH / TKSK</span>
                                    <div class="grid grid-cols-1 gap-3 mt-2 sm:grid-cols-2">
                                        <div>
                                            <InputLabel value="Jabatan Kolom Kiri" />
                                            <input v-model="form.jabatan_ttd_kiri" type="text" placeholder="Pendamping PKH /TKSK Kec.Cigalontang" :class="INPUT_CLASS" />
                                        </div>
                                        <div>
                                            <InputLabel value="Nama Kolom Kiri (Opsional)" :optional="true" />
                                            <input v-model="form.nama_ttd_kiri" type="text" placeholder="Kosongkan jika titik-titik" :class="INPUT_CLASS" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Kolom Tengah -->
                                <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-3 dark:border-slate-800 dark:bg-slate-900/40">
                                    <span class="text-xs font-bold text-rose-700 dark:text-rose-400">Kolom 2 (Tengah): Camat</span>
                                    <div class="grid grid-cols-1 gap-3 mt-2 sm:grid-cols-2">
                                        <div>
                                            <InputLabel value="Label Atas" :optional="true" />
                                            <input v-model="form.label_ttd_tengah" type="text" placeholder="Mengetahui," :class="INPUT_CLASS" />
                                        </div>
                                        <div>
                                            <InputLabel value="Jabatan Camat" />
                                            <input v-model="form.jabatan_ttd_tengah" type="text" placeholder="Camat Cigalontang" :class="INPUT_CLASS" />
                                        </div>
                                        <div class="sm:col-span-2">
                                            <InputLabel value="Nama Camat (Opsional)" :optional="true" />
                                            <input v-model="form.nama_ttd_tengah" type="text" placeholder="Kosongkan jika titik-titik" :class="INPUT_CLASS" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-3 dark:border-slate-800 dark:bg-slate-900/40">
                                    <span class="text-xs font-bold text-rose-700 dark:text-rose-400">Kolom 3 (Kanan): Kepala Desa</span>
                                    <div class="grid grid-cols-1 gap-3 mt-2 sm:grid-cols-2">
                                        <div>
                                            <InputLabel value="Jabatan Kades" />
                                            <input v-model="form.jabatan_ttd_kanan" type="text" placeholder="Kepala Desa Sirnaputra" :class="INPUT_CLASS" />
                                        </div>
                                        <div>
                                            <InputLabel value="Nama Kades" />
                                            <input v-model="form.nama_ttd_kanan" type="text" placeholder="Suhendar" :class="INPUT_CLASS" />
                                        </div>
                                        <div class="sm:col-span-2">
                                            <InputLabel value="Upload Tanda Tangan PNG Transparan" :optional="true" />
                                            <input type="file" accept="image/*" @change="onImageUpload('tanda_tangan', $event)"
                                                class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-rose-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white" />
                                            <img v-if="uploads.tanda_tangan" :src="uploads.tanda_tangan" class="mt-2 h-12 object-contain" alt="TTD" />
                                        </div>
                                        <div class="sm:col-span-2">
                                            <InputLabel value="Upload Stempel PNG Transparan" :optional="true" />
                                            <input type="file" accept="image/*" @change="onImageUpload('stempel', $event)"
                                                class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-slate-700 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white" />
                                            <img v-if="uploads.stempel" :src="uploads.stempel" class="mt-2 h-14 object-contain opacity-60" alt="Stempel" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <div class="flex flex-wrap items-center gap-3 pb-6">
                            <button type="button" @click="submit" :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-rose-600 to-pink-600 px-6 py-2.5 text-xs font-bold text-white shadow-md shadow-rose-600/20 transition hover:from-rose-500 active:scale-95 disabled:opacity-60">
                                <svg v-if="!form.processing" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                                <svg v-else class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                {{ form.processing ? 'Menyimpan...' : 'Simpan & Cetak Surat' }}
                            </button>
                        </div>

                    </div>

                    <!-- LIVE PREVIEW -->
                    <div class="sticky top-4 space-y-4" id="live-preview">
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-3 shadow-sm print:hidden dark:border-slate-800 dark:bg-slate-900 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Kertas:</span>
                                <select v-model="paper" class="rounded-lg border-slate-300 py-1 text-xs font-bold dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                                    <option value="f4">F4 / Folio</option>
                                    <option value="a4">A4</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="compact = !compact"
                                    :class="[compact ? 'bg-rose-100 border-rose-300 text-rose-700 dark:bg-rose-950' : 'bg-white border-slate-200 text-slate-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-300', 'inline-flex items-center gap-1 rounded-xl border px-2.5 py-1 text-xs font-bold transition shadow-sm']">
                                    Mode Ringkas
                                </button>
                                <button type="button" @click="printSheet()"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-rose-600 to-pink-600 px-3 py-1.5 text-xs font-bold text-white shadow-md transition hover:from-rose-500 active:scale-95">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" />
                                    </svg>
                                    Cetak Preview
                                </button>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-slate-200/70 p-4 sm:p-5 print:p-0 print:bg-transparent dark:bg-slate-900/60 shadow-inner overflow-auto">
                            <AssistanceLetterPreview :letter="previewLetter" :paper="paper" :compact="compact" :scaled="true" />
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
