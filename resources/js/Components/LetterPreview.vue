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
        default: 'f4', // Default F4 format
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

const heightMm = computed(() =>
    props.paper === 'a4' ? A4_H_MM : F4_H_MM,
);
const sheetWidthPx = SHEET_W_MM * MM_TO_PX;
const sheetHeightPx = computed(() => heightMm.value * MM_TO_PX);

const wrapper = ref(null);
const scale = ref(1);

const scaledHeight = computed(() => sheetHeightPx.value * scale.value);
const scaledWidth = computed(() => sheetWidthPx * scale.value);

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
        kabupaten:  s.kabupaten  || 'Tasikmalaya',
        kecamatan:  s.kecamatan  || 'Cigalontang',
        nama_desa:  s.nama_desa  || 'Sirnaputra',
        alamat: s.alamat || '',
        kode_pos: s.kode_pos || '',
        kontak: s.kontak || '',
        email_desa: s.email_desa || '',
        logo_kiri:  s.logo_kiri  || '/images/logo-tasikmalaya.png',
        logo_kanan: s.logo_kanan || null,
        nomor_surat: s.nomor_surat || '',
        sifat: s.sifat || '',
        lampiran: s.lampiran || '',
        perihal: s.perihal || '',
        tanggal_surat: s.tanggal_surat || '',
        tujuan: s.tujuan || '',
        pembuka: s.pembuka || '',
        isi_surat: s.isi_surat || '',
        penutup: s.penutup || '',
        jabatan_penandatangan: s.jabatan_penandatangan || '',
        nama_penandatangan: s.nama_penandatangan || '',
        nip_niapd: s.nip_niapd || '',
        tanda_tangan: s.tanda_tangan || null,
        stempel: s.stempel || null,
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

const tanggalFormatted = computed(() => formatTanggal(l.value.tanggal_surat));

function isArabicOrSalam(text) {
    if (!text) return false;
    const lower = text.toLowerCase().trim();
    return (
        lower.includes('سلام') ||
        lower.includes('assalamu') ||
        lower.includes('wassalamu')
    );
}

const signers = computed(() => {
    const list = props.letter?.penandatangan_list;
    if (Array.isArray(list) && list.length > 0) {
        return list;
    }
    return [
        {
            peran: 'kanan',
            label_mengetahui: '',
            jabatan: l.value.jabatan_penandatangan || '',
            nama: l.value.nama_penandatangan || '',
            nip_niapd: l.value.nip_niapd || '',
            tanda_tangan: l.value.tanda_tangan || null,
        },
    ];
});

