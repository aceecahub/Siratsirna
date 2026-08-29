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
const SECTION_CLASS = 'rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-sm dark:border-slate-800/80 dark:bg-slate-900/90';
const SECTION_TITLE = 'flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 px-5 py-3.5 text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wide';

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
});

const initialSigners = (props.letter.penandatangan_list && props.letter.penandatangan_list.length > 0)
    ? props.letter.penandatangan_list.map(s => ({
        peran: s.peran || 'kanan',
        label_mengetahui: s.label_mengetahui || '',
        jabatan: s.jabatan || '',
        nama: s.nama || '',
        nip_niapd: s.nip_niapd || '',
        tanda_tangan: s.tanda_tangan || null,
        stempel: s.stempel || null,
        _ttdFile: null,
        _stempelFile: null,
    }))
    : [
        { peran: 'kiri',  label_mengetahui: '', jabatan: '', nama: '', nip_niapd: '', tanda_tangan: null, stempel: null, _ttdFile: null, _stempelFile: null },
        { peran: 'kanan', label_mengetahui: '', jabatan: 'Kepala Desa', nama: '', nip_niapd: '', tanda_tangan: null, stempel: null, _ttdFile: null, _stempelFile: null },
    ];

const signers = reactive(initialSigners);

function addSigner() {
    signers.push({ peran: 'kanan', label_mengetahui: '', jabatan: '', nama: '', nip_niapd: '', tanda_tangan: null, stempel: null, _ttdFile: null, _stempelFile: null });
}
function removeSigner(idx) {
    signers.splice(idx, 1);
}

