<script setup>
import { computed, reactive, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SignaturePad from '@/Components/SignaturePad.vue';
import IncomeLetterPreview from '@/Components/IncomeLetterPreview.vue';
import { printSheet } from '@/lib/print';

const INPUT_CLASS = 'mt-1.5 block w-full rounded-xl border-slate-300/90 text-xs font-medium shadow-sm transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-slate-700/80 dark:bg-slate-900 dark:text-slate-200';
const SECTION_CLASS = 'rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/90';
const SECTION_TITLE = 'flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 px-5 py-3.5 text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wide';

const form = useForm({
    kabupaten:  'Tasikmalaya',
    kecamatan:  'Cigalontang',
    nama_desa:  'Sirnaputra',
    alamat:     '',
    kode_pos:   '',
    kontak:     '',
    email_desa: '',
    logo_kiri:  null,
    logo_kanan: null,

    nomor_surat:   '',
    tanggal_surat: new Date().toISOString().slice(0, 10),

    nama_warga:    '',
    nik:           '',
    tempat_lahir:  '',
    tanggal_lahir: '',
    jenis_kelamin: 'Perempuan',
    pekerjaan:     '',
    agama:         'Islam',
    alamat_warga:  '',
    rt:            '',
    rw:            '',
    dusun:         '',

    nominal_penghasilan:    '',
    keterangan_penghasilan: '',
    kalimat_penutup:        '',

    label_atasan:          '',
    jabatan_penandatangan: 'Kepala Desa Sirnaputra',
    nama_penandatangan:    'Suhendar',
    nip_niapd:             '',
    tanda_tangan:          null,
    stempel:               null,
});

const uploads = reactive({ logo_kiri: '/images/logo-tasikmalaya.png', logo_kanan: null, tanda_tangan: null, stempel: null });

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

    nama_warga:    form.nama_warga,
    nik:           form.nik,
    tempat_lahir:  form.tempat_lahir,
    tanggal_lahir: form.tanggal_lahir,
    jenis_kelamin: form.jenis_kelamin,
    pekerjaan:     form.pekerjaan,
    agama:         form.agama,
    alamat_warga:  form.alamat_warga,
    rt:            form.rt,
    rw:            form.rw,
    dusun:         form.dusun,

    nominal_penghasilan:    form.nominal_penghasilan,
    keterangan_penghasilan: form.keterangan_penghasilan,
    kalimat_penutup:        form.kalimat_penutup,

    label_atasan:          form.label_atasan,
    jabatan_penandatangan: form.jabatan_penandatangan,
    nama_penandatangan:    form.nama_penandatangan,
    nip_niapd:             form.nip_niapd,
    tanda_tangan:          uploads.tanda_tangan,
    stempel:               uploads.stempel,
}));

function submit() {
    form.post(route('surat-keterangan-penghasilan.store'), {
        preserveScroll: true,
        forceFormData: true,
    });
}
</script>

