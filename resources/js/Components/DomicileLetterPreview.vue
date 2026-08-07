<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { formatTanggal } from '@/lib/format';

const props = defineProps({
    letter: {
        type: Object,
        required: true,
    },
    paper: {
        type: String,
        default: 'f4',
        validator: (value) => ['a4', 'f4'].includes(value),
    },
    scaled: {
        type: Boolean,
        default: false,
    },
    compact: {
        type: Boolean,
        default: false,
    },
});

const SERIF = "'Times New Roman', Times, serif";
const SHEET_W_MM = 210;
const A4_H_MM = 297;
const F4_H_MM = 330;
const MM_TO_PX = 96 / 25.4;

const heightMm = computed(() => (props.paper === 'a4' ? A4_H_MM : F4_H_MM));
const sheetWidthPx = SHEET_W_MM * MM_TO_PX;
const sheetHeightPx = computed(() => heightMm.value * MM_TO_PX);
const scaledHeight = computed(() => sheetHeightPx.value * scale.value);
const scaledWidth = computed(() => sheetWidthPx * scale.value);

const wrapper = ref(null);
const scale = ref(1);
let observer = null;

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

onBeforeUnmount(() => {
    observer?.disconnect();
});

const l = computed(() => {
    const s = props.letter ?? {};
    return {
        kabupaten:   s.kabupaten   || '',
        kecamatan:   s.kecamatan   || '',
        nama_desa:   s.nama_desa   || '',
        alamat:      s.alamat      || '',
        kode_pos:    s.kode_pos    || '',
        kontak:      s.kontak      || '',
        email_desa:  s.email_desa  || '',
        logo_kiri:   s.logo_kiri   || null,
        logo_kanan:  s.logo_kanan  || null,
        nomor_surat:   s.nomor_surat   || '',
        tanggal_surat: s.tanggal_surat || '',
        nik:           s.nik           || '',
        nama_warga:    s.nama_warga    || '',
        tempat_lahir:  s.tempat_lahir  || '',
        tanggal_lahir: s.tanggal_lahir || '',
        jenis_kelamin: s.jenis_kelamin || '',
        warga_negara:  s.warga_negara  || 'Indonesia',
        agama:         s.agama         || '',
        pekerjaan:     s.pekerjaan     || '',
        alamat_warga:  s.alamat_warga  || '',
        rt:            s.rt            || '',
        rw:            s.rw            || '',
        dusun:         s.dusun         || '',
        keterangan_domisili: s.keterangan_domisili || '',
        catatan_ektp:        s.catatan_ektp        || '',
        jabatan_penandatangan: s.jabatan_penandatangan || '',
        nama_penandatangan:    s.nama_penandatangan    || '',
        nip_niapd:             s.nip_niapd             || '',
        tanda_tangan:          s.tanda_tangan          || null,
        stempel:               s.stempel               || null,
    };
});

const kabupatenFormatted = computed(() => {
    const raw = (l.value.kabupaten || '').trim();
    if (!raw) return 'PEMERINTAH DAERAH KABUPATEN TASIKMALAYA';
    const upper = raw.toUpperCase();
    if (upper.startsWith('PEMERINTAH')) return upper;
    if (upper.startsWith('KABUPATEN')) return `PEMERINTAH DAERAH ${upper}`;
    return `PEMERINTAH DAERAH KABUPATEN ${upper}`;
});

const kecamatanFormatted = computed(() => {
    const raw = (l.value.kecamatan || '').trim();
    if (!raw) return 'KECAMATAN CIGALONTANG';
    const upper = raw.toUpperCase();
    if (upper.startsWith('KECAMATAN')) return upper;
    return `KECAMATAN ${upper}`;
});

const desaFormatted = computed(() => {
    const raw = (l.value.nama_desa || '').trim();
    if (!raw) return 'DESA SIRNAPUTRA';
    const upper = raw.toUpperCase();
    if (upper.startsWith('DESA')) return upper;
    return `DESA ${upper}`;
});

const tanggalSuratFormatted = computed(() => formatTanggal(l.value.tanggal_surat));
const tanggalLahirFormatted = computed(() => formatTanggal(l.value.tanggal_lahir));

