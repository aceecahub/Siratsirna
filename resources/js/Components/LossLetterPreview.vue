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
        kabupaten:  s.kabupaten  || '',
        kecamatan:  s.kecamatan  || '',
        nama_desa:  s.nama_desa  || '',
        alamat:     s.alamat     || '',
        kode_pos:   s.kode_pos   || '',
        kontak:     s.kontak     || '',
        email_desa: s.email_desa || '',
        logo_kiri:  s.logo_kiri  || null,
        logo_kanan: s.logo_kanan || null,

        nomor_surat:   s.nomor_surat   || '',
        tanggal_surat: s.tanggal_surat || '',

        nama_warga:      s.nama_warga      || '',
        jenis_kelamin:   s.jenis_kelamin   || 'Laki – Laki',
        alamat_warga:    s.alamat_warga    || '',
        rt:              s.rt              || '',
        rw:              s.rw              || '',
        dusun:           s.dusun           || '',
        desa_warga:      s.desa_warga      || '',
        kecamatan_warga: s.kecamatan_warga || '',
        kabupaten_warga: s.kabupaten_warga || '',

        barang_hilang:         s.barang_hilang         || '',
        keterangan_kehilangan: s.keterangan_kehilangan || '',
        kalimat_penutup:       s.kalimat_penutup       || '',

        label_atasan:          s.label_atasan          || '',
        jabatan_penandatangan: s.jabatan_penandatangan || '',
        nama_penandatangan:    s.nama_penandatangan    || '',
        nip_niapd:             s.nip_niapd             || '',
        tanda_tangan:          s.tanda_tangan          || null,
        stempel:               s.stempel               || null,
    };
});

// Kop Formatted
const kabFormatted = computed(() => {
    const r = (l.value.kabupaten || '').trim().toUpperCase();
    if (!r) return 'PEMERINTAH DAERAH KABUPATEN TASIKMALAYA';
    if (r.startsWith('PEMERINTAH')) return r;
    if (r.startsWith('KABUPATEN')) return `PEMERINTAH DAERAH ${r}`;
    return `PEMERINTAH DAERAH KABUPATEN ${r}`;
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

// Alamat warga formatted
const alamatWargaFormatted = computed(() => {
    if (l.value.alamat_warga) return l.value.alamat_warga;
    const parts = [];
    if (l.value.dusun) parts.push(`Kp ${l.value.dusun}`);
    parts.push(`Rt ${l.value.rt ? String(l.value.rt) : '...'}`);
    parts.push(`Rw ${l.value.rw ? String(l.value.rw) : '....'}`);
    const desa = l.value.desa_warga || l.value.nama_desa || '';
    if (desa) parts.push(desa.toUpperCase().startsWith('DESA') ? desa : `Desa ${desa}`);
    const kec = l.value.kecamatan_warga || (l.value.kecamatan || '').replace(/^KECAMATAN\s*/i, '');
    if (kec) parts.push(`Kec ${kec}`);
    const kab = l.value.kabupaten_warga || (l.value.kabupaten || '').replace(/^PEMERINTAH.*?KABUPATEN\s*/i, '');
    if (kab) parts.push(`Kabupaten ${kab}`);
    return parts.join(' ');
});

const keteranganKehilangan = computed(() => {
    if (l.value.keterangan_kehilangan) return l.value.keterangan_kehilangan;
    const barang = l.value.barang_hilang || 'SPPT Pajak Bumi & Bangunan';
    return `Orang tersebut diatas adalah betul-betul merupakan pemilik dokumen/barang berupa ${barang}, menerangkan bahwa dokumen/barang tersebut Hilang.`;
});

const kalimatPenutup = computed(() =>
    l.value.kalimat_penutup ||
    'Demikian surat keterangan ini dibuat agar yang berkepentingan menjadi tahu dan untuk dijadikan bahan sebagaimana mestinya.'
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
                            <div class="mt-1" :style="{ fontSize: compact ? '8pt' : '8.5pt' }">
                                <template v-if="l.alamat">Kantor :JL {{ l.alamat }}</template>
                            </div>
                            <div :style="{ fontSize: compact ? '8pt' : '8.5pt' }">
                                <template v-if="l.email_desa">{{ l.email_desa }}</template>
                                <template v-if="l.email_desa && l.kontak"> | </template>
                                <template v-if="l.kontak">Telp : {{ l.kontak }}</template>
                            </div>
                            <div :style="{ fontSize: compact ? '8pt' : '8.5pt' }">
                                <template v-if="l.kode_pos">Kode Pos {{ l.kode_pos }}</template>
                            </div>
                        </div>

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
                            SURAT KETERANGAN KEHILANGAN
                        </div>
                        <div :style="{ fontSize: compact ? '9pt' : '10pt' }">
                            Nomor: {{ l.nomor_surat || '.../470/196/XII/DS/...' }}
                        </div>
                    </div>

                    <!-- Pembuka -->
                    <div class="text-justify"
                        :style="{ marginTop: compact ? '4mm' : '6mm', textJustify: 'inter-word' }">
                        Yang bertanda tangan dibawah ini Kepala Desa {{ l.nama_desa || 'Sirnaputra' }} Kecamatan {{ l.kecamatan || 'Cigalontang' }} Kabupaten {{ l.kabupaten || 'Tasikmalaya' }} dengan ini menerangkan bahwa :
                    </div>

                    <!-- Data Warga Table -->
                    <div :style="{ marginTop: compact ? '3mm' : '5mm', paddingLeft: '8mm' }">
                        <table style="width:100%; border-collapse:collapse; line-height:1.75;">
                            <tbody>
                                <tr>
                                    <td style="width:40mm; vertical-align:top; padding:0.4mm 0;">Nama</td>
                                    <td style="width:6mm; vertical-align:top; padding:0.4mm 0;">:</td>
                                    <td style="vertical-align:top; padding:0.4mm 0; font-weight:500;">{{ l.nama_warga }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; padding:0.4mm 0;">Jenis Kelamin</td>
                                    <td style="vertical-align:top; padding:0.4mm 0;">:</td>
                                    <td style="vertical-align:top; padding:0.4mm 0; font-weight:500;">{{ l.jenis_kelamin }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; padding:0.4mm 0;">Alamat</td>
                                    <td style="vertical-align:top; padding:0.4mm 0;">:</td>
                                    <td style="vertical-align:top; padding:0.4mm 0; font-weight:500; white-space:pre-line;">{{ alamatWargaFormatted }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Keterangan Kehilangan -->
                    <div class="text-justify"
                        :style="{ marginTop: compact ? '4mm' : '6mm', textJustify: 'inter-word' }">
                        &nbsp;&nbsp;&nbsp;&nbsp;{{ keteranganKehilangan }}
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
                            <div class="border border-dashed border-gray-300 print:border-gray-300"
                                :style="{ width: compact ? '60mm' : '68mm', height: compact ? '28mm' : '35mm' }" />
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