const uploads = reactive({
    logo_kiri: props.letter.logo_kiri || '/images/logo-tasikmalaya.png',
    logo_kanan: props.letter.logo_kanan || null,
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

async function onSignerTtd(idx, event) {
    const file = event.target.files[0];
    if (!file) return;
    signers[idx]._ttdFile     = file;
    signers[idx].tanda_tangan = await readFile(file);
}

async function onSignerStempel(idx, event) {
    const file = event.target.files[0];
    if (!file) return;
    signers[idx]._stempelFile = file;
    signers[idx].stempel      = await readFile(file);
}

const paper = ref('f4');
const compact = ref(false);

const previewLetter = computed(() => ({
    kabupaten: form.kabupaten, kecamatan: form.kecamatan, nama_desa: form.nama_desa,
    alamat: form.alamat, kode_pos: form.kode_pos, kontak: form.kontak, email_desa: form.email_desa,
    logo_kiri: uploads.logo_kiri, logo_kanan: uploads.logo_kanan,
    nomor_surat: form.nomor_surat, tanggal_surat: form.tanggal_surat,
    nama_warga: form.nama_warga, tempat_lahir: form.tempat_lahir,
    tanggal_lahir: form.tanggal_lahir, pekerjaan: form.pekerjaan,
    alamat_warga: form.alamat_warga, rt: form.rt, rw: form.rw, dusun: form.dusun,
    keterangan: form.keterangan, stempel: uploads.stempel,
    penandatangan_list: signers.map(s => ({
        peran:            s.peran,
        label_mengetahui: s.label_mengetahui,
        jabatan:          s.jabatan,
        nama:             s.nama,
        nip_niapd:        s.nip_niapd,
        tanda_tangan:     s.tanda_tangan,
        stempel:          s.stempel,
    })),
}));

function submit() {
    form.transform((data) => {
        const signerData = signers.map(s => ({
            peran:            s.peran,
            label_mengetahui: s.label_mengetahui,
            jabatan:          s.jabatan,
            nama:             s.nama,
            nip_niapd:        s.nip_niapd,
        }));

        const transformed = {
            ...data,
            penandatangan_list: JSON.stringify(signerData),
        };

        signers.forEach((s, idx) => {
            if (s._ttdFile)     transformed[`tanda_tangan_signer_${idx}`] = s._ttdFile;
            if (s._stempelFile) transformed[`stempel_signer_${idx}`] = s._stempelFile;
        });

        return transformed;
    }).post(route('surat-tidak-mampu.update', props.letter.id), {
        preserveScroll: true,
        forceFormData: true,
    });
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

                        <!-- Penandatangan Dinamis -->
                        <section :class="SECTION_CLASS">
                            <div class="mb-5 flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3 dark:border-slate-800">
                                <h3 :class="SECTION_TITLE" class="!mb-0 !border-0 !pb-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5 text-rose-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                    </svg>
                                    Penandatangan Surat
                                </h3>
                                <button type="button" @click="addSigner"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-rose-600 to-pink-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition hover:from-rose-500 active:scale-95">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Tambah Penandatangan
                                </button>
                            </div>

                            <div class="space-y-5">
                                <div v-for="(signer, idx) in signers" :key="idx"
                                    class="relative rounded-2xl border border-slate-200/90 bg-slate-50/70 p-4 sm:p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">

                                    <div class="mb-4 flex items-center justify-between border-b border-slate-200/80 pb-3 dark:border-slate-800">
                                        <span class="inline-flex items-center gap-2 text-xs font-black text-slate-800 dark:text-slate-200">
                                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-rose-100 text-rose-800 font-extrabold text-[10px] dark:bg-rose-950 dark:text-rose-300 ring-1 ring-rose-500/30">
                                                {{ idx + 1 }}
                                            </span>
                                            Penandatangan #{{ idx + 1 }}
                                        </span>
                                        <button v-if="signers.length > 1" type="button" @click="removeSigner(idx)"
                                            class="rounded px-2 py-1 text-xs font-bold text-red-600 hover:bg-red-50 dark:text-red-400">
                                            Hapus
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <!-- Posisi -->
                                        <div>
                                            <InputLabel value="Posisi / Kolom" />
                                            <select v-model="signer.peran" :class="INPUT_CLASS">
                                                <option value="kiri">Kiri</option>
                                                <option value="kanan">Kanan (Utama)</option>
                                                <option value="mengetahui">Tengah / Mengetahui</option>
                                            </select>
                                        </div>
                                        <!-- Label atas -->
                                        <div>
                                            <InputLabel value="Label Atas (cth: Mengetahui,)" :optional="true" />
                                            <input v-model="signer.label_mengetahui" type="text"
                                                placeholder="cth. Mengetahui," :class="INPUT_CLASS" />
                                        </div>
                                        <!-- Jabatan -->
                                        <div>
                                            <InputLabel value="Jabatan" />
                                            <input v-model="signer.jabatan" type="text"
                                                placeholder="cth. Kepala Desa / Pendamping PKH" :class="INPUT_CLASS" />
                                        </div>
                                        <!-- Nama -->
                                        <div>
                                            <InputLabel value="Nama Jelas" />
                                            <input v-model="signer.nama" type="text"
                                                placeholder="Nama Lengkap" :class="INPUT_CLASS" />
                                        </div>
                                        <!-- NIP -->
                                        <div class="sm:col-span-2">
                                            <InputLabel value="NIP / NIAPD" :optional="true" />
                                            <input v-model="signer.nip_niapd" type="text"
                                                placeholder="cth. 19720315..." :class="INPUT_CLASS" />
                                        </div>
                                        <!-- TTD Digital Pad / Gambar -->
                                        <div class="sm:col-span-2">
                                            <InputLabel value="Tanda Tangan Digital (Gambar Langsung / Unggah Foto)" :optional="true" />
                                            <div class="mt-1">
                                                <SignaturePad
                                                    :height="140"
                                                    @change="(file, dataUrl) => { signer._ttdFile = file; signer.tanda_tangan = dataUrl; }"
                                                />
                                            </div>
                                            <div v-if="signer.tanda_tangan" class="mt-2 flex items-center gap-3 bg-white p-2 rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-700">
                                                <span class="text-[11px] font-bold text-slate-500">TTD Saat Ini:</span>
                                                <img :src="signer.tanda_tangan" class="h-10 object-contain" alt="Preview TTD" />
                                            </div>
                                            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                                <span>Atau unggah berkas gambar TTD:</span>
                                                <input type="file" accept="image/*" @change="onSignerTtd(idx, $event)"
                                                    class="text-xs text-gray-500 file:rounded file:border-0 file:bg-slate-200 file:px-2 file:py-1 file:text-xs file:font-semibold" />
                                            </div>
                                        </div>
                                        <!-- Stempel per signer -->
                                        <div class="sm:col-span-2">
                                            <InputLabel value="Upload Stempel Khusus Penandatangan Ini" :optional="true" />
                                            <input type="file" accept="image/*" @change="onSignerStempel(idx, $event)"
                                                class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-slate-700 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white" />
                                            <img v-if="signer.stempel" :src="signer.stempel"
                                                class="mt-2 h-10 object-contain opacity-60" alt="Stempel" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-3 pt-2">
                            <button type="button" @click="submit" :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-xl bg-rose-600 px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-rose-500 transition cursor-pointer disabled:opacity-50">
                                <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
                            </button>
                            <Link :href="route('surat-tidak-mampu.show', props.letter.id)" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
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
                                class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-rose-600 to-pink-600 px-3 py-1.5 text-xs font-bold text-white shadow-md transition hover:from-rose-500 active:scale-95">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" />
                                </svg>
                                Cetak Preview
                            </button>
                        </div>
                        <PovertyLetterPreview :letter="previewLetter" :paper="paper" :compact="compact" :scaled="true" />
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

