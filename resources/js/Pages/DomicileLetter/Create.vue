<script setup>
import { computed, reactive, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import DomicileLetterPreview from '@/Components/DomicileLetterPreview.vue';
import { printSheet } from '@/lib/print';

// ─── Styling constants ───────────────────────────────────────────────
const INPUT_CLASS =
    'mt-1.5 block w-full rounded-xl border-slate-300/90 text-xs font-medium shadow-sm transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-700/80 dark:bg-slate-900 dark:text-slate-200 dark:focus:border-indigo-500';

const SECTION_CLASS =
    'rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/90 transition hover:border-slate-300/80 dark:hover:border-slate-700/80';

const SECTION_TITLE_CLASS =
    'mb-5 flex items-center gap-2.5 border-b border-slate-100 pb-3 text-xs font-black uppercase tracking-wider text-slate-800 dark:border-slate-800 dark:text-slate-200';

// ─── Form ────────────────────────────────────────────────────────────
const form = useForm({
    // Kop surat
    kabupaten:  '',
    kecamatan:  '',
    nama_desa:  '',
    alamat:     '',
    kode_pos:   '',
    kontak:     '',
    email_desa: '',
    logo_kiri:  null,
    logo_kanan: null,

    // Atribut surat
    nomor_surat:   '',
    tanggal_surat: new Date().toISOString().slice(0, 10),

    // Data warga
    nik:           '',
    nama_warga:    '',
    tempat_lahir:  '',
    tanggal_lahir: '',
    jenis_kelamin: 'Laki-laki',
    warga_negara:  'Indonesia',
    agama:         'Islam',
    pekerjaan:     '',
    alamat_warga:  '',
    rt:            '',
    rw:            '',
    dusun:         '',

    // Keterangan
    keterangan_domisili: '',
    catatan_ektp:        '',

    // Tanda tangan
    jabatan_penandatangan: 'Kepala Desa',
    nama_penandatangan:    '',
    nip_niapd:             '',
    tanda_tangan:          null,
    stempel:               null,
});

// ─── Image uploads (for preview) ─────────────────────────────────────
const uploads = reactive({
    logo_kiri:    null,
    logo_kanan:   null,
    tanda_tangan: null,
    stempel:      null,
});

const showPreview = ref(true);
const paper = ref('f4');
const isCompact = ref(false);

// ─── Helpers ─────────────────────────────────────────────────────────
function readAsDataUrl(file) {
    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.onload  = () => resolve(reader.result);
        reader.onerror = () => resolve(null);
        reader.readAsDataURL(file);
    });
}

async function onImageUpload(field, event) {
    const file = event.target.files[0];
    if (!file) {
        form[field]    = null;
        uploads[field] = null;
        return;
    }
    if (!file.type.startsWith('image/')) {
        window.alert('File harus berupa gambar (PNG/JPG).');
        event.target.value = '';
        return;
    }
    if (file.size > 5 * 1024 * 1024) {
        window.alert('Ukuran file maksimal 5MB.');
        event.target.value = '';
        return;
    }
    form[field]    = file;
    uploads[field] = await readAsDataUrl(file);
}

function filePreviewUrl(field) {
    return uploads[field];
}

// ─── Live Preview data ────────────────────────────────────────────────
const previewLetter = computed(() => ({
    kabupaten:  form.kabupaten,
    kecamatan:  form.kecamatan,
    nama_desa:  form.nama_desa,
    alamat:     form.alamat,
    kode_pos:   form.kode_pos,
    kontak:     form.kontak,
    email_desa: form.email_desa,
    logo_kiri:  uploads.logo_kiri,
    logo_kanan: uploads.logo_kanan,

    nomor_surat:   form.nomor_surat,
    tanggal_surat: form.tanggal_surat,

    nik:           form.nik,
    nama_warga:    form.nama_warga,
    tempat_lahir:  form.tempat_lahir,
    tanggal_lahir: form.tanggal_lahir,
    jenis_kelamin: form.jenis_kelamin,
    warga_negara:  form.warga_negara,
    agama:         form.agama,
    pekerjaan:     form.pekerjaan,
    alamat_warga:  form.alamat_warga,
    rt:            form.rt,
    rw:            form.rw,
    dusun:         form.dusun,

    keterangan_domisili: form.keterangan_domisili,
    catatan_ektp:        form.catatan_ektp,

    jabatan_penandatangan: form.jabatan_penandatangan,
    nama_penandatangan:    form.nama_penandatangan,
    nip_niapd:             form.nip_niapd,
    tanda_tangan:          uploads.tanda_tangan,
    stempel:               uploads.stempel,
}));