<template>
    <Head title="Buat Surat Keterangan Penghasilan" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Pembuatan Surat</div>
                    <h2 class="text-xl font-black text-slate-900 dark:text-white">Buat Surat Keterangan Penghasilan</h2>
                </div>
            </div>
        </template>

        <div class="py-10 print:p-0">
            <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8 print:max-w-none print:px-0">
                <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-2 print:grid-cols-1">

                    <!-- FORM -->
                    <div class="space-y-6 print:hidden">

                        <!-- 1. Kop Surat -->
                        <section :class="SECTION_CLASS" style="display: none;">
                            <h3 :class="SECTION_TITLE">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-emerald-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                </svg>
                                Kop Surat
                            </h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <InputLabel value="Logo Kabupaten / Kiri" :optional="true" />
                                    <input type="file" accept="image/*" @change="onImageUpload('logo_kiri', $event)"
                                        class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-emerald-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white" />
                                    <img v-if="uploads.logo_kiri" :src="uploads.logo_kiri" class="mt-2 h-12 object-contain" alt="Logo Kiri" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Logo Desa / Kanan" :optional="true" />
                                    <input type="file" accept="image/*" @change="onImageUpload('logo_kanan', $event)"
                                        class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-emerald-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white" />
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

                        <!-- 2. Nomor & Tanggal Surat -->
                        <section :class="SECTION_CLASS">
                            <h3 :class="SECTION_TITLE">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-emerald-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5" />
                                </svg>
                                Nomor &amp; Tanggal Surat
                            </h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <InputLabel value="Nomor Surat" />
                                    <input v-model="form.nomor_surat" type="text" placeholder="cth. 070/470/Ds/IV/2026" :class="INPUT_CLASS" />
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
                            <h3 :class="SECTION_TITLE">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-emerald-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                Data Warga
                            </h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <InputLabel value="Nama Lengkap" />
                                    <input v-model="form.nama_warga" type="text" placeholder="cth. Sari Hadiniah" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.nama_warga" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="NIK (Opsional)" :optional="true" />
                                    <input v-model="form.nik" type="text" placeholder="cth. 3206276003840002" :class="INPUT_CLASS" maxlength="20" />
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
                                        <option value="Perempuan">Perempuan</option>
                                        <option value="Laki – Laki">Laki – Laki</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel value="Agama" />
                                    <select v-model="form.agama" :class="INPUT_CLASS">
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Buddha">Buddha</option>
                                        <option value="Khonghucu">Khonghucu</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Pekerjaan" />
                                    <input v-model="form.pekerjaan" type="text" placeholder="cth. Mengurus Rumah Tangga" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.pekerjaan" />
                                </div>
                                <div>
                                    <InputLabel value="Dusun / Kampung" :optional="true" />
                                    <input v-model="form.dusun" type="text" placeholder="cth. Tegaltepus" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="RT" :optional="true" />
                                    <input v-model="form.rt" type="text" placeholder="cth. 023" :class="INPUT_CLASS" maxlength="5" />
                                </div>
                                <div>
                                    <InputLabel value="RW" :optional="true" />
                                    <input v-model="form.rw" type="text" placeholder="cth. 008" :class="INPUT_CLASS" maxlength="5" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Alamat Lengkap (Override)" :optional="true" />
                                    <textarea v-model="form.alamat_warga" rows="2" :class="INPUT_CLASS" placeholder="Kosongkan untuk diisi otomatis dari Dusun/RT/RW/Desa" />
                                </div>
                            </div>
                        </section>

                        <!-- 4. Detail Penghasilan -->
                        <section :class="SECTION_CLASS">
                            <h3 :class="SECTION_TITLE">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-emerald-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Detail Penghasilan &amp; Keterangan
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <InputLabel value="Nominal Penghasilan Rata-Rata (Opsional)" :optional="true" />
                                    <input v-model="form.nominal_penghasilan" type="text" placeholder="cth. Rp 1.500.000,- / bulan (kosongkan jika tidak berpenghasilan)" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Teks Keterangan Penghasilan (Override Teks Paragraf)" :optional="true" />
                                    <textarea v-model="form.keterangan_penghasilan" rows="3" :class="INPUT_CLASS"
                                        placeholder="Kosongkan untuk memakai teks standar: 'Orang tersebut di atas adalah benar Warga/Penduduk Desa kami, kemudian kami terangkan pula bahwa menurut orang yang bersangkutan orang tersebut benar benar tidak berpenghasilan.'" />
                                </div>
                                <div>
                                    <InputLabel value="Kalimat Penutup" :optional="true" />
                                    <textarea v-model="form.kalimat_penutup" rows="2" :class="INPUT_CLASS" placeholder="Kosongkan untuk memakai teks standar: 'Demikian surat keterangan ini kami buat dengan sebenarnya...'" />
                                </div>
                            </div>
                        </section>

                        <!-- 5. Penandatangan -->
                        <section :class="SECTION_CLASS">
                            <h3 :class="SECTION_TITLE">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-emerald-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                </svg>
                                Penandatangan (Kolom Kanan)
                            </h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <InputLabel value="Label Atas (cth: a.n Kepala Desa ...)" :optional="true" />
                                    <input v-model="form.label_atasan" type="text" placeholder="cth. a.n Kepala Desa Sirnaputra" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Jabatan Penandatangan" />
                                    <input v-model="form.jabatan_penandatangan" type="text" placeholder="cth. Kepala Desa Sirnaputra" :class="INPUT_CLASS" />
                                </div>
                                <div>
                                    <InputLabel value="Nama Penandatangan" />
                                    <input v-model="form.nama_penandatangan" type="text" placeholder="cth. Suhendar" :class="INPUT_CLASS" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="NIP / NIAPD" :optional="true" />
                                    <input v-model="form.nip_niapd" type="text" placeholder="cth. 19800101..." :class="INPUT_CLASS" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Tanda Tangan Digital (Gambar Langsung / Unggah Foto)" :optional="true" />
                                    <div class="mt-1">
                                        <SignaturePad
                                            :height="150"
                                            @change="(file, dataUrl) => { form.tanda_tangan = file; uploads.tanda_tangan = dataUrl; }"
                                        />
                                    </div>
                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                        <span>Atau unggah file gambar tanda tangan:</span>
                                        <input type="file" accept="image/*" @change="onImageUpload('tanda_tangan', $event)"
                                            class="text-xs text-gray-500 file:rounded file:border-0 file:bg-slate-200 file:px-2 file:py-1 file:text-xs file:font-semibold" />
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Upload Stempel PNG Transparan" :optional="true" />
                                    <input type="file" accept="image/*" @change="onImageUpload('stempel', $event)"
                                        class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-slate-700 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white" />
                                    <img v-if="uploads.stempel" :src="uploads.stempel" class="mt-2 h-14 object-contain opacity-60" alt="Stempel" />
                                </div>
                            </div>
                        </section>

                        <div class="flex flex-wrap items-center gap-3 pb-6">
                            <button type="button" @click="submit" :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-2.5 text-xs font-bold text-white shadow-md shadow-emerald-600/20 transition hover:from-emerald-500 active:scale-95 disabled:opacity-60">
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
                        <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm print:hidden dark:border-slate-800 dark:bg-slate-900 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Kertas:</span>
                                <select v-model="paper" class="rounded-lg border-slate-300 py-1 text-xs font-bold dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                                    <option value="f4">F4 / Folio</option>
                                    <option value="a4">A4</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="compact = !compact"
                                    :class="[compact ? 'bg-emerald-100 border-emerald-300 text-emerald-700 dark:bg-emerald-950' : 'bg-white border-slate-200 text-slate-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400', 'inline-flex items-center gap-1 rounded-xl border px-2.5 py-1 text-xs font-bold transition shadow-sm']">
                                    Mode Ringkas
                                </button>
                                <button type="button" @click="printSheet()"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-3 py-1.5 text-xs font-bold text-white shadow-md transition hover:from-emerald-500 active:scale-95">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" />
                                    </svg>
                                    Cetak Preview
                                </button>
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-100 p-4 print:p-0 print:bg-transparent dark:bg-slate-800/50 shadow-inner overflow-auto">
                            <IncomeLetterPreview :letter="previewLetter" :paper="paper" :compact="compact" :scaled="true" />
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
