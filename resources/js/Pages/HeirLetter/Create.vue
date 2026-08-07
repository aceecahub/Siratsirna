<script setup>
import { computed, reactive, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import HeirLetterPreview from '@/Components/HeirLetterPreview.vue';
import { printSheet } from '@/lib/print';

const INPUT_CLASS = 'mt-1.5 block w-full rounded-xl border-slate-300/90 text-xs font-medium shadow-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700/80 dark:bg-slate-900 dark:text-slate-200';
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

    nomor_surat:   '',
    tanggal_surat: new Date().toISOString().slice(0, 10),

    nama_kades:    'Suhendar',
    jabatan_kades: 'Kepala Desa Sirnaputra',

    nama_almarhum:          '',
    nik_almarhum:           '',
    jenis_kelamin_almarhum: 'Laki-Laki',
    tempat_lahir_almarhum:  '',
    tanggal_lahir_almarhum: '',
    agama_almarhum:         'Islam',
    alamat_almarhum:        '',
    rt_almarhum:            '',
    rw_almarhum:            '',
    dusun_almarhum:         '',

    hari_meninggal:    'selasa',
    tanggal_meninggal: '',
    tempat_meninggal:  'Rumah',
    sebab_meninggal:   'sakit',
    tpu_pemakaman:     '',

    nama_yang_menyatakan:  '',
    label_yang_menyatakan: 'Ahli waris',

    no_reg_camat:  '',
    jabatan_camat: 'Camat Cigalontang',
    nama_camat:    '',

    tanda_tangan: null,
    stempel:      null,
});

// Dynamic lists
const ahliWarisList = reactive([
    { nama: 'Neni', tempat_lahir: 'Tasikmalaya', tanggal_lahir: '1976-03-18', pekerjaan: 'Mengurus Rumah Tangga', alamat: 'Kp Cipadani RT 004 RW 001 Desa Sirnaputra Kecamatan Cigalontang Kabupaten Tasikmalaya', nik: '3206275803760001', hubungan: 'Istri' },
    { nama: 'Mira Julianti', tempat_lahir: 'Tasikmalaya', tanggal_lahir: '2024-07-27', pekerjaan: 'Pelajar/ Mahasiswa', alamat: 'Kp Cipadani RT 004 RW 001 Desa Sirnaputra Kecamatan Cigalontang Kabupaten Tasikmalaya', nik: '', hubungan: 'Anak' },
]);

const saksiList = reactive([
    { nama: 'Nisa Nugraha Fitria' },
    { nama: 'Tita Karsita' },
]);

function addAhliWaris() {
    ahliWarisList.push({ nama: '', tempat_lahir: '', tanggal_lahir: '', pekerjaan: '', alamat: '', nik: '', hubungan: '' });
}
function removeAhliWaris(idx) {
    if (ahliWarisList.length > 1) ahliWarisList.splice(idx, 1);
}

function addSaksi() {
    saksiList.push({ nama: '' });
}
function removeSaksi(idx) {
    if (saksiList.length > 1) saksiList.splice(idx, 1);
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

    nomor_surat:   form.nomor_surat,
    tanggal_surat: form.tanggal_surat,

    nama_kades:    form.nama_kades,
    jabatan_kades: form.jabatan_kades,

    nama_almarhum:          form.nama_almarhum,
    nik_almarhum:           form.nik_almarhum,
    jenis_kelamin_almarhum: form.jenis_kelamin_almarhum,
    tempat_lahir_almarhum:  form.tempat_lahir_almarhum,
    tanggal_lahir_almarhum: form.tanggal_lahir_almarhum,
    agama_almarhum:         form.agama_almarhum,
    alamat_almarhum:        form.alamat_almarhum,
    rt_almarhum:            form.rt_almarhum,
    rw_almarhum:            form.rw_almarhum,
    dusun_almarhum:         form.dusun_almarhum,

    hari_meninggal:    form.hari_meninggal,
    tanggal_meninggal: form.tanggal_meninggal,
    tempat_meninggal:  form.tempat_meninggal,
    sebab_meninggal:   form.sebab_meninggal,
    tpu_pemakaman:     form.tpu_pemakaman,

    ahli_waris_list: ahliWarisList,
    saksi_list:      saksiList,

    nama_yang_menyatakan:  form.nama_yang_menyatakan,
    label_yang_menyatakan: form.label_yang_menyatakan,

    no_reg_camat:  form.no_reg_camat,
    jabatan_camat: form.jabatan_camat,
    nama_camat:    form.nama_camat,

    tanda_tangan: uploads.tanda_tangan,
    stempel:      uploads.stempel,
}));

