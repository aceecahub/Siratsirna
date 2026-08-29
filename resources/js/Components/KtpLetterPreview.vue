<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { formatTanggal } from '@/lib/format';

const props = defineProps({
    letter:  { type: Object, required: true },
    paper:   { type: String, default: 'f4', validator: (v) => ['a4', 'f4'].includes(v) },
    scaled:  { type: Boolean, default: false },
    compact: { type: Boolean, default: false },
});

const SERIF      = "'Times New Roman', Times, serif";
const SHEET_W_MM = 210;
const F4_H_MM    = 330;
const A4_H_MM    = 297;
const MM_TO_PX   = 96 / 25.4;

const heightMm      = computed(() => props.paper === 'a4' ? A4_H_MM : F4_H_MM);
const sheetWidthPx  = SHEET_W_MM * MM_TO_PX;
const sheetHeightPx = computed(() => heightMm.value * MM_TO_PX);
const scaledHeight  = computed(() => sheetHeightPx.value * scale.value);

const wrapper = ref(null);
const scale   = ref(1);
let observer  = null;

function measure() {
    if (!props.scaled || !wrapper.value) return;
    scale.value = Math.min(1, wrapper.value.clientWidth / sheetWidthPx);
}
onMounted(() => {
    if (!props.scaled || !wrapper.value) return;
    measure();
    observer = new ResizeObserver(measure);
    observer.observe(wrapper.value);
});
onBeforeUnmount(() => observer?.disconnect());

const l = computed(() => {
    const s = props.letter ?? {};
    return {
        kabupaten:  s.kabupaten  || 'Tasikmalaya',
        kecamatan:  s.kecamatan  || 'Cigalontang',
        nama_desa:  s.nama_desa  || 'Sirnaputra',
        alamat:     s.alamat     || '',
        kode_pos:   s.kode_pos   || '',
        kontak:     s.kontak     || '',
        email_desa: s.email_desa || '',
        logo_kiri:  s.logo_kiri  || '/images/logo-tasikmalaya.png',
        logo_kanan: s.logo_kanan || null,

        nomor_surat:   s.nomor_surat   || '',
        tanggal_surat: s.tanggal_surat || '',

        nik:               s.nik               || '',
        nama_warga:        s.nama_warga        || '',
        tempat_lahir:      s.tempat_lahir      || '',
        tanggal_lahir:     s.tanggal_lahir     || '',
        warga_negara:      s.warga_negara      || 'Indonesia',
        jenis_kelamin:     s.jenis_kelamin     || 'Laki – Laki',
        agama:             s.agama             || 'Islam',
        status_perkawinan: s.status_perkawinan || 'Belum Kawin',
        pekerjaan:         s.pekerjaan         || '',
        alamat_warga:      s.alamat_warga      || '',
        rt:                s.rt                || '',
        rw:                s.rw                || '',
        dusun:             s.dusun             || '',

        keterangan_tambahan: s.keterangan_tambahan || '',
        kalimat_penutup:     s.kalimat_penutup     || '',

        label_atasan:          s.label_atasan          || '',
        jabatan_penandatangan: s.jabatan_penandatangan || '',
        nama_penandatangan:    s.nama_penandatangan    || '',
        nip_niapd:             s.nip_niapd             || '',
        tanda_tangan:          s.tanda_tangan          || null,
        stempel:               s.stempel               || null,
    };
});

// Format Kop
const kabFormatted = computed(() => {
    const r = (l.value.kabupaten || '').trim().toUpperCase();
    if (!r) return 'PEMERINTAH KABUPATEN TASIKMALAYA';
    if (r.startsWith('PEMERINTAH')) return r;
    if (r.startsWith('KABUPATEN')) return `PEMERINTAH ${r}`;
    return `PEMERINTAH KABUPATEN ${r}`;
});
const kecFormatted = computed(() => {
    const r = (l.value.kecamatan || '').trim().toUpperCase();
    return r.startsWith('KECAMATAN') ? r : `KECAMATAN ${r}`;
});
const desaFormatted = computed(() => {
    const r = (l.value.nama_desa || '').trim().toUpperCase();
    return r.startsWith('DESA') ? r : `DESA ${r}`;
});

const tanggalSuratFormatted = computed(() => formatTanggal(l.value.tanggal_surat));
const tanggalLahirFormatted = computed(() => formatTanggal(l.value.tanggal_lahir));