// Build alamat warga string (Kp. X RT Y RW Z Ds. nama_desa Kec. kecamatan Kab. kabupaten)
const alamatWargaFormatted = computed(() => {
    const parts = [];
    if (l.value.dusun) parts.push(`Kp. ${l.value.dusun}`);
    if (l.value.rt)    parts.push(`RT ${l.value.rt.toString().padStart(3, '0')}`);
    if (l.value.rw)    parts.push(`RW ${l.value.rw.toString().padStart(3, '0')}`);
    if (l.value.nama_desa) {
        const raw = l.value.nama_desa.trim();
        const hasDesa = raw.toUpperCase().startsWith('DESA');
        parts.push(hasDesa ? raw : `Ds. ${raw}`);
    }
    const kec = (l.value.kecamatan || '').replace(/^KECAMATAN\s*/i, '');
    if (kec) parts.push(`Kec. ${kec}`);
    const kab = (l.value.kabupaten || '').replace(/^PEMERINTAH.*?KABUPATEN\s*/i, '');
    if (kab) parts.push(`Kab. ${kab}`);
    return parts.join(' ');
});

// Default keterangan & catatan if empty
const keteranganText = computed(() => {
    if (l.value.keterangan_domisili) return l.value.keterangan_domisili;
    const kp   = l.value.dusun    ? `Kp. ${l.value.dusun}` : '';
    const rt   = l.value.rt       ? `RT ${l.value.rt.toString().padStart(3,'0')}` : '';
    const rw   = l.value.rw       ? `RW ${l.value.rw.toString().padStart(3,'0')}` : '';
    const loc  = [kp, rt, rw].filter(Boolean).join(' ');
    const nama = l.value.nama_desa ? l.value.nama_desa : '{nama_desa}';
    return `Orang tersebut di atas adalah benar Warga/Penduduk Desa kami yang berdomisili di ${loc || '{alamat}'}, kemudian kami terangkan pula bahwa E-KTP orang tersebut Masih dalam Proses Cetak.`;
});

const catatanText = computed(() => {
    if (l.value.catatan_ektp) return l.value.catatan_ektp;
    return '';
});
</script>