const leftSigners = computed(() => signers.value.filter((s) => s.peran === 'kiri'));
const rightSigners = computed(() =>
    signers.value.filter((s) => s.peran === 'kanan' || (!s.peran && s.peran !== 'kiri' && s.peran !== 'mengetahui')),
);
const bottomSigners = computed(() => signers.value.filter((s) => s.peran === 'mengetahui'));
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
            <!-- Watermark Transparan di Tengah Halaman -->
            <div
                v-if="l.stempel"
                class="pointer-events-none absolute inset-0 z-0 flex items-center justify-center overflow-hidden"
            >
                <img
                    :src="l.stempel"
                    class="watermark-img h-[105mm] w-[105mm] object-contain opacity-[0.08]"
                    style="opacity: 0.08 !important;"
                    alt=""
                />
            </div>

            <div class="relative z-10 flex h-full flex-col justify-between" style="min-height: calc(100% - 2px)">
                <!-- Kop Surat -->
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

                        <!-- Text Kop (Diisi pr-[26mm] agar teks tetap presisi di tengah halaman) -->
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
                            <img
                                :src="l.logo_kanan"
                                class="h-[25mm] w-full object-contain"
                                alt=""
                            />
                        </div>
                    </div>

                    <!-- Double Line Garis Kop Surat -->
                    <div class="mt-2.5 border-b-[2.5px] border-black" />
                    <div class="mt-[1.5px] border-b border-black" />
                </div>

                <!-- Section Tanggal, Meta & Tujuan Surat (Side by Side) -->
                <div class="flex flex-1 flex-col" :style="{ paddingTop: compact ? '2mm' : '4mm' }">
                    <div class="flex-1 flex flex-col justify-between">
                        <div>
                            <div
                                class="flex items-start justify-between gap-6"
                                :style="{ fontSize: compact ? '9.5pt' : '10.5pt' }"
                            >
                                <!-- Kolom Kiri: Nomor, Sifat, Lampiran, Perihal -->
                                <div class="w-[55%] min-w-0">
                                    <div
                                        class="grid"
                                        :style="{
                                            gridTemplateColumns: '20mm 4mm 1fr',
                                            rowGap: '2px',
                                            lineHeight: '1.4',
                                        }"
                                    >
                                        <span>Nomor</span>
                                        <span>:</span>
                                        <span class="break-words font-medium">{{ l.nomor_surat }}</span>

                                        <span>Sifat</span>
                                        <span>:</span>
                                        <span>{{ l.sifat || 'Biasa' }}</span>

                                        <span>Lampiran</span>
                                        <span>:</span>
                                        <span>{{ l.lampiran || '-' }}</span>

                                        <span>Perihal</span>
                                        <span>:</span>
                                        <span class="font-bold underline leading-snug break-words">{{ l.perihal }}</span>
                                    </div>
                                </div>

                                <!-- Kolom Kanan: Tanggal Surat & Kepada Yth. -->
                                <div class="w-[45%] min-w-0 leading-tight">
                                    <div class="mb-3">
                                        {{ l.nama_desa }}, {{ tanggalFormatted }}
                                    </div>

                                    <div>
                                        <div class="font-normal">Kepada</div>
                                        <div class="flex items-start">
                                            <span class="font-semibold shrink-0 pr-1">Yth.</span>
                                            <div class="whitespace-pre-line font-medium leading-snug flex-1">{{ l.tujuan }}</div>
                                        </div>
                                        <div class="mt-1 pl-6">di -</div>
                                        <div class="pl-10 font-bold">Tempat</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Salam Pembuka -->
                            <div
                                v-if="l.pembuka"
                                class="mt-4"
                                :class="isArabicOrSalam(l.pembuka) ? 'text-center font-bold text-[11.5pt]' : 'text-justify text-[10.5pt]'"
                            >
                                {{ l.pembuka }}
                            </div>

                            <!-- Isi Surat Utama (Rata Kanan-Kiri Justify) -->
                            <div
                                v-if="l.isi_surat"
                                class="mt-3 whitespace-pre-line text-justify leading-relaxed"
                                :style="{
                                    fontSize: compact ? '9.5pt' : '10.5pt',
                                    lineHeight: compact ? '1.45' : '1.6',
                                    textAlign: 'justify',
                                    textJustify: 'inter-word'
                                }"
                            >
                                {{ l.isi_surat }}
                            </div>

                            <!-- Salam Penutup -->
                            <div
                                v-if="l.penutup"
                                class="mt-3"
                                :class="isArabicOrSalam(l.penutup) ? 'text-center font-bold text-[11.5pt]' : 'text-justify text-[10.5pt]'"
                            >
                                {{ l.penutup }}
                            </div>
                        </div>

                        <!-- Tanda Tangan Digital -->
                        <div
                            class="shrink-0 space-y-2"
                            :class="compact ? 'mt-3 pt-1' : 'mt-5 pt-1'"
                            :style="{ fontSize: compact ? '9.5pt' : '10.5pt', breakInside: 'avoid', pageBreakInside: 'avoid' }"
                        >
                            <!-- Baris Pertama Signer (Kiri & Kanan) -->
                            <div class="flex items-start justify-between gap-4">
                                <!-- Kolom Kiri -->
                                <div class="w-[66mm] text-center flex flex-col items-center">
                                    <div v-for="(signer, idx) in leftSigners" :key="'left-' + idx" class="w-full">
                                        <div v-if="signer.label_mengetahui" class="font-bold leading-tight">
                                            {{ signer.label_mengetahui }}
                                        </div>
                                        <div class="font-bold leading-tight" :style="{ fontSize: compact ? '9.5pt' : '10.5pt' }">
                                            {{ signer.jabatan }}
                                        </div>

                                        <div
                                            class="relative mx-auto flex items-center justify-center my-1"
                                            :class="compact ? 'h-[16mm] w-[56mm]' : 'h-[20mm] w-[60mm]'"
                                        >
                                            <img
                                                v-if="signer.stempel"
                                                :src="signer.stempel"
                                                class="signer-stempel-img absolute left-1/2 top-1/2 z-0 -translate-x-1/2 -translate-y-1/2 object-contain pointer-events-none"
                                                :class="compact ? 'h-[24mm] w-[24mm]' : 'h-[28mm] w-[28mm]'"
                                                style="opacity: 0.85 !important;"
                                                alt=""
                                            />
                                            <img
                                                v-if="signer.tanda_tangan"
                                                :src="signer.tanda_tangan"
                                                class="relative z-10 object-contain"
                                                :class="compact ? 'max-h-[16mm] max-w-[54mm]' : 'max-h-[20mm] max-w-[58mm]'"
                                                alt=""
                                            />
                                        </div>

                                        <div class="font-extrabold underline tracking-wide uppercase" :style="{ fontSize: compact ? '10pt' : '11pt' }">
                                            {{ signer.nama }}
                                        </div>
                                        <div :style="{ fontSize: compact ? '8.5pt' : '9.5pt' }" class="mt-0.5 font-medium">
                                            <template v-if="signer.nip_niapd">
                                                NIP/NIAPD. {{ signer.nip_niapd }}
                                            </template>
                                            <template v-else>&nbsp;</template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="w-[66mm] text-center flex flex-col items-center ml-auto">
                                    <div v-for="(signer, idx) in rightSigners" :key="'right-' + idx" class="w-full">
                                        <div v-if="signer.label_mengetahui" class="font-bold leading-tight">
                                            {{ signer.label_mengetahui }}
                                        </div>
                                        <div class="font-bold leading-tight" :style="{ fontSize: compact ? '9.5pt' : '10.5pt' }">
                                            {{ signer.jabatan }}
                                        </div>

                                        <div
                                            class="relative mx-auto flex items-center justify-center my-1"
                                            :class="compact ? 'h-[16mm] w-[56mm]' : 'h-[20mm] w-[60mm]'"
                                        >
                                            <img
                                                v-if="signer.stempel"
                                                :src="signer.stempel"
                                                class="signer-stempel-img absolute left-1/2 top-1/2 z-0 -translate-x-1/2 -translate-y-1/2 object-contain pointer-events-none"
                                                :class="compact ? 'h-[24mm] w-[24mm]' : 'h-[28mm] w-[28mm]'"
                                                style="opacity: 0.85 !important;"
                                                alt=""
                                            />
                                            <img
                                                v-if="signer.tanda_tangan"
                                                :src="signer.tanda_tangan"
                                                class="relative z-10 object-contain"
                                                :class="compact ? 'max-h-[16mm] max-w-[54mm]' : 'max-h-[20mm] max-w-[58mm]'"
                                                alt=""
                                            />
                                        </div>

                                        <div class="font-extrabold underline tracking-wide uppercase" :style="{ fontSize: compact ? '10pt' : '11pt' }">
                                            {{ signer.nama }}
                                        </div>
                                        <div :style="{ fontSize: compact ? '8.5pt' : '9.5pt' }" class="mt-0.5 font-medium">
                                            <template v-if="signer.nip_niapd">
                                                NIP/NIAPD. {{ signer.nip_niapd }}
                                            </template>
                                            <template v-else>&nbsp;</template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Baris Mengetahui (Tengah Bawah) -->
                            <div v-if="bottomSigners.length > 0" class="flex flex-col items-center justify-center text-center pt-1 mt-2">
                                <div v-for="(signer, idx) in bottomSigners" :key="'bottom-' + idx" class="w-[70mm]">
                                    <div class="font-bold leading-tight" :style="{ fontSize: compact ? '9.5pt' : '10.5pt' }">
                                        {{ signer.label_mengetahui || 'Mengetahui,' }}
                                    </div>
                                    <div class="font-bold leading-tight" :style="{ fontSize: compact ? '9.5pt' : '10.5pt' }">
                                        {{ signer.jabatan }}
                                    </div>

                                    <div
                                        class="relative mx-auto flex items-center justify-center my-1"
                                        :class="compact ? 'h-[16mm] w-[56mm]' : 'h-[20mm] w-[60mm]'"
                                    >
                                        <img
                                            v-if="signer.stempel"
                                            :src="signer.stempel"
                                            class="signer-stempel-img absolute left-1/2 top-1/2 z-0 -translate-x-1/2 -translate-y-1/2 object-contain pointer-events-none"
                                            :class="compact ? 'h-[24mm] w-[24mm]' : 'h-[28mm] w-[28mm]'"
                                            style="opacity: 0.85 !important;"
                                            alt=""
                                        />
                                        <img
                                            v-if="signer.tanda_tangan"
                                            :src="signer.tanda_tangan"
                                            class="relative z-10 object-contain"
                                            :class="compact ? 'max-h-[16mm] max-w-[54mm]' : 'max-h-[20mm] max-w-[58mm]'"
                                            alt=""
                                        />
                                    </div>

                                    <div class="font-extrabold underline tracking-wide uppercase" :style="{ fontSize: compact ? '10pt' : '11pt' }">
                                        {{ signer.nama }}
                                    </div>
                                    <div :style="{ fontSize: compact ? '8.5pt' : '9.5pt' }" class="mt-0.5 font-medium">
                                        <template v-if="signer.nip_niapd">
                                            NIP/NIAPD. {{ signer.nip_niapd }}
                                        </template>
                                        <template v-else>&nbsp;</template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