// Alamat warga formatted
const alamatWargaFormatted = computed(() => {
    if (l.value.alamat_warga) return l.value.alamat_warga;
    const parts = [];
    if (l.value.dusun) parts.push(`Kp. ${l.value.dusun}`);
    if (l.value.rt)    parts.push(`RT ${String(l.value.rt).padStart(3,'0')}`);
    if (l.value.rw)    parts.push(`RW ${String(l.value.rw).padStart(3,'0')}`);
    return parts.join(' ');
});

const alamatLengkapFormatted = computed(() => {
    const desa = (l.value.nama_desa || '').trim();
    const desaStr = desa ? (desa.toUpperCase().startsWith('DESA') ? desa : `Desa ${desa}`) : '';
    const kec = (l.value.kecamatan || '').replace(/^KECAMATAN\s*/i, '');
    const kecStr = kec ? `Kec. ${kec}` : '';
    const kab = (l.value.kabupaten || '').replace(/^PEMERINTAH.*?KABUPATEN\s*/i, '');
    const kabStr = kab ? `Kab. ${kab}` : '';
    return [desaStr, kecStr, kabStr].filter(Boolean).join(' ');
});

const keteranganTambahan = computed(() =>
    l.value.keterangan_tambahan ||
    'Orang tersebut diatas adalah penduduk/Warga Desa Kami dan bertempat tinggal di alamat tersebut diatas.'
);

const kalimatPenutup = computed(() =>
    l.value.kalimat_penutup ||
    'Demikian surat pengantar Pencetakan e-KTP ini dibuat, agar dipergunakan sebagaimana mestinya.'
);
</script>