// ─── Actions ─────────────────────────────────────────────────────────
function submit() {
    form.post(route('surat-keterangan-domisili.store'), {
        preserveScroll: true,
    });
}

async function printPreview() {
    await printSheet();
}

function scrollToPreview() {
    document.getElementById('live-preview')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
</script>

<template>
    <Head title="Buat Surat Keterangan Domisili" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 text-white shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                    </svg>
                </div>
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400">Pelayanan Surat</div>
                    <h2 class="text-xl font-black text-slate-900 dark:text-white">Buat Surat Keterangan Domisili</h2>
                </div>
            </div>
        </template>

        <div class="py-10 print:p-0">
            <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8 print:max-w-none print:px-0">
                <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-2 print:grid-cols-1">

                    <!-- =============== FORM =============== -->
                    <div class="space-y-6 print:hidden">

                        <!-- 1. Kop Surat -->
                        <section :class="SECTION_CLASS">
                            <h3 :class="SECTION_TITLE_CLASS">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-indigo-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                </svg>
                                Kop Surat
                            </h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <!-- Logo Kiri -->
                                <div class="sm:col-span-2">
                                    <InputLabel value="Logo Kabupaten / Pemda (Kiri)" :optional="true" />
                                    <input type="file" accept="image/png,image/jpeg" @change="onImageUpload('logo_kiri', $event)"
                                        class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-indigo-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white" />
                                    <img v-if="filePreviewUrl('logo_kiri')" :src="filePreviewUrl('logo_kiri')" alt="Logo Kiri" class="mt-2 h-14 object-contain" />
                                    <InputError class="mt-1" :message="form.errors.logo_kiri" />
                                </div>
                                <!-- Logo Kanan -->
                                <div class="sm:col-span-2">
                                    <InputLabel value="Logo Desa (Kanan)" :optional="true" />
                                    <input type="file" accept="image/png,image/jpeg" @change="onImageUpload('logo_kanan', $event)"
                                        class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-indigo-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white" />
                                    <img v-if="filePreviewUrl('logo_kanan')" :src="filePreviewUrl('logo_kanan')" alt="Logo Kanan" class="mt-2 h-14 object-contain" />
                                    <InputError class="mt-1" :message="form.errors.logo_kanan" />
                                </div>

                                <div>
                                    <InputLabel value="Pemerintah Kabupaten" />
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
                                    <InputError class="mt-1" :message="form.errors.kode_pos" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Alamat Kantor Desa" />
                                    <textarea v-model="form.alamat" rows="2" placeholder="Jl. Pacihayan No.16 Sirnaputra Cigalontang" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.alamat" />
                                </div>
                                <div>
                                    <InputLabel value="Kontak / Telp" :optional="true" />
                                    <input v-model="form.kontak" type="text" placeholder="cth. 0812-xxxx-xxxx" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.kontak" />
                                </div>
                                <div>
                                    <InputLabel value="Email Desa" :optional="true" />
                                    <input v-model="form.email_desa" type="email" placeholder="cth. desa@gmail.com" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.email_desa" />
                                </div>
                            </div>
                        </section>

                        <!-- 2. Nomor & Tanggal Surat -->
                        <section :class="SECTION_CLASS">
                            <h3 :class="SECTION_TITLE_CLASS">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-indigo-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5" />
                                </svg>
                                Nomor &amp; Tanggal Surat
                            </h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <InputLabel value="Nomor Surat" />
                                    <input v-model="form.nomor_surat" type="text" placeholder="cth. 110/470/DS/2026" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.nomor_surat" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Tanggal Surat" />
                                    <input v-model="form.tanggal_surat" type="date" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.tanggal_surat" />
                                </div>
                            </div>
                        </section>

                        <!-- 3. Data Warga -->
                        <section :class="SECTION_CLASS">
                            <h3 :class="SECTION_TITLE_CLASS">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-indigo-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                Data Warga
                            </h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <InputLabel value="NIK E-KTP" />
                                    <input v-model="form.nik" type="text" placeholder="cth. 3206276206260001" :class="INPUT_CLASS" maxlength="16" />
                                    <InputError class="mt-1" :message="form.errors.nik" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Nama Lengkap Warga" />
                                    <input v-model="form.nama_warga" type="text" placeholder="cth. WINA WINARTI" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.nama_warga" />
                                </div>
                                <div>
                                    <InputLabel value="Tempat Lahir" />
                                    <input v-model="form.tempat_lahir" type="text" placeholder="cth. Tasikmalaya" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.tempat_lahir" />
                                </div>
                                <div>
                                    <InputLabel value="Tanggal Lahir" />
                                    <input v-model="form.tanggal_lahir" type="date" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.tanggal_lahir" />
                                </div>
                                <div>
                                    <InputLabel value="Jenis Kelamin" />
                                    <select v-model="form.jenis_kelamin" :class="INPUT_CLASS">
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                    <InputError class="mt-1" :message="form.errors.jenis_kelamin" />
                                </div>
                                <div>
                                    <InputLabel value="Warga Negara" />
                                    <input v-model="form.warga_negara" type="text" placeholder="Indonesia" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.warga_negara" />
                                </div>
                                <div>
                                    <InputLabel value="Agama" />
                                    <select v-model="form.agama" :class="INPUT_CLASS">
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen Protestan">Kristen Protestan</option>
                                        <option value="Kristen Katolik">Kristen Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Buddha">Buddha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    </select>
                                    <InputError class="mt-1" :message="form.errors.agama" />
                                </div>
                                <div>
                                    <InputLabel value="Pekerjaan" />
                                    <input v-model="form.pekerjaan" type="text" placeholder="cth. Mengurus Rumah Tangga" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.pekerjaan" />
                                </div>

                                <!-- Alamat Warga -->
                                <div class="sm:col-span-2">
                                    <InputLabel value="Dusun / Kampung" :optional="true" />
                                    <input v-model="form.dusun" type="text" placeholder="cth. Cilimus" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.dusun" />
                                </div>
                                <div>
                                    <InputLabel value="RT" :optional="true" />
                                    <input v-model="form.rt" type="text" placeholder="cth. 017" :class="INPUT_CLASS" maxlength="5" />
                                    <InputError class="mt-1" :message="form.errors.rt" />
                                </div>
                                <div>
                                    <InputLabel value="RW" :optional="true" />
                                    <input v-model="form.rw" type="text" placeholder="cth. 006" :class="INPUT_CLASS" maxlength="5" />
                                    <InputError class="mt-1" :message="form.errors.rw" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Alamat Lengkap (Opsional, jika ingin override auto-format)" :optional="true" />
                                    <textarea v-model="form.alamat_warga" rows="2" placeholder="Kosongkan untuk otomatis dibuat dari Kp/RT/RW/Desa di atas" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.alamat_warga" />
                                </div>
                            </div>
                        </section>

                        <!-- 4. Keterangan Tambahan -->
                        <section :class="SECTION_CLASS">
                            <h3 :class="SECTION_TITLE_CLASS">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-indigo-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                                </svg>
                                Keterangan Tambahan
                                <span class="ml-auto text-[10px] font-normal text-slate-400">(Opsional, ada teks default)</span>
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <InputLabel value="Paragraf Keterangan Domisili" :optional="true" />
                                    <textarea
                                        v-model="form.keterangan_domisili"
                                        rows="4"
                                        placeholder="Kosongkan untuk menggunakan teks default: 'Orang tersebut di atas adalah benar Warga/Penduduk Desa kami yang berdomisili di ...'"
                                        :class="INPUT_CLASS"
                                    />
                                    <InputError class="mt-1" :message="form.errors.keterangan_domisili" />
                                </div>
                                <div>
                                    <InputLabel value="Catatan E-KTP" :optional="true" />
                                    <textarea
                                        v-model="form.catatan_ektp"
                                        rows="2"
                                        placeholder="Kosongkan untuk tidak ada catatan tambahan E-KTP"
                                        :class="INPUT_CLASS"
                                    />
                                    <InputError class="mt-1" :message="form.errors.catatan_ektp" />
                                </div>
                            </div>
                        </section>

                        <!-- 5. Tanda Tangan -->
                        <section :class="SECTION_CLASS">
                            <h3 :class="SECTION_TITLE_CLASS">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-indigo-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                </svg>
                                Tanda Tangan Kepala Desa
                            </h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <InputLabel value="Jabatan Penandatangan" />
                                    <input v-model="form.jabatan_penandatangan" type="text" placeholder="cth. Kepala Desa" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.jabatan_penandatangan" />
                                </div>
                                <div>
                                    <InputLabel value="NIP / NIAPD" :optional="true" />
                                    <input v-model="form.nip_niapd" type="text" placeholder="cth. 19800101 200604 1 001" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.nip_niapd" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Nama Penandatangan" />
                                    <input v-model="form.nama_penandatangan" type="text" placeholder="cth. AHMAD FAUZI" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.nama_penandatangan" />
                                </div>
                                <div>
                                    <InputLabel value="Upload Tanda Tangan (PNG transparan)" :optional="true" />
                                    <input type="file" accept="image/png,image/jpeg" @change="onImageUpload('tanda_tangan', $event)"
                                        class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-indigo-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white" />
                                    <img v-if="filePreviewUrl('tanda_tangan')" :src="filePreviewUrl('tanda_tangan')" alt="TTD" class="mt-2 h-12 object-contain" />
                                    <InputError class="mt-1" :message="form.errors.tanda_tangan" />
                                </div>
                                <div>
                                    <InputLabel value="Upload Stempel Desa" :optional="true" />
                                    <input type="file" accept="image/png,image/jpeg" @change="onImageUpload('stempel', $event)"
                                        class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-indigo-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white" />
                                    <img v-if="filePreviewUrl('stempel')" :src="filePreviewUrl('stempel')" alt="Stempel" class="mt-2 h-12 object-contain" />
                                    <InputError class="mt-1" :message="form.errors.stempel" />
                                </div>
                            </div>
                        </section>

                        <!-- Submit -->
                        <div class="flex flex-wrap items-center gap-3 pb-6">
                            <button
                                type="button"
                                @click="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-2.5 text-xs font-bold text-white shadow-md shadow-indigo-600/20 transition hover:from-indigo-500 hover:to-violet-500 active:scale-95 disabled:opacity-60"
                            >
                                <svg v-if="!form.processing" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                                <svg v-else class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                {{ form.processing ? 'Menyimpan...' : 'Simpan &amp; Cetak Surat' }}
                            </button>
                            <button
                                type="button"
                                @click="scrollToPreview"
                                class="inline-flex items-center gap-2 rounded-xl border border-indigo-300 bg-white px-4 py-2.5 text-xs font-bold text-indigo-700 shadow-sm transition hover:bg-indigo-50 dark:border-indigo-700 dark:bg-slate-900 dark:text-indigo-300 xl:hidden"
                            >
                                Lihat Preview ↓
                            </button>
                        </div>
                    </div>

                    <!-- =============== LIVE PREVIEW =============== -->
                    <div id="live-preview" class="sticky top-4 space-y-4">
                        <!-- Controls -->
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-3 shadow-sm print:hidden dark:border-slate-800 dark:bg-slate-900 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Kertas:</span>
                                <select
                                    v-model="paper"
                                    class="rounded-lg border-slate-300 py-1 text-xs font-bold text-slate-800 focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200"
                                >
                                    <option value="f4">F4 / Folio (210×330 mm)</option>
                                    <option value="a4">A4 (210×297 mm)</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="isCompact = !isCompact"
                                    :class="[
                                        isCompact
                                            ? 'bg-indigo-100 border-indigo-300 text-indigo-700 dark:bg-indigo-950 dark:border-indigo-700 dark:text-indigo-300'
                                            : 'bg-white border-slate-200 text-slate-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-300',
                                        'inline-flex items-center gap-1 rounded-xl border px-2.5 py-1 text-xs font-bold transition shadow-sm'
                                    ]"
                                >
                                    Mode Ringkas
                                </button>
                                <button
                                    type="button"
                                    @click="printPreview"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-3 py-1.5 text-xs font-bold text-white shadow-md shadow-indigo-600/20 transition hover:from-indigo-500 active:scale-95"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" />
                                    </svg>
                                    Cetak Preview
                                </button>
                            </div>
                        </div>

                        <!-- Preview Surat -->
                        <div class="rounded-2xl bg-slate-200/70 p-4 sm:p-5 print:p-0 print:bg-transparent dark:bg-slate-900/60 shadow-inner overflow-auto">
                            <DomicileLetterPreview
                                :letter="previewLetter"
                                :paper="paper"
                                :scaled="true"
                                :compact="isCompact"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
