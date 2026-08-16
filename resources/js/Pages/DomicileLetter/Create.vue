<script setup>
import { computed, reactive, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SignaturePad from '@/Components/SignaturePad.vue';
import DomicileLetterPreview from '@/Components/DomicileLetterPreview.vue';
import { printSheet } from '@/lib/print';

// ─── Input styling constants ───────────────────────────────────────────────
const INPUT_CLASS = 'mt-1 block w-full rounded-lg border-slate-200 bg-white text-xs font-medium shadow-sm transition focus:border-primary focus:ring-2 focus:ring-primary/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-primary';
const FIELD_FILE_CLASS = 'mt-1 block w-full cursor-pointer rounded-lg border border-dashed border-slate-300 bg-slate-50 p-2 text-xs text-slate-500 file:mr-3 file:rounded-md file:border-0 file:bg-primary file:px-3 file:py-1.5 file:text-[11px] file:font-semibold file:text-white hover:file:bg-primary/90 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400';

// ─── Form ──────────────────────────────────────────────────────────────────
const form = useForm({
    kabupaten:  'Tasikmalaya', kecamatan:  'Cigalontang', nama_desa:  'Sirnaputra', alamat: '',
    kode_pos: '', kontak: '', email_desa: '', logo_kiri: null, logo_kanan: null,
    nomor_surat: '', tanggal_surat: new Date().toISOString().slice(0, 10),
    nik: '', nama_warga: '', tempat_lahir: '', tanggal_lahir: '',
    jenis_kelamin: 'Laki-laki', warga_negara: 'Indonesia', agama: 'Islam',
    pekerjaan: '', alamat_warga: '', rt: '', rw: '', dusun: '',
    keterangan_domisili: '', catatan_ektp: '',
    jabatan_penandatangan: 'Kepala Desa', nama_penandatangan: '', nip_niapd: '',
    tanda_tangan: null, stempel: null,
});

const uploads = reactive({ logo_kiri: '/images/logo-tasikmalaya.png', logo_kanan: null, tanda_tangan: null, stempel: null });
const showPreview = ref(true);
const paper = ref('f4');
const isCompact = ref(false);

function readAsDataUrl(file) {
    return new Promise(resolve => {
        const r = new FileReader();
        r.onload = () => resolve(r.result);
        r.onerror = () => resolve(null);
        r.readAsDataURL(file);
    });
}

async function onImageUpload(field, event) {
    const file = event.target.files[0];
    if (!file) { form[field] = null; uploads[field] = null; return; }
    if (!file.type.startsWith('image/')) { window.alert('File harus berupa gambar.'); event.target.value = ''; return; }
    if (file.size > 5 * 1024 * 1024) { window.alert('Ukuran file maksimal 5MB.'); event.target.value = ''; return; }
    form[field] = file;
    uploads[field] = await readAsDataUrl(file);
}

const previewLetter = computed(() => ({
    kabupaten: form.kabupaten, kecamatan: form.kecamatan, nama_desa: form.nama_desa,
    alamat: form.alamat, kode_pos: form.kode_pos, kontak: form.kontak, email_desa: form.email_desa,
    logo_kiri: uploads.logo_kiri, logo_kanan: uploads.logo_kanan,
    nomor_surat: form.nomor_surat, tanggal_surat: form.tanggal_surat,
    nik: form.nik, nama_warga: form.nama_warga, tempat_lahir: form.tempat_lahir,
    tanggal_lahir: form.tanggal_lahir, jenis_kelamin: form.jenis_kelamin,
    warga_negara: form.warga_negara, agama: form.agama, pekerjaan: form.pekerjaan,
    alamat_warga: form.alamat_warga, rt: form.rt, rw: form.rw, dusun: form.dusun,
    keterangan_domisili: form.keterangan_domisili, catatan_ektp: form.catatan_ektp,
    jabatan_penandatangan: form.jabatan_penandatangan, nama_penandatangan: form.nama_penandatangan,
    nip_niapd: form.nip_niapd, tanda_tangan: uploads.tanda_tangan, stempel: uploads.stempel,
}));

function submit() { form.post(route('surat-keterangan-domisili.store'), { preserveScroll: true }); }
async function printPreview() { await printSheet(); }
function scrollToPreview() { document.getElementById('live-preview')?.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
</script>

<template>
    <Head title="Buat Surat Keterangan Domisili" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                        <Link :href="route('surat-keterangan-domisili.index')" class="hover:text-primary transition">Domisili</Link>
                        <span class="text-slate-300 dark:text-slate-600">/</span>
                        <span class="text-slate-700 dark:text-slate-300 font-medium">Buat Surat</span>
                    </div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Buat Surat Keterangan Domisili</h1>
                </div>
                <button type="button" @click="scrollToPreview"
                    class="xl:hidden inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 transition">
                    Preview ↓
                </button>
            </div>
        </template>

        <div class="py-8 print:p-0">
            <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8 print:max-w-none print:px-0">
                <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-2 print:grid-cols-1">

                    <!-- ═══════ FORM ═══════ -->
                    <div class="space-y-4 print:hidden">

                        <!-- 1. Kop Surat -->
                        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
                            <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 px-5 py-3.5">
                                <div class="flex h-6 w-6 items-center justify-center rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-bold">1</div>
                                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wide">Kop Surat</h3>
                            </div>
                            <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <InputLabel value="Logo Kabupaten / Pemda (Kiri)" />
                                    <input type="file" accept="image/*" @change="onImageUpload('logo_kiri', $event)" :class="FIELD_FILE_CLASS" />
                                    <img v-if="uploads.logo_kiri" :src="uploads.logo_kiri" alt="Logo Kiri" class="mt-2 h-12 w-auto object-contain rounded border border-slate-200 dark:border-slate-700 p-1 bg-white" />
                                    <InputError class="mt-1" :message="form.errors.logo_kiri" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Logo Desa (Kanan)" />
                                    <input type="file" accept="image/*" @change="onImageUpload('logo_kanan', $event)" :class="FIELD_FILE_CLASS" />
                                    <img v-if="uploads.logo_kanan" :src="uploads.logo_kanan" alt="Logo Kanan" class="mt-2 h-12 w-auto object-contain rounded border border-slate-200 dark:border-slate-700 p-1 bg-white" />
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
                                    <InputLabel value="Kode Pos" />
                                    <input v-model="form.kode_pos" type="text" placeholder="cth. 46463" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.kode_pos" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Alamat Kantor Desa" />
                                    <textarea v-model="form.alamat" rows="2" placeholder="Jl. Pacihayan No.16 Sirnaputra" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.alamat" />
                                </div>
                                <div>
                                    <InputLabel value="Kontak / Telp" />
                                    <input v-model="form.kontak" type="text" placeholder="0812-xxxx-xxxx" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.kontak" />
                                </div>
                                <div>
                                    <InputLabel value="Email Desa" />
                                    <input v-model="form.email_desa" type="email" placeholder="desa@gmail.com" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.email_desa" />
                                </div>
                            </div>
                        </div>

                        <!-- 2. Nomor & Tanggal -->
                        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
                            <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 px-5 py-3.5">
                                <div class="flex h-6 w-6 items-center justify-center rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-bold">2</div>
                                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wide">Nomor & Tanggal Surat</h3>
                            </div>
                            <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
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
                        </div>

                        <!-- 3. Data Warga -->
                        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
                            <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 px-5 py-3.5">
                                <div class="flex h-6 w-6 items-center justify-center rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-bold">3</div>
                                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wide">Data Warga</h3>
                            </div>
                            <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <InputLabel value="NIK E-KTP" />
                                    <input v-model="form.nik" type="text" placeholder="3206276206260001" :class="INPUT_CLASS" maxlength="16" />
                                    <InputError class="mt-1" :message="form.errors.nik" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Nama Lengkap Warga" />
                                    <input v-model="form.nama_warga" type="text" placeholder="WINA WINARTI" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.nama_warga" />
                                </div>
                                <div>
                                    <InputLabel value="Tempat Lahir" />
                                    <input v-model="form.tempat_lahir" type="text" placeholder="Tasikmalaya" :class="INPUT_CLASS" />
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
                                    <input v-model="form.pekerjaan" type="text" placeholder="Mengurus Rumah Tangga" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.pekerjaan" />
                                </div>
                                <div>
                                    <InputLabel value="Warga Negara" />
                                    <input v-model="form.warga_negara" type="text" placeholder="Indonesia" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.warga_negara" />
                                </div>
                                <div>
                                    <InputLabel value="Dusun / Kampung" />
                                    <input v-model="form.dusun" type="text" placeholder="Cilimus" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.dusun" />
                                </div>
                                <div>
                                    <InputLabel value="RT" />
                                    <input v-model="form.rt" type="text" placeholder="017" :class="INPUT_CLASS" maxlength="5" />
                                    <InputError class="mt-1" :message="form.errors.rt" />
                                </div>
                                <div>
                                    <InputLabel value="RW" />
                                    <input v-model="form.rw" type="text" placeholder="006" :class="INPUT_CLASS" maxlength="5" />
                                    <InputError class="mt-1" :message="form.errors.rw" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Alamat Lengkap (Opsional)" />
                                    <textarea v-model="form.alamat_warga" rows="2" placeholder="Kosongkan untuk dibuat otomatis dari Kp/RT/RW di atas" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.alamat_warga" />
                                </div>
                            </div>
                        </div>

                        <!-- 4. Keterangan -->
                        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
                            <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 px-5 py-3.5">
                                <div class="flex h-6 w-6 items-center justify-center rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-bold">4</div>
                                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wide">Keterangan Tambahan</h3>
                                <span class="ml-auto text-[10px] text-slate-400">(Opsional)</span>
                            </div>
                            <div class="p-5 space-y-4">
                                <div>
                                    <InputLabel value="Paragraf Keterangan Domisili" />
                                    <textarea v-model="form.keterangan_domisili" rows="3" placeholder="Kosongkan untuk menggunakan teks default..." :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.keterangan_domisili" />
                                </div>
                                <div>
                                    <InputLabel value="Catatan E-KTP" />
                                    <textarea v-model="form.catatan_ektp" rows="2" placeholder="Kosongkan untuk tidak ada catatan E-KTP" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.catatan_ektp" />
                                </div>
                            </div>
                        </div>

                        <!-- 5. Tanda Tangan -->
                        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
                            <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-800 px-5 py-3.5">
                                <div class="flex h-6 w-6 items-center justify-center rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-bold">5</div>
                                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wide">Tanda Tangan & Stempel</h3>
                            </div>
                            <div class="p-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <InputLabel value="Jabatan Penandatangan" />
                                    <input v-model="form.jabatan_penandatangan" type="text" placeholder="Kepala Desa" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.jabatan_penandatangan" />
                                </div>
                                <div>
                                    <InputLabel value="NIP / NIAPD" />
                                    <input v-model="form.nip_niapd" type="text" placeholder="19800101 200604 1 001" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.nip_niapd" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Nama Penandatangan" />
                                    <input v-model="form.nama_penandatangan" type="text" placeholder="AHMAD FAUZI" :class="INPUT_CLASS" />
                                    <InputError class="mt-1" :message="form.errors.nama_penandatangan" />
                                </div>
                                <div>
                                    <InputLabel value="Upload Tanda Tangan (PNG)" />
                                    <input type="file" accept="image/*" @change="onImageUpload('tanda_tangan', $event)" :class="FIELD_FILE_CLASS" />
                                    <img v-if="uploads.tanda_tangan" :src="uploads.tanda_tangan" alt="TTD" class="mt-2 h-12 w-auto object-contain rounded border border-slate-200 dark:border-slate-700 p-1 bg-white" />
                                    <InputError class="mt-1" :message="form.errors.tanda_tangan" />
                                </div>
                                <div>
                                    <InputLabel value="Upload Stempel Desa" />
                                    <input type="file" accept="image/*" @change="onImageUpload('stempel', $event)" :class="FIELD_FILE_CLASS" />
                                    <img v-if="uploads.stempel" :src="uploads.stempel" alt="Stempel" class="mt-2 h-12 w-auto object-contain rounded border border-slate-200 dark:border-slate-700 p-1 bg-white" />
                                    <InputError class="mt-1" :message="form.errors.stempel" />
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="flex flex-wrap items-center gap-3 pt-1 pb-6">
                            <button type="button" @click="submit" :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-2.5 text-xs font-semibold text-white shadow-sm shadow-primary/25 transition hover:bg-primary/90 active:scale-95 disabled:opacity-60 focus:outline-none focus:ring-2 focus:ring-primary/40">
                                <svg v-if="!form.processing" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" /></svg>
                                <svg v-else class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" /></svg>
                                {{ form.processing ? 'Menyimpan...' : 'Simpan & Cetak Surat' }}
                            </button>
                            <button type="button" @click="scrollToPreview"
                                class="xl:hidden inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-xs font-medium text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 transition">
                                Lihat Preview ↓
                            </button>
                            <Link :href="route('surat-keterangan-domisili.index')"
                                class="text-xs font-medium text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 transition">
                                Batal
                            </Link>
                        </div>
                    </div>

                    <!-- ═══════ PREVIEW ═══════ -->
                    <div id="live-preview" class="sticky top-4 space-y-3">
                        <!-- Preview Controls -->
                        <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm print:hidden dark:border-slate-800 dark:bg-slate-900 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2">
                                    <label class="text-[11px] font-semibold text-slate-600 dark:text-slate-300">Kertas:</label>
                                    <select v-model="paper"
                                        class="rounded-md border-slate-200 py-1 text-[11px] font-semibold text-slate-800 focus:border-primary focus:ring-primary dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                        <option value="f4">F4 / Folio</option>
                                        <option value="a4">A4</option>
                                    </select>
                                </div>
                                <button type="button" @click="isCompact = !isCompact"
                                    :class="['inline-flex items-center rounded-md px-2.5 py-1 text-[11px] font-semibold border transition', isCompact ? 'bg-primary/10 border-primary/30 text-primary dark:bg-primary/20' : 'bg-white border-slate-200 text-slate-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400']">
                                    Ringkas
                                </button>
                            </div>
                            <button type="button" @click="printPreview"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" /></svg>
                                Cetak Preview
                            </button>
                        </div>

                        <!-- Preview Sheet -->
                        <div class="rounded-xl bg-slate-100 p-4 print:p-0 print:bg-transparent dark:bg-slate-800/50 shadow-inner overflow-auto">
                            <DomicileLetterPreview :letter="previewLetter" :paper="paper" :scaled="true" :compact="isCompact" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