<template>
    <div ref="wrapper" class="letter-scale-wrap mx-auto relative" :style="{ height: scaled ? `${scaledHeight}px` : 'auto',
            width: scaled ? `${scaledWidth}px` : '100%', }">
        <div
            class="print-sheet relative bg-white text-black overflow-hidden"
            :class="[
                props.paper === 'a4' ? 'paper-a4' : 'paper-f4',
                { 'compact-mode': props.compact }
            ]"
            :style="{
                width: `${SHEET_W_MM}mm`,
                minHeight: `${heightMm}mm`,
                maxHeight: `${heightMm}mm`,
                fontFamily: SERIF,
                transform: scaled ? `scale(${scale})` : 'none',
                transformOrigin: 'top left',
                padding: compact ? '15mm 15mm 12mm 20mm' : '20mm 18mm 18mm 22mm',
                boxShadow: '0 10px 30px -12px rgba(0,0,0,0.35)',
                boxSizing: 'border-box',
            }"
        >
            <!-- Watermark -->
            <div v-if="l.stempel"
                class="pointer-events-none absolute inset-0 z-0 flex items-center justify-center overflow-hidden">
                <img :src="l.stempel" class="watermark-img h-[110mm] w-[110mm] object-contain"
                    style="opacity: 0.07 !important;" alt="" />
            </div>

            <div class="relative z-10 flex h-full flex-col">

                <!-- ── KOP SURAT ───────────────────────────────── -->
                <div class="shrink-0">
                    <div class="flex items-center">
                        <div class="w-[26mm] shrink-0 flex justify-center">
                            <img v-if="l.logo_kiri" :src="l.logo_kiri" class="h-[24mm] w-full object-contain" alt="" />
                            <div v-else class="flex h-[24mm] w-full items-center justify-center rounded border border-dashed border-gray-300 text-[7pt] text-gray-400 print:hidden leading-tight text-center">
                                Logo<br/>Kab
                            </div>
                        </div>

                        <div class="flex-1 text-center leading-snug"
                            :class="{ 'pr-[26mm]': !l.logo_kanan }">
                            <div class="font-bold uppercase tracking-tight"
                                :style="{ fontSize: compact ? '11pt' : '12pt' }">
                                {{ kabFormatted }}
                            </div>
                            <div class="font-bold uppercase tracking-tight mt-0.5"
                                :style="{ fontSize: compact ? '10pt' : '11pt' }">
                                {{ kecFormatted }}
                            </div>
                            <div class="font-bold uppercase tracking-wider mt-0.5"
                                :style="{ fontSize: compact ? '13pt' : '15pt' }">
                                {{ desaFormatted }}
                            </div>

                            <div class="mt-1 text-black font-normal" :style="{ fontSize: compact ? '10pt' : '11pt' }">
                                Kantor : JalanPacihayan No 16 Sirnaputra Cigalontang
                            </div>
                            <div class="text-black font-normal" :style="{ fontSize: compact ? '10pt' : '11pt' }">
                                E-mail : <span class="text-blue-700 underline font-semibold">sirnaputra@gmail.com</span>
                            </div>
                            <div class="text-black font-normal uppercase tracking-widest" :style="{ fontSize: compact ? '10pt' : '11pt' }">
                                TASIKMALAYA
                            </div>
                            <div class="text-black font-normal" :style="{ fontSize: compact ? '10pt' : '11pt' }">
                                KodePos 46463
                            </div>
                        </div>

                        <!-- Logo Kanan (Hanya jika diunggah) -->
                        <div v-if="l.logo_kanan" class="w-[26mm] shrink-0 flex justify-center">
                            <img :src="l.logo_kanan" class="h-[24mm] w-full object-contain" alt="" />
                        </div>
                    </div>
                    <div class="mt-2 border-b-[2.5px] border-black" />
                    <div class="mt-[1.5px] border-b border-black" />
                </div>

                <!-- ── ISI SURAT ───────────────────────────────── -->
                <div
                    class="flex flex-1 flex-col"
                    :style="{
                        paddingTop: compact ? '4mm' : '6mm',
                        fontSize:   compact ? '10pt' : '11pt',
                        lineHeight: compact ? '1.5'  : '1.65',
                    }"
                >
                    <!-- Judul -->
                    <div class="text-center" :style="{ marginBottom: compact ? '1mm' : '2mm' }">
                        <div class="font-bold underline tracking-wide"
                            :style="{ fontSize: compact ? '11pt' : '12pt' }">
                            SURAT PENGANTAR PENCETAKAN E-KTP
                        </div>
                        <div :style="{ fontSize: compact ? '9pt' : '10pt' }">
                            Nomor &nbsp;: {{ l.nomor_surat || '.../475/Ds/...' }}
                        </div>
                    </div>

                    <!-- Pembuka -->
                    <div class="text-justify"
                        :style="{ marginTop: compact ? '4mm' : '6mm', textJustify: 'inter-word' }">
                        Yang bertandatangan di bawah ini ,Kepala Desa {{ l.nama_desa || 'Sirnaputra' }} Kecamatan {{ l.kecamatan || 'Cigalontang' }} Kabupaten {{ l.kabupaten || 'Tasikmalaya' }},menerangkan permohonan pembuatan e-KTP dengan data sebagai berikut:
                    </div>

                    <!-- Data Warga Table -->
                    <div :style="{ marginTop: compact ? '3mm' : '5mm', paddingLeft: '8mm' }">
                        <table style="width:100%; border-collapse:collapse; line-height:1.75;">
                            <tbody>
                                <tr>
                                    <td style="width:48mm; vertical-align:top; padding:0.3mm 0;">NIK</td>
                                    <td style="width:6mm; vertical-align:top; padding:0.3mm 0;">:</td>
                                    <td style="vertical-align:top; padding:0.3mm 0; font-weight:500;">{{ l.nik }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; padding:0.3mm 0;">Nama</td>
                                    <td style="vertical-align:top; padding:0.3mm 0;">:</td>
                                    <td style="vertical-align:top; padding:0.3mm 0; font-weight:500;">{{ l.nama_warga }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; padding:0.3mm 0;">Tempat/Tanggal lahir</td>
                                    <td style="vertical-align:top; padding:0.3mm 0;">:</td>
                                    <td style="vertical-align:top; padding:0.3mm 0; font-weight:500;">
                                        {{ l.tempat_lahir }}, {{ tanggalLahirFormatted }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; padding:0.3mm 0;">Warga Negara</td>
                                    <td style="vertical-align:top; padding:0.3mm 0;">:</td>
                                    <td style="vertical-align:top; padding:0.3mm 0; font-weight:500;">{{ l.warga_negara }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; padding:0.3mm 0;">Jenis Kelamin</td>
                                    <td style="vertical-align:top; padding:0.3mm 0;">:</td>
                                    <td style="vertical-align:top; padding:0.3mm 0; font-weight:500;">{{ l.jenis_kelamin }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; padding:0.3mm 0;">Agama</td>
                                    <td style="vertical-align:top; padding:0.3mm 0;">:</td>
                                    <td style="vertical-align:top; padding:0.3mm 0; font-weight:500;">{{ l.agama }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; padding:0.3mm 0;">Status Perkawinan</td>
                                    <td style="vertical-align:top; padding:0.3mm 0;">:</td>
                                    <td style="vertical-align:top; padding:0.3mm 0; font-weight:500;">{{ l.status_perkawinan }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; padding:0.3mm 0;">Pekerjaan</td>
                                    <td style="vertical-align:top; padding:0.3mm 0;">:</td>
                                    <td style="vertical-align:top; padding:0.3mm 0; font-weight:500;">{{ l.pekerjaan }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; padding:0.3mm 0;">Alamat</td>
                                    <td style="vertical-align:top; padding:0.3mm 0;">:</td>
                                    <td style="vertical-align:top; padding:0.3mm 0; font-weight:500;">
                                        <div>{{ alamatWargaFormatted }}</div>
                                        <div class="mt-1">{{ alamatLengkapFormatted }}</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Keterangan Tambahan -->
                    <div class="text-justify"
                        :style="{ marginTop: compact ? '4mm' : '6mm', textJustify: 'inter-word' }">
                        &nbsp;&nbsp;&nbsp;&nbsp;{{ keteranganTambahan }}
                    </div>

                    <!-- Penutup -->
                    <div class="text-justify"
                        :style="{ marginTop: compact ? '3mm' : '5mm', textJustify: 'inter-word' }">
                        &nbsp;&nbsp;&nbsp;&nbsp;{{ kalimatPenutup }}
                    </div>

                    <!-- ── TANDA TANGAN TUNGGAL (kanan) ────────────────── -->
                    <div class="shrink-0 flex" :style="{ marginTop: compact ? '5mm' : '8mm' }">
                        <!-- Kotak kosong di kiri -->
                        <div class="flex-1 flex items-start justify-start">
                            <!-- area kosong untuk TTD kiri -->
                    </div>
                        <!-- Blok TTD kanan -->
                        <div class="text-center" :style="{ width: compact ? '70mm' : '80mm' }">
                            <!-- Tanggal -->
                            <div :style="{ fontSize: compact ? '9.5pt' : '10.5pt' }">
                                {{ l.nama_desa || 'Sirnaputra' }}, {{ tanggalSuratFormatted }}
                            </div>

                            <!-- Label atasan -->
                            <div v-if="l.label_atasan"
                                :style="{ fontSize: compact ? '9.5pt' : '10.5pt' }">
                                {{ l.label_atasan }}
                            </div>

                            <!-- Jabatan -->
                            <div class="font-bold"
                                :style="{ fontSize: compact ? '9.5pt' : '10.5pt', marginTop: '0.5mm' }">
                                {{ l.jabatan_penandatangan || 'Kepala Desa Sirnaputra' }}
                            </div>

                            <!-- Area TTD + Stempel -->
                            <div class="relative mx-auto flex items-center justify-center"
                                :style="{
                                    height: compact ? '18mm' : '24mm',
                                    width: compact ? '60mm' : '70mm',
                                    margin: compact ? '1mm auto' : '2mm auto',
                                }">
                                <img v-if="l.stempel" :src="l.stempel"
                                    class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 object-contain pointer-events-none"
                                    :style="{ height: compact ? '22mm' : '28mm', width: compact ? '22mm' : '28mm', opacity: '0.85' }"
                                    alt="" />
                                <img v-if="l.tanda_tangan" :src="l.tanda_tangan"
                                    class="relative z-10 object-contain"
                                    :style="{ maxHeight: compact ? '16mm' : '22mm', maxWidth: '90%' }"
                                    alt="" />
                            </div>

                            <!-- Nama -->
                            <div class="font-extrabold underline uppercase tracking-wide"
                                :style="{ fontSize: compact ? '10pt' : '11pt' }">
                                {{ l.nama_penandatangan }}
                            </div>

                            <!-- NIP -->
                            <div v-if="l.nip_niapd" class="font-medium"
                                :style="{ fontSize: compact ? '8.5pt' : '9.5pt', marginTop: '0.5mm' }">
                                NIP. {{ l.nip_niapd }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