<template>
    <div
        ref="wrapper"
        class="letter-scale-wrap mx-auto relative"
        :style="{
            height: scaled ? `${scaledHeight}px` : 'auto',
            width: scaled ? `${scaledWidth}px` : '100%',
        }"
    >
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
                padding: props.compact ? '18mm 15mm 15mm 20mm' : '25mm 20mm 20mm 25mm',
                boxShadow: '0 10px 30px -12px rgba(0,0,0,0.35)',
                boxSizing: 'border-box',
            }"
        >
            <!-- Watermark stempel di tengah halaman -->
            <div
                v-if="l.stempel"
                class="pointer-events-none absolute inset-0 z-0 flex items-center justify-center overflow-hidden"
            >
                <img
                    :src="l.stempel"
                    class="watermark-img h-[105mm] w-[105mm] object-contain"
                    style="opacity: 0.08 !important;"
                    alt=""
                />
            </div>

            <div class="relative z-10 flex h-full flex-col" style="min-height: calc(100% - 2px)">
                <!-- KOP SURAT -->
                <div class="shrink-0" style="break-inside: avoid; page-break-inside: avoid">
                    <div class="flex items-center">
                        <!-- Logo Kiri -->
                        <div class="w-[26mm] shrink-0 flex justify-center">
                            <img
                                v-if="l.logo_kiri"
                                :src="l.logo_kiri"
                                class="h-[25mm] w-full object-contain"
                                alt=""
                            />
                            <div
                                v-else
                                class="flex h-[25mm] w-full items-center justify-center rounded border border-dashed border-gray-400 text-[8pt] font-semibold leading-tight text-gray-500 print:hidden"
                            >
                                Logo<br />Kabupaten
                            </div>
                        </div>

                        <!-- Text Kop Tengah -->
                        <div
                            class="min-w-0 flex-1 text-center leading-snug"
                            :class="{ 'pr-[26mm]': !l.logo_kanan }"
                        >
                            <div
                                class="font-bold uppercase tracking-tight text-black"
                                :style="{ fontSize: compact ? '11.5pt' : '12.5pt' }"
                            >
                                {{ kabupatenFormatted }}
                            </div>
                            <div
                                class="font-bold uppercase tracking-tight text-black mt-0.5"
                                :style="{ fontSize: compact ? '10.5pt' : '11.5pt' }"
                            >
                                {{ kecamatanFormatted }}
                            </div>
                            <div
                                class="mt-0.5 font-bold uppercase tracking-wider text-black"
                                :style="{ fontSize: compact ? '13.5pt' : '15.5pt' }"
                            >
                                {{ desaFormatted }}
                            </div>
                            <div
                                class="mt-1 text-black font-normal"
                                :style="{ fontSize: compact ? '8.5pt' : '9pt' }"
                            >
                                <template v-if="l.alamat">Kantor : {{ l.alamat }}</template>
                            </div>
                            <div
                                class="text-black font-normal"
                                :style="{ fontSize: compact ? '8.5pt' : '9pt' }"
                            >
                                <template v-if="l.email_desa">{{ l.email_desa }}</template>
                                <template v-if="l.email_desa && l.kontak">&nbsp;|&nbsp;</template>
                                <template v-if="l.kontak">Telp: {{ l.kontak }}</template>
                            </div>
                            <div
                                class="text-black font-normal"
                                :style="{ fontSize: compact ? '8.5pt' : '9pt' }"
                            >
                                <template v-if="l.kode_pos">Kode Pos {{ l.kode_pos }}</template>
                            </div>
                        </div>

                        <!-- Logo Kanan -->
                        <div v-if="l.logo_kanan" class="w-[26mm] shrink-0 flex justify-center">
                            <img
                                :src="l.logo_kanan"
                                class="h-[25mm] w-full object-contain"
                                alt=""
                            />
                        </div>
                    </div>

                    <!-- Garis Double Kop -->
                    <div class="mt-2.5 border-b-[2.5px] border-black" />
                    <div class="mt-[1.5px] border-b border-black" />
                </div>

                <!-- ISI SURAT -->
                <div class="flex flex-1 flex-col" :style="{ paddingTop: compact ? '3mm' : '5mm', fontSize: compact ? '10pt' : '11pt' }">

                    <!-- Judul -->
                    <div class="text-center" :style="{ marginBottom: compact ? '1mm' : '2mm' }">
                        <div class="font-bold underline tracking-wide" :style="{ fontSize: compact ? '11pt' : '12pt' }">
                            SURAT KETERANGAN
                        </div>
                        <div :style="{ fontSize: compact ? '9.5pt' : '10.5pt' }">
                            Nomor : {{ l.nomor_surat || '.../.../DS/...' }}
                        </div>
                    </div>

                    <!-- Pembuka -->
                    <div
                        class="text-justify"
                        :style="{
                            marginTop: compact ? '3mm' : '5mm',
                            lineHeight: compact ? '1.5' : '1.65',
                            textAlign: 'justify',
                            textJustify: 'inter-word',
                        }"
                    >
                        Yang bertanda tangan dibawah ini Kepala Desa {{ l.nama_desa }}, Kecamatan {{ l.kecamatan }}, Kabupaten {{ l.kabupaten }},&nbsp;dengan ini menerangkan bahwa :
                    </div>

                    <!-- Tabel Data Warga -->
                    <div
                        :style="{
                            marginTop: compact ? '3mm' : '5mm',
                            paddingLeft: '8mm',
                        }"
                    >
                        <table style="width: 100%; border-collapse: collapse; line-height: 1.7;">
                            <tbody>
                                <tr>
                                    <td style="width: 50mm; vertical-align: top; padding: 0.5mm 0;">NIK E - KTP</td>
                                    <td style="width: 6mm; vertical-align: top; padding: 0.5mm 0;">:</td>
                                    <td style="vertical-align: top; padding: 0.5mm 0; font-weight: 500;">{{ l.nik }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; padding: 0.5mm 0;">Nama</td>
                                    <td style="vertical-align: top; padding: 0.5mm 0;">:</td>
                                    <td style="vertical-align: top; padding: 0.5mm 0; font-weight: 500;">{{ l.nama_warga }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; padding: 0.5mm 0;">Tempat Tanggal Lahir</td>
                                    <td style="vertical-align: top; padding: 0.5mm 0;">:</td>
                                    <td style="vertical-align: top; padding: 0.5mm 0; font-weight: 500;">
                                        {{ l.tempat_lahir }}, {{ tanggalLahirFormatted }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; padding: 0.5mm 0;">Jenis Kelamin</td>
                                    <td style="vertical-align: top; padding: 0.5mm 0;">:</td>
                                    <td style="vertical-align: top; padding: 0.5mm 0; font-weight: 500;">{{ l.jenis_kelamin }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; padding: 0.5mm 0;">Warga Negara</td>
                                    <td style="vertical-align: top; padding: 0.5mm 0;">:</td>
                                    <td style="vertical-align: top; padding: 0.5mm 0; font-weight: 500;">{{ l.warga_negara }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; padding: 0.5mm 0;">Agama</td>
                                    <td style="vertical-align: top; padding: 0.5mm 0;">:</td>
                                    <td style="vertical-align: top; padding: 0.5mm 0; font-weight: 500;">{{ l.agama }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; padding: 0.5mm 0;">Pekerjaan</td>
                                    <td style="vertical-align: top; padding: 0.5mm 0;">:</td>
                                    <td style="vertical-align: top; padding: 0.5mm 0; font-weight: 500;">{{ l.pekerjaan }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; padding: 0.5mm 0;">Alamat</td>
                                    <td style="vertical-align: top; padding: 0.5mm 0;">:</td>
                                    <td style="vertical-align: top; padding: 0.5mm 0; font-weight: 500; white-space: pre-line;">{{ alamatWargaFormatted || l.alamat_warga }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Keterangan Domisili -->
                    <div
                        class="text-justify"
                        :style="{
                            marginTop: compact ? '3mm' : '5mm',
                            lineHeight: compact ? '1.5' : '1.65',
                            textAlign: 'justify',
                            textJustify: 'inter-word',
                        }"
                    >
                        &nbsp;&nbsp;&nbsp;&nbsp;{{ keteranganText }}
                    </div>

                    <!-- Penutup -->
                    <div
                        class="text-justify"
                        :style="{
                            marginTop: compact ? '3mm' : '5mm',
                            lineHeight: compact ? '1.5' : '1.65',
                        }"
                    >
                        &nbsp;&nbsp;&nbsp;&nbsp;Demikian surat keterangan ini kami buat dengan sebenarnya dan untuk dipergunakan sebagai mana mestinya.
                    </div>

                    <!-- Tanggal & Tanda Tangan -->
                    <div
                        class="shrink-0"
                        :style="{
                            marginTop: compact ? '4mm' : '6mm',
                            breakInside: 'avoid',
                            pageBreakInside: 'avoid',
                        }"
                    >
                        <!-- Tanggal di kanan -->
                        <div class="text-center" style="float: right; width: 65mm;">
                            {{ l.nama_desa }}, {{ tanggalSuratFormatted }}
                        </div>
                        <div style="clear: both;"></div>

                        <!-- Blok TTD Kepala Desa (kanan bawah) -->
                        <div class="flex justify-end">
                            <div class="text-center" style="width: 65mm;">
                                <div class="font-bold" :style="{ fontSize: compact ? '9.5pt' : '10.5pt' }">
                                    {{ l.jabatan_penandatangan || 'Kepala Desa' }} {{ l.nama_desa }}
                                </div>

                                <!-- Area TTD & Stempel -->
                                <div
                                    class="relative mx-auto flex items-center justify-center"
                                    :class="compact ? 'my-1 h-[16mm] w-[55mm]' : 'my-2 h-[22mm] w-[60mm]'"
                                >
                                    <img
                                        v-if="l.stempel"
                                        :src="l.stempel"
                                        class="signer-stempel-img absolute left-1/2 top-1/2 z-0 -translate-x-1/2 -translate-y-1/2 object-contain pointer-events-none"
                                        :class="compact ? 'h-[22mm] w-[22mm]' : 'h-[28mm] w-[28mm]'"
                                        style="opacity: 0.85 !important;"
                                        alt=""
                                    />
                                    <img
                                        v-if="l.tanda_tangan"
                                        :src="l.tanda_tangan"
                                        class="relative z-10 object-contain"
                                        :class="compact ? 'max-h-[14mm] max-w-[52mm]' : 'max-h-[20mm] max-w-[58mm]'"
                                        alt=""
                                    />
                                </div>

                                <div
                                    class="font-extrabold underline tracking-wide uppercase"
                                    :style="{ fontSize: compact ? '10pt' : '11pt' }"
                                >
                                    {{ l.nama_penandatangan }}
                                </div>
                                <div
                                    v-if="l.nip_niapd"
                                    class="font-medium mt-0.5"
                                    :style="{ fontSize: compact ? '8.5pt' : '9.5pt' }"
                                >
                                    NIP/NIAPD. {{ l.nip_niapd }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