function submit() {
    // Transform arrays into JSON string before submission
    form.transform((data) => ({
        ...data,
        ahli_waris_list: JSON.stringify(ahliWarisList),
        saksi_list:      JSON.stringify(saksiList),
    })).post(route('surat-ahli-waris.store'), {
        preserveScroll: true,
        forceFormData: true,
    });
}
</script>

<template>
    <Head title="Buat Surat Keterangan Ahli Waris" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197M12 11.25a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z" />
                    </svg>
                </div>
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-blue-600 dark:text-blue-400">Pembuatan Surat</div>
                    <h2 class="text-xl font-black text-slate-900 dark:text-white">Buat Surat Keterangan Ahli Waris</h2>
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
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                </svg>
                                Kop Surat
                            </h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <InputLabel value="Logo Kabupaten / Kiri" :optional="true" />
                                    <input type="file" accept="image/*" @change="onImageUpload('logo_kiri', $event)"
                                        class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-blue-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white" />
                                    <img v-if="uploads.logo_kiri" :src="uploads.logo_kiri" class="mt-2 h-12 object-contain" alt="Logo Kiri" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Logo Desa / Kanan" :optional="true" />
                                    <input type="file" accept="image/*" @change="onImageUpload('logo_kanan', $event)"
                                        class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-blue-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white" />
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

                        <!-- 2. Nomor & Pejabat Desa -->
                        <section :class="SECTION_CLASS">
                            <h3 :class="SECTION_TITLE">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5" />
                                </svg>
                                Nomor &amp; Pejabat Desa
                            </h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <InputLabel value="Nomor Surat" />
                                    <input v-model="form.nomor_surat" type="text" placeholder="cth. 050/170/Ds/2026" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.nomor_surat" />
                                </div>
                                <div>
                                    <InputLabel value="Tanggal Surat" />
                                    <input v-model="form.tanggal_surat" type="date" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.tanggal_surat" />
                                </div>
                                <div>
                                    <InputLabel value="Nama Kepala Desa" />
                                    <input v-model="form.nama_kades" type="text" placeholder="cth. Suhendar" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Jabatan Kepala Desa" />
                                    <input v-model="form.jabatan_kades" type="text" placeholder="cth. Kepala Desa Sirnaputra" :class="INPUT_CLASS" />
                                </div>
                            </div>
                        </section>

                        <!-- 3. Data Almarhum / Almarhumah -->
                        <section :class="SECTION_CLASS">
                            <h3 :class="SECTION_TITLE">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                Data Almarhum / Almarhumah
                            </h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <InputLabel value="Nama Almarhum / Almarhumah" />
                                    <input v-model="form.nama_almarhum" type="text" placeholder="cth. Dadang" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.nama_almarhum" />
                                </div>
                                <div>
                                    <InputLabel value="NIK Almarhum" :optional="true" />
                                    <input v-model="form.nik_almarhum" type="text" placeholder="cth. 3206271010710001" :class="INPUT_CLASS" maxlength="20" />
                                </div>
                                <div>
                                    <InputLabel value="Jenis Kelamin" />
                                    <select v-model="form.jenis_kelamin_almarhum" :class="INPUT_CLASS">
                                        <option value="Laki-Laki">Laki-Laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel value="Tempat Lahir" :optional="true" />
                                    <input v-model="form.tempat_lahir_almarhum" type="text" placeholder="cth. Tasikmalaya" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Tanggal Lahir" :optional="true" />
                                    <input v-model="form.tanggal_lahir_almarhum" type="date" :class="INPUT_CLASS" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Agama" />
                                    <select v-model="form.agama_almarhum" :class="INPUT_CLASS">
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Buddha">Buddha</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel value="Dusun / Kampung" :optional="true" />
                                    <input v-model="form.dusun_almarhum" type="text" placeholder="cth. Cipadani" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="RT" :optional="true" />
                                    <input v-model="form.rt_almarhum" type="text" placeholder="cth. 004" :class="INPUT_CLASS" maxlength="5" />
                                </div>
                                <div>
                                    <InputLabel value="RW" :optional="true" />
                                    <input v-model="form.rw_almarhum" type="text" placeholder="cth. 001" :class="INPUT_CLASS" maxlength="5" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Alamat Lengkap Almarhum (Override)" :optional="true" />
                                    <textarea v-model="form.alamat_almarhum" rows="2" :class="INPUT_CLASS" placeholder="Kosongkan untuk diisi otomatis dari Dusun/RT/RW/Desa" />
                                </div>
                            </div>
                        </section>

                        <!-- 4. Detail Kematian -->
                        <section :class="SECTION_CLASS">
                            <h3 :class="SECTION_TITLE">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                                Detail Meninggal Dunia
                            </h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <InputLabel value="Hari Meninggal" :optional="true" />
                                    <input v-model="form.hari_meninggal" type="text" placeholder="cth. selasa" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Tanggal Meninggal" :optional="true" />
                                    <input v-model="form.tanggal_meninggal" type="date" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Tempat Meninggal" :optional="true" />
                                    <input v-model="form.tempat_meninggal" type="text" placeholder="cth. Rumah" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Sebab Meninggal" :optional="true" />
                                    <input v-model="form.sebab_meninggal" type="text" placeholder="cth. sakit" :class="INPUT_CLASS" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Lokasi Pemakaman / TPU" :optional="true" />
                                    <input v-model="form.tpu_pemakaman" type="text" placeholder="cth. TPU Gunung Tanjung Desa Sirnaputra" :class="INPUT_CLASS" />
                                </div>
                            </div>
                        </section>

                        <!-- 5. List Dinamis Ahli Waris -->
                        <section :class="SECTION_CLASS">
                            <div class="mb-5 flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3 dark:border-slate-800">
                                <h3 :class="SECTION_TITLE" class="!mb-0 !border-0 !pb-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197M12 11.25a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z" />
                                    </svg>
                                    Daftar Nama Ahli Waris (Dinamis)
                                </h3>
                                <button type="button" @click="addAhliWaris"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition hover:from-blue-500 active:scale-95">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Tambah Ahli Waris
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div v-for="(item, idx) in ahliWarisList" :key="idx"
                                    class="relative rounded-2xl border border-slate-200/90 bg-slate-50/70 p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                                    <div class="mb-3 flex items-center justify-between border-b border-slate-200/80 pb-2 dark:border-slate-800">
                                        <span class="inline-flex items-center gap-2 text-xs font-black text-slate-800 dark:text-slate-200">
                                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-100 text-blue-800 font-extrabold text-[10px] dark:bg-blue-950 dark:text-blue-300">
                                                {{ idx + 1 }}
                                            </span>
                                            Ahli Waris #{{ idx + 1 }}
                                        </span>
                                        <button v-if="ahliWarisList.length > 1" type="button" @click="removeAhliWaris(idx)"
                                            class="text-xs font-bold text-red-600 hover:underline dark:text-red-400">
                                            Hapus
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div>
                                            <InputLabel value="Nama Lengkap" />
                                            <input v-model="item.nama" type="text" placeholder="cth. Neni / Mira Julianti" :class="INPUT_CLASS" />
                                        </div>
                                        <div>
                                            <InputLabel value="NIK (Opsional)" :optional="true" />
                                            <input v-model="item.nik" type="text" placeholder="cth. 3206275803760001" :class="INPUT_CLASS" maxlength="20" />
                                        </div>
                                        <div>
                                            <InputLabel value="Tempat Lahir" :optional="true" />
                                            <input v-model="item.tempat_lahir" type="text" placeholder="cth. Tasikmalaya" :class="INPUT_CLASS" />
                                        </div>
                                        <div>
                                            <InputLabel value="Tanggal Lahir" :optional="true" />
                                            <input v-model="item.tanggal_lahir" type="date" :class="INPUT_CLASS" />
                                        </div>
                                        <div>
                                            <InputLabel value="Pekerjaan" :optional="true" />
                                            <input v-model="item.pekerjaan" type="text" placeholder="cth. Mengurus Rumah Tangga / Pelajar" :class="INPUT_CLASS" />
                                        </div>
                                        <div>
                                            <InputLabel value="Hubungan Keluarga" :optional="true" />
                                            <input v-model="item.hubungan" type="text" placeholder="cth. Istri / Anak" :class="INPUT_CLASS" />
                                        </div>
                                        <div class="sm:col-span-2">
                                            <InputLabel value="Alamat Lengkap" :optional="true" />
                                            <textarea v-model="item.alamat" rows="2" :class="INPUT_CLASS" placeholder="cth. Kp Cipadani RT 004 RW 001 Desa Sirnaputra..." />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- 6. Saksi - Saksi (Dinamis) -->
                        <section :class="SECTION_CLASS">
                            <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                                <h3 :class="SECTION_TITLE" class="!mb-0 !border-0 !pb-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                    Saksi - Saksi (Dinamis)
                                </h3>
                                <button type="button" @click="addSaksi"
                                    class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700 hover:bg-blue-100 dark:bg-blue-950 dark:text-blue-300">
                                    + Saksi
                                </button>
                            </div>
                            <div class="space-y-3">
                                <div v-for="(saksi, idx) in saksiList" :key="idx" class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-500 w-6">#{{ idx + 1 }}</span>
                                    <input v-model="saksi.nama" type="text" placeholder="Nama Saksi" :class="INPUT_CLASS" class="flex-1 !mt-0" />
                                    <button v-if="saksiList.length > 1" type="button" @click="removeSaksi(idx)" class="text-xs text-red-600 hover:underline px-1">Hapus</button>
                                </div>
                            </div>
                        </section>

                        <!-- 7. Yang Menyatakan & Pengesahan Camat -->
                        <section :class="SECTION_CLASS">
                            <h3 :class="SECTION_TITLE">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-blue-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                </svg>
                                Yang Menyatakan &amp; Pengesahan
                            </h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <InputLabel value="Nama Yang Menyatakan (Ahli Waris Utama)" :optional="true" />
                                    <input v-model="form.nama_yang_menyatakan" type="text" placeholder="cth. Neni (kosongkan = ahli waris pertama)" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Label Yang Menyatakan" :optional="true" />
                                    <input v-model="form.label_yang_menyatakan" type="text" placeholder="Ahli waris" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="No Reg Camat" :optional="true" />
                                    <input v-model="form.no_reg_camat" type="text" placeholder="cth. 050/..." :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Jabatan Camat" :optional="true" />
                                    <input v-model="form.jabatan_camat" type="text" placeholder="Camat Cigalontang" :class="INPUT_CLASS" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Nama Camat (Opsional)" :optional="true" />
                                    <input v-model="form.nama_camat" type="text" placeholder="Nama Camat" :class="INPUT_CLASS" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Upload Tanda Tangan Kades (PNG Transparan)" :optional="true" />
                                    <input type="file" accept="image/*" @change="onImageUpload('tanda_tangan', $event)"
                                        class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-blue-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white" />
                                    <img v-if="uploads.tanda_tangan" :src="uploads.tanda_tangan" class="mt-2 h-12 object-contain" alt="TTD" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Upload Stempel Desa PNG Transparan" :optional="true" />
                                    <input type="file" accept="image/*" @change="onImageUpload('stempel', $event)"
                                        class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-slate-700 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white" />
                                    <img v-if="uploads.stempel" :src="uploads.stempel" class="mt-2 h-14 object-contain opacity-60" alt="Stempel" />
                                </div>
                            </div>
                        </section>

                        <div class="flex flex-wrap items-center gap-3 pb-6">
                            <button type="button" @click="submit" :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-2.5 text-xs font-bold text-white shadow-md shadow-blue-600/20 transition hover:from-blue-500 active:scale-95 disabled:opacity-60">
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
                                    :class="[compact ? 'bg-blue-100 border-blue-300 text-blue-700 dark:bg-blue-950' : 'bg-white border-slate-200 text-slate-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-300', 'inline-flex items-center gap-1 rounded-xl border px-2.5 py-1 text-xs font-bold transition shadow-sm']">
                                    Mode Ringkas
                                </button>
                                <button type="button" @click="printSheet()"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-3 py-1.5 text-xs font-bold text-white shadow-md transition hover:from-blue-500 active:scale-95">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" />
                                    </svg>
                                    Cetak Preview
                                </button>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-slate-200/70 p-4 sm:p-5 print:p-0 print:bg-transparent dark:bg-slate-900/60 shadow-inner overflow-auto">
                            <HeirLetterPreview :letter="previewLetter" :paper="paper" :compact="compact" :scaled="true" />
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
