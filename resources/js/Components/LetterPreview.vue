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
        default: 'a4', // 'a4' | 'f4'
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
        kabupaten: s.kabupaten || '',
        kecamatan: s.kecamatan || '',
        nama_desa: s.nama_desa || '',
        alamat: s.alamat || '',
        kode_pos: s.kode_pos || '',
        kontak: s.kontak || '',
        email_desa: s.email_desa || '',
        logo_kiri: s.logo_kiri || null,
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

const tanggalFormatted = computed(() => formatTanggal(l.value.tanggal_surat));
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
        class="letter-scale-wrap"
        :style="{
            height: scaled ? `${scaledHeight}px` : 'auto',
        }"
    >
        <div
            class="print-sheet relative mx-auto bg-white text-black overflow-hidden"
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
                transformOrigin: 'top center',
                padding: props.compact ? '25mm 20mm 20mm 25mm' : '40mm 30mm 30mm 40mm',
                boxShadow: '0 10px 30px -12px rgba(0,0,0,0.35)',
                boxSizing: 'border-box',
            }"
        >
            <!-- Watermark / Background Image Transparan di Tengah Halaman Surat -->
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
                    <div class="flex items-center gap-4">
                        <div class="w-[28mm] shrink-0">
                            <img
                                v-if="l.logo_kiri"
                                :src="l.logo_kiri"
                                class="h-[25mm] w-full object-contain"
                                alt=""
                            />
                            <div
                                v-else
                                class="flex h-[25mm] w-full items-center justify-center rounded border border-dashed border-gray-400 text-[8.5pt] font-semibold leading-tight text-gray-600 print:hidden"
                            >
                                Logo<br />Kabupaten
                            </div>
                        </div>

                        <div class="min-w-0 flex-1 text-center leading-snug">
                            <div
                                class="font-bold uppercase"
                                :style="{ fontSize: compact ? '11.5pt' : '12.5pt' }"
                            >
                                Pemerintah Kabupaten {{ l.kabupaten }}
                            </div>
                            <div
                                class="font-bold uppercase"
                                :style="{ fontSize: compact ? '9.5pt' : '10.5pt' }"
                            >
                                Kecamatan {{ l.kecamatan }}
                            </div>
                            <div
                                class="mt-0.5 font-bold uppercase tracking-wider"
                                :style="{ fontSize: compact ? '13.5pt' : '15pt' }"
                            >
                                Desa {{ l.nama_desa }}
                            </div>
                            <div :style="{ fontSize: compact ? '8.5pt' : '9pt' }">
                                Alamat : {{ l.alamat }}
                            </div>
                            <div :style="{ fontSize: compact ? '8.5pt' : '9pt' }">
                                <template v-if="l.kode_pos">
                                    Kode Pos {{ l.kode_pos }}
                                </template>
                                <template v-if="l.kontak">
                                    <span v-if="l.kode_pos">&nbsp;|&nbsp;</span>
                                    Telp/Hp {{ l.kontak }}
                                </template>
                                <template v-if="l.email_desa">
                                    <span v-if="l.kode_pos || l.kontak"
                                        >&nbsp;|&nbsp;</span
                                    >
                                    Email : {{ l.email_desa }}
                                </template>
                            </div>
                        </div>

                        <div class="w-[28mm] shrink-0">
                            <img
                                v-if="l.logo_kanan"
                                :src="l.logo_kanan"
                                class="h-[25mm] w-full object-contain"
                                alt=""
                            />
                            <div
                                v-else
                                class="flex h-[25mm] w-full items-center justify-center rounded border border-dashed border-gray-400 text-[8.5pt] font-semibold leading-tight text-gray-600 print:hidden"
                            >
                                Logo<br />Desa
                            </div>
                        </div>
                    </div>

                    <div class="mt-2 border-b-[3px] border-black" />
                    <div class="mt-[1.5px] border-b border-black" />
                </div>

                <!-- Badan Surat -->
                <div class="flex flex-1 flex-col" :style="{ paddingTop: compact ? '3mm' : '5mm' }">
                    <div class="flex-1 flex flex-col">
                        <div
                            class="text-right"
                            :style="{ fontSize: compact ? '9.5pt' : '10.5pt' }"
                        >
                            {{ l.nama_desa }}, {{ tanggalFormatted }}
                        </div>

                        <div
                            class="mt-2 grid"
                            :style="{
                                fontSize: compact ? '9.5pt' : '10.5pt',
                                gridTemplateColumns: '24mm 5mm 1fr',
                                rowGap: '1.5px',
                            }"
                        >
                            <span>Nomor</span>
                            <span>:</span>
                            <span>{{ l.nomor_surat }}</span>

                            <span>Lampiran</span>
                            <span>:</span>
                            <span>{{ l.lampiran || '-' }}</span>

                            <span>Perihal</span>
                            <span>:</span>
                            <span class="font-bold">{{ l.perihal }}</span>

                            <template v-if="l.sifat && l.sifat !== 'Biasa'">
                                <span>Sifat</span>
                                <span>:</span>
                                <span>{{ l.sifat }}</span>
                            </template>
                        </div>

                        <div
                            class="mt-3"
                            :style="{ fontSize: compact ? '9.5pt' : '10.5pt', lineHeight: '1.4' }"
                        >
                            <div>Kepada Yth.</div>
                            <div class="font-semibold">{{ l.tujuan }}</div>
                            <div>di</div>
                            <div>Tempat</div>
                        </div>

                        <!-- Isi Surat Rata Kanan Kiri (Justify) -->
                        <div
                            class="mt-3 space-y-2.5 text-justify"
                            :style="{ fontSize: compact ? '9.5pt' : '10.5pt', lineHeight: compact ? '1.4' : '1.55', textAlign: 'justify', textJustify: 'inter-word' }"
                        >
                            <p v-if="l.pembuka" class="text-justify" style="text-align: justify;">{{ l.pembuka }}</p>
                            <p v-if="l.isi_surat" class="whitespace-pre-line text-justify" style="text-align: justify;">{{ l.isi_surat }}</p>
                            <p v-if="l.penutup" class="text-justify" style="text-align: justify;">{{ l.penutup }}</p>
                        </div>

                        <!-- Tanda Tangan Digital Dinamis (Bisa 1, 2, atau Mengetahui di Bawah) -->
                        <div
                            class="shrink-0 space-y-2"
                            :class="compact ? 'mt-2 pt-1' : 'mt-4 pt-1'"
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
                                            class="relative mx-auto flex items-center justify-center my-0.5"
                                            :class="compact ? 'h-[15mm] w-[56mm]' : 'h-[19mm] w-[60mm]'"
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
                                                :class="compact ? 'max-h-[15mm] max-w-[54mm]' : 'max-h-[19mm] max-w-[58mm]'"
                                                alt=""
                                            />
                                        </div>

                                        <div class="font-extrabold underline tracking-wide" :style="{ fontSize: compact ? '10pt' : '11.5pt' }">
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
                                            class="relative mx-auto flex items-center justify-center my-0.5"
                                            :class="compact ? 'h-[15mm] w-[56mm]' : 'h-[19mm] w-[60mm]'"
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
                                                :class="compact ? 'max-h-[15mm] max-w-[54mm]' : 'max-h-[19mm] max-w-[58mm]'"
                                                alt=""
                                            />
                                        </div>

                                        <div class="font-extrabold underline tracking-wide" :style="{ fontSize: compact ? '10pt' : '11.5pt' }">
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
                                        class="relative mx-auto flex items-center justify-center my-0.5"
                                        :class="compact ? 'h-[15mm] w-[56mm]' : 'h-[19mm] w-[60mm]'"
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
                                            :class="compact ? 'max-h-[15mm] max-w-[54mm]' : 'max-h-[19mm] max-w-[58mm]'"
                                            alt=""
                                        />
                                    </div>

                                    <div class="font-extrabold underline tracking-wide" :style="{ fontSize: compact ? '10pt' : '11.5pt' }">
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
