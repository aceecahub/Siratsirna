<script setup>
import { computed, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import LetterPreview from '@/Components/LetterPreview.vue';
import SignaturePad from '@/Components/SignaturePad.vue';
import { printSheet } from '@/lib/print';

const props = defineProps({
    templates: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    kabupaten: '',
    kecamatan: '',
    nama_desa: '',
    alamat: '',
    kode_pos: '',
    kontak: '',
    email_desa: '',
    logo_kiri: null,
    logo_kanan: null,

    nomor_surat: '',
    sifat: 'Biasa',
    lampiran: '-',
    perihal: '',
    tanggal_surat: new Date().toISOString().slice(0, 10),
    tujuan: '',

    pembuka: '',
    isi_surat: '',
    penutup: '',

    jabatan_penandatangan: '',
    nama_penandatangan: '',
    nip_niapd: '',
    tanda_tangan: null,
    stempel: null,

    penandatangan_list: [
        {
            peran: 'kanan',
            label_mengetahui: '',
            jabatan: '',
            nama: '',
            nip_niapd: '',
            tanda_tangan: null,
            stempel: null,
        },
    ],
});

const uploads = {
    logo_kiri: null,
    logo_kanan: null,
    tanda_tangan: null,
    stempel: null,
};

const INPUT_CLASS =
    'mt-1.5 block w-full rounded-xl border-slate-300/90 text-xs font-medium shadow-sm transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-slate-700/80 dark:bg-slate-900 dark:text-slate-200 dark:focus:border-emerald-500';

const SECTION_CLASS =
    'rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/90 transition hover:border-slate-300/80 dark:hover:border-slate-700/80';

const SECTION_TITLE_CLASS =
    'mb-5 flex items-center gap-2.5 border-b border-slate-100 pb-3 text-xs font-black uppercase tracking-wider text-slate-800 dark:border-slate-800 dark:text-slate-200';

function readAsDataUrl(file) {
    return new Promise((resolve) => {
        const reader = new FileReader();

        reader.onload = () => resolve(reader.result);
        reader.onerror = () => resolve(null);
        reader.readAsDataURL(file);
    });
}

async function onImageUpload(field, event) {
    const file = event.target.files[0];

    if (!file) {
        form[field] = null;
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

    form[field] = file;
    uploads[field] = await readAsDataUrl(file);
}

function applyTemplate(event) {
    const raw = event.target.value;

    if (!raw) return;

    const mainSignerJabatan = form.penandatangan_list[0]?.jabatan || form.jabatan_penandatangan || '{Jabatan}';

    form.pembuka = raw
        .replace('{jabatan}', mainSignerJabatan)
        .replace('{nama_desa}', form.nama_desa || '{Nama Desa}')
        .replace('{tujuan}', form.tujuan || '{Tujuan}');
}

const showPreview = ref(true);
const paper = ref('f4');
const isCompact = ref(false);

function addSigner() {
    let nextPeran = 'kanan';
    if (form.penandatangan_list.length === 1) {
        nextPeran = 'kiri';
    } else {
        nextPeran = 'mengetahui';
    }

    form.penandatangan_list.push({
        peran: nextPeran,
        label_mengetahui: nextPeran === 'mengetahui' ? 'Mengetahui,' : '',
        jabatan: '',
        nama: '',
        nip_niapd: '',
        tanda_tangan: null,
        stempel: null,
    });
}

function removeSigner(index) {
    if (form.penandatangan_list.length > 1) {
        form.penandatangan_list.splice(index, 1);
    }
}

async function onSignerSignatureChange(index, file, dataUrl) {
    const value = dataUrl ?? (file ? await readAsDataUrl(file) : null);
    form.penandatangan_list[index].tanda_tangan = value;
    if (file) {
        form.penandatangan_list[index].tanda_tangan_file = file;
    }
}

async function onSignerImageUpload(index, event) {
    const file = event.target.files[0];
    if (!file) {
        form.penandatangan_list[index].tanda_tangan = null;
        form.penandatangan_list[index].tanda_tangan_file = null;

        return;
    }
    if (!file.type.startsWith('image/')) {
        window.alert('File harus berupa gambar (PNG/JPG).');
        event.target.value = '';

        return;
    }
    form.penandatangan_list[index].tanda_tangan_file = file;
    const dataUrl = await readAsDataUrl(file);
    form.penandatangan_list[index].tanda_tangan = dataUrl;
}

async function onSignerStempelUpload(index, event) {
    const file = event.target.files[0];
    if (!file) {
        form.penandatangan_list[index].stempel = null;
        form.penandatangan_list[index].stempel_file = null;

        return;
    }
    if (!file.type.startsWith('image/')) {
        window.alert('File stempel harus berupa gambar (PNG/JPG).');
        event.target.value = '';

        return;
    }
    form.penandatangan_list[index].stempel_file = file;
    const dataUrl = await readAsDataUrl(file);
    form.penandatangan_list[index].stempel = dataUrl;
}

function scrollToPreview() {
    document
        .getElementById('live-preview')
        ?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

const previewLetter = computed(() => ({
    kabupaten: form.kabupaten,
    kecamatan: form.kecamatan,
    nama_desa: form.nama_desa,
    alamat: form.alamat,
    kode_pos: form.kode_pos,
    kontak: form.kontak,
    email_desa: form.email_desa,
    logo_kiri: uploads.logo_kiri,
    logo_kanan: uploads.logo_kanan,

    nomor_surat: form.nomor_surat,
    sifat: form.sifat,
    lampiran: form.lampiran,
    perihal: form.perihal,
    tanggal_surat: form.tanggal_surat,
    tujuan: form.tujuan,

    pembuka: form.pembuka,
    isi_surat: form.isi_surat,
    penutup: form.penutup,

    jabatan_penandatangan: form.penandatangan_list[0]?.jabatan || form.jabatan_penandatangan,
    nama_penandatangan: form.penandatangan_list[0]?.nama || form.nama_penandatangan,
    nip_niapd: form.penandatangan_list[0]?.nip_niapd || form.nip_niapd,
    tanda_tangan: form.penandatangan_list[0]?.tanda_tangan || uploads.tanda_tangan,
    stempel: uploads.stempel,
    penandatangan_list: form.penandatangan_list,
}));

function submit() {
    form.post(route('surat-keluar.store'), {
        preserveScroll: true,
    });
}

async function printPreview() {
    await printSheet();
}

function filePreviewUrl(field) {
    return uploads[field];
}
</script>

<template>
    <Head title="Buat Surat Keluar" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Generator Surat Keluar
            </h2>
        </template>

        <div class="py-12 print:p-0">
            <div
                class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8 print:max-w-none print:px-0"
            >
                <div
                    class="grid grid-cols-1 items-start gap-6 xl:grid-cols-2 print:grid-cols-1"
                >
                    <!-- ===================== FORM ===================== -->
                    <div class="space-y-6 print:hidden">
                        <!-- Kop Surat -->
                        <section :class="SECTION_CLASS">
                            <h3 :class="SECTION_TITLE_CLASS">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.8"
                                    stroke="currentColor"
                                    class="h-5 w-5 text-indigo-500"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"
                                    />
                                </svg>
                                Kop Surat
                            </h3>

                            <div
                                class="grid grid-cols-1 gap-4 sm:grid-cols-2"
                            >
                                <div class="sm:col-span-2">
                                    <InputLabel value="Logo Kop Surat (Kabupaten / Pemda - Sebelah Kiri)" :optional="true" />
                                    <input
                                        type="file"
                                        accept="image/png,image/jpeg"
                                        @change="onImageUpload('logo_kiri', $event)"
                                        class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-gray-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white dark:text-gray-400 dark:file:bg-gray-200 dark:file:text-gray-800"
                                    />
                                    <img
                                        v-if="filePreviewUrl('logo_kiri')"
                                        :src="filePreviewUrl('logo_kiri')"
                                        alt="Logo Kop Surat"
                                        class="mt-2 h-14 object-contain"
                                    />
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.logo_kiri"
                                    />
                                </div>

                                <div>
                                    <InputLabel value="Pemerintah Kabupaten" />
                                    <input
                                        v-model="form.kabupaten"
                                        type="text"
                                        placeholder="cth. Bandung"
                                        :class="INPUT_CLASS"
                                    />
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.kabupaten"
                                    />
                                </div>
                                <div>
                                    <InputLabel value="Kecamatan" />
                                    <input
                                        v-model="form.kecamatan"
                                        type="text"
                                        placeholder="cth. Soreang"
                                        :class="INPUT_CLASS"
                                    />
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.kecamatan"
                                    />
                                </div>

                                <div>
                                    <InputLabel value="Nama Desa" />
                                    <input
                                        v-model="form.nama_desa"
                                        type="text"
                                        placeholder="cth. Siratsirna"
                                        :class="INPUT_CLASS"
                                    />
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.nama_desa"
                                    />
                                </div>
                                <div>
                                    <InputLabel value="Kode Pos" :optional="true" />
                                    <input
                                        v-model="form.kode_pos"
                                        type="text"
                                        placeholder="cth. 40951"
                                        :class="INPUT_CLASS"
                                    />
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.kode_pos"
                                    />
                                </div>

                                <div class="sm:col-span-2">
                                    <InputLabel value="Alamat Lengkap" />
                                    <textarea
                                        v-model="form.alamat"
                                        rows="2"
                                        placeholder="Jalan / RT RW / Dusun ..."
                                        :class="INPUT_CLASS"
                                    />
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.alamat"
                                    />
                                </div>

                                <div>
                                    <InputLabel value="Kontak (Telp/Hp)" :optional="true" />
                                    <input
                                        v-model="form.kontak"
                                        type="text"
                                        placeholder="cth. 0812-3456-7890"
                                        :class="INPUT_CLASS"
                                    />
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.kontak"
                                    />
                                </div>
                                <div>
                                    <InputLabel value="Email Desa" :optional="true" />
                                    <input
                                        v-model="form.email_desa"
                                        type="email"
                                        placeholder="desa@example.com"
                                        :class="INPUT_CLASS"
                                    />
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.email_desa"
                                    />
                                </div>
                            </div>
                        </section>

                        <!-- Atribut Surat -->
                        <section :class="SECTION_CLASS">
                            <h3 :class="SECTION_TITLE_CLASS">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.8"
                                    stroke="currentColor"
                                    class="h-5 w-5 text-indigo-500"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"
                                    />
                                </svg>
                                Atribut Surat
                            </h3>

                            <div
                                class="grid grid-cols-1 gap-4 sm:grid-cols-2"
                            >
                                <div>
                                    <InputLabel value="Nomor Surat" />
                                    <input
                                        v-model="form.nomor_surat"
                                        type="text"
                                        placeholder="cth. 421.2/010/PD/2026"
                                        :class="INPUT_CLASS"
                                    />
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.nomor_surat"
                                    />
                                </div>
                                <div>
                                    <InputLabel value="Sifat" />
                                    <select
                                        v-model="form.sifat"
                                        :class="INPUT_CLASS"
                                    >
                                        <option value="Biasa">Biasa</option>
                                        <option value="Penting">Penting</option>
                                        <option value="Rahasia">Rahasia</option>
                                    </select>
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.sifat"
                                    />
                                </div>

                                <div>
                                    <InputLabel value="Lampiran" :optional="true" />
                                    <input
                                        v-model="form.lampiran"
                                        type="text"
                                        placeholder="cth. 1 (satu) berkas / -"
                                        :class="INPUT_CLASS"
                                    />
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.lampiran"
                                    />
                                </div>
                                <div>
                                    <InputLabel value="Tanggal Surat" />
                                    <input
                                        v-model="form.tanggal_surat"
                                        type="date"
                                        :class="INPUT_CLASS"
                                    />
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.tanggal_surat"
                                    />
                                </div>

                                <div class="sm:col-span-2">
                                    <InputLabel value="Perihal" />
                                    <input
                                        v-model="form.perihal"
                                        type="text"
                                        placeholder="cth. Undangan Rapat ..."
                                        :class="INPUT_CLASS"
                                    />
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.perihal"
                                    />
                                </div>

                                <div class="sm:col-span-2">
                                    <InputLabel value="Penerima / Tujuan (Bisa Banyak / Lebih dari 1 Penerima)" />
                                    <textarea
                                        v-model="form.tujuan"
                                        rows="3"
                                        placeholder="Tuliskan penerima surat. Jika lebih dari 1, tekan Enter untuk menambah baris (atau beri penomoran).&#10;Contoh:&#10;1. Ketua RT/RW&#10;2. KPM PKH/BPNT&#10;3. Camat Cigalontang"
                                        :class="INPUT_CLASS"
                                    />
                                    <p class="mt-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                        Tips: Tekan Enter untuk menambah penerima berikutnya. Sistem akan menata daftar penerima secara rapi di surat.
                                    </p>
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.tujuan"
                                    />
                                </div>
                            </div>
                        </section>

                        <!-- Isi Surat -->
                        <section :class="SECTION_CLASS">
                            <h3 :class="SECTION_TITLE_CLASS">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.8"
                                    stroke="currentColor"
                                    class="h-5 w-5 text-indigo-500"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"
                                    />
                                </svg>
                                Isi Surat
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <InputLabel value="Kalimat Pembuka" />
                                    <div class="mt-1 flex items-start gap-2">
                                        <select
                                            class="w-48 shrink-0 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                            @change="applyTemplate"
                                        >
                                            <option value="">
                                                Template standar...
                                            </option>
                                            <option
                                                v-for="tpl in templates"
                                                :key="tpl.label"
                                                :value="tpl.text"
                                            >
                                                {{ tpl.label }}
                                            </option>
                                        </select>
                                        <textarea
                                            v-model="form.pembuka"
                                            rows="2"
                                            :class="INPUT_CLASS"
                                        />
                                    </div>
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.pembuka"
                                    />
                                </div>

                                <div>
                                    <InputLabel value="Isi Surat Utama" />
                                    <textarea
                                        v-model="form.isi_surat"
                                        rows="6"
                                        :class="INPUT_CLASS"
                                        placeholder="Tuliskan isi surat di sini. Baris baru akan dipertahankan pada tata letak surat."
                                    />
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.isi_surat"
                                    />
                                </div>

                                <div>
                                    <InputLabel value="Kalimat Penutup" />
                                    <textarea
                                        v-model="form.penutup"
                                        rows="2"
                                        :class="INPUT_CLASS"
                                        placeholder="Demikian surat ini dibuat..."
                                    />
                                    <InputError
                                        class="mt-1"
                                        :message="form.errors.penutup"
                                    />
                                </div>
                            </div>
                        </section>

                        <!-- Tanda Tangan Dinamis (Multi Signer) -->
                        <section :class="SECTION_CLASS">
                            <div class="mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 pb-3 dark:border-gray-700">
                                <h3 class="flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.8"
                                        stroke="currentColor"
                                        class="h-5 w-5 text-indigo-500"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"
                                        />
                                    </svg>
                                    Penandatangan Surat (Bisa Banyak / Mengetahui)
                                </h3>
                                <button
                                    type="button"
                                    @click="addSigner"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm transition hover:from-emerald-500 hover:to-teal-500 active:scale-95 ring-1 ring-emerald-400/30"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Tambah Penandatangan
                                </button>
                            </div>

                            <div class="space-y-6">
                                <div
                                    v-for="(signer, index) in form.penandatangan_list"
                                    :key="index"
                                    class="relative rounded-2xl border border-slate-200/90 bg-slate-50/70 p-4 sm:p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900/50"
                                >
                                    <div class="mb-4 flex items-center justify-between border-b border-slate-200/80 pb-3 dark:border-slate-800">
                                        <span class="inline-flex items-center gap-2 text-xs font-black text-slate-800 dark:text-slate-200">
                                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px] dark:bg-emerald-950 dark:text-emerald-300 ring-1 ring-emerald-500/30">
                                                {{ index + 1 }}
                                            </span>
                                            Penandatangan #{{ index + 1 }}
                                        </span>
                                        <button
                                            v-if="form.penandatangan_list.length > 1"
                                            type="button"
                                            @click="removeSigner(index)"
                                            class="rounded px-2 py-1 text-xs font-bold text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950"
                                        >
                                            Hapus Penandatangan Ini
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div>
                                            <InputLabel value="Posisi / Peran Layout" />
                                            <select
                                                v-model="signer.peran"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-xs font-semibold"
                                            >
                                                <option value="kanan">Kanan (Utama / Kanan)</option>
                                                <option value="kiri">Kiri (Pendamping / Kiri)</option>
                                                <option value="mengetahui">Mengetahui (Tengah Bawah)</option>
                                            </select>
                                        </div>

                                        <div>
                                            <InputLabel value="Label Tambahan (misal: Mengetahui,)" :optional="true" />
                                            <input
                                                v-model="signer.label_mengetahui"
                                                type="text"
                                                placeholder="cth. Mengetahui, atau Menyetujui,"
                                                :class="INPUT_CLASS"
                                            />
                                        </div>

                                        <div>
                                            <InputLabel value="Jabatan Penandatangan" />
                                            <input
                                                v-model="signer.jabatan"
                                                type="text"
                                                placeholder="cth. Kepala Desa / Ketua / Sekretaris"
                                                :class="INPUT_CLASS"
                                            />
                                        </div>

                                        <div>
                                            <InputLabel value="Nama Jelas" />
                                            <input
                                                v-model="signer.nama"
                                                type="text"
                                                placeholder="Nama Lengkap & Gelar"
                                                :class="INPUT_CLASS"
                                            />
                                        </div>

                                        <div class="sm:col-span-2">
                                            <InputLabel value="NIP / NIAPD" :optional="true" />
                                            <input
                                                v-model="signer.nip_niapd"
                                                type="text"
                                                placeholder="cth. 19720315..."
                                                :class="INPUT_CLASS"
                                            />
                                        </div>

                                        <div class="sm:col-span-2">
                                            <InputLabel value="Tanda Tangan Digital (Gambar Langsung / Unggah Foto)" />
                                            <div class="mt-1">
                                                <SignaturePad
                                                    :height="150"
                                                    @change="(file, dataUrl) => onSignerSignatureChange(index, file, dataUrl)"
                                                />
                                            </div>
                                            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                                <span>Atau unggah file gambar tanda tangan:</span>
                                                <input
                                                    type="file"
                                                    accept="image/png,image/jpeg"
                                                    @change="onSignerImageUpload(index, $event)"
                                                    class="text-xs text-gray-500 file:rounded file:border-0 file:bg-slate-200 file:px-2 file:py-1 file:text-xs file:font-semibold"
                                                />
                                            </div>
                                        </div>

                                        <div class="sm:col-span-2 mt-2 border-t border-slate-200/60 pt-3 dark:border-slate-700/60">
                                            <InputLabel value="Gambar Stempel Khusus Penandatangan Ini (Opsional - PNG Transparan)" :optional="true" />
                                            <div class="mt-1.5 flex flex-wrap items-center gap-3">
                                                <input
                                                    type="file"
                                                    accept="image/png,image/jpeg"
                                                    @change="onSignerStempelUpload(index, $event)"
                                                    class="block w-full text-xs text-slate-500 file:mr-3 file:rounded-md file:border-0 file:bg-slate-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white dark:file:bg-slate-200 dark:file:text-slate-800"
                                                />
                                                <img
                                                    v-if="signer.stempel"
                                                    :src="signer.stempel"
                                                    alt="Stempel Penandatangan"
                                                    class="h-12 w-12 object-contain rounded border border-dashed border-slate-300 bg-white p-1"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 border-t border-slate-200 pt-4 dark:border-slate-700">
                                <InputLabel value="Gambar Background / Watermark Surat (Opsional - PNG Transparan)" :optional="true" />
                                <input
                                    type="file"
                                    accept="image/png,image/jpeg"
                                    @change="onImageUpload('stempel', $event)"
                                    class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-gray-800 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white dark:text-gray-400 dark:file:bg-gray-200 dark:file:text-gray-800"
                                />
                                <p class="mt-1.5 text-xs text-slate-500">
                                    Gambar logo/watermark ini akan ditampilkan halus di tengah latar belakang halaman surat (opacity rendah).
                                </p>
                                <img
                                    v-if="filePreviewUrl('stempel')"
                                    :src="filePreviewUrl('stempel')"
                                    alt="Watermark Background"
                                    class="mt-2 h-16 object-contain opacity-40 border border-dashed border-slate-300 rounded p-1 bg-white"
                                />
                                <InputError
                                    class="mt-1"
                                    :message="form.errors.stempel"
                                />
                            </div>
                        </section>

                        <div class="flex justify-end">
                            <PrimaryButton
                                :disabled="form.processing"
                                :class="{ 'opacity-25': form.processing }"
                                @click="submit"
                            >
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Surat' }}
                            </PrimaryButton>
                        </div>
                    </div>

                    <!-- ===================== LIVE PREVIEW ===================== -->
                    <div
                        id="live-preview"
                        class="scroll-mt-20 xl:sticky xl:top-6 print:col-span-1"
                    >
                        <div
                            class="mb-4 flex flex-wrap items-center justify-between gap-3 print:hidden"
                        >
                            <div class="flex flex-wrap items-center gap-2">
                                <h3
                                    class="text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-200"
                                >
                                    Live Preview
                                </h3>
                                <select
                                    v-model="paper"
                                    class="rounded-lg border-slate-300 py-1 text-xs font-bold text-slate-800 focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200"
                                >
                                    <option value="a4">A4 (210×297 mm)</option>
                                    <option value="f4">F4 / Folio (210×330 mm)</option>
                                </select>
                                <button
                                    type="button"
                                    @click="isCompact = !isCompact"
                                    :class="[
                                        isCompact
                                            ? 'bg-emerald-100 border-emerald-300 text-emerald-800 dark:bg-emerald-950 dark:border-emerald-700 dark:text-emerald-300'
                                            : 'bg-white border-slate-300 text-slate-700 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-300',
                                        'inline-flex items-center gap-1 rounded-lg border px-2.5 py-1 text-xs font-bold transition shadow-sm'
                                    ]"
                                >
                                    {{ isCompact ? '1 Halaman Pas (Aktif)' : 'Mode 1 Halaman Pas' }}
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    type="button"
                                    @click="showPreview = !showPreview"
                                    class="inline-flex min-h-[2.25rem] items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 md:hidden dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                >
                                    {{ showPreview ? 'Sembunyikan' : 'Lihat Preview' }}
                                </button>
                                <button
                                    type="button"
                                    @click="printPreview"
                                    class="inline-flex items-center gap-2 rounded-lg border border-transparent bg-slate-900 px-4 py-2 text-xs font-bold uppercase tracking-wider text-white transition hover:bg-slate-800 focus:outline-none dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white shadow-sm"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.8"
                                        stroke="currentColor"
                                        class="h-4 w-4"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z"
                                        />
                                    </svg>
                                    Cetak PDF
                                </button>
                            </div>
                        </div>

                        <div
                            :class="showPreview ? 'block' : 'hidden md:block print:block'"
                            class="rounded-xl bg-slate-200/70 p-3 sm:p-5 print:block dark:bg-slate-900/60 shadow-inner"
                        >
                            <LetterPreview
                                :letter="previewLetter"
                                :paper="paper"
                                :compact="isCompact"
                                scaled
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol akses cepat preview di layar kecil (Android) -->
        <button
            type="button"
            @click="
                showPreview = true;
                scrollToPreview();
            "
            class="fixed bottom-4 right-4 z-40 inline-flex min-h-[2.75rem] items-center gap-2 rounded-full border border-transparent bg-indigo-600 px-5 text-xs font-bold uppercase tracking-wide text-white shadow-lg transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 md:hidden print:hidden"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.8"
                stroke="currentColor"
                class="h-4 w-4"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
                />
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                />
            </svg>
            Preview
        </button>
    </AuthenticatedLayout>
</template>
