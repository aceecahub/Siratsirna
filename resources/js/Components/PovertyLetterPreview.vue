<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { formatTanggal } from '@/lib/format';

const props = defineProps({
    letter: { type: Object, required: true },
    paper:  { type: String, default: 'f4', validator: (v) => ['a4', 'f4'].includes(v) },
    scaled: { type: Boolean, default: false },
    compact:{ type: Boolean, default: false },
});

const SERIF      = "'Times New Roman', Times, serif";
const SHEET_W_MM = 210;
const A4_H_MM    = 297;
const F4_H_MM    = 330;
const MM_TO_PX   = 96 / 25.4;

const heightMm       = computed(() => (props.paper === 'a4' ? A4_H_MM : F4_H_MM));
const sheetWidthPx   = SHEET_W_MM * MM_TO_PX;
const sheetHeightPx  = computed(() => heightMm.value * MM_TO_PX);
const scaledHeight   = computed(() => sheetHeightPx.value * scale.value);

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
        nama_warga:    s.nama_warga    || '',
        tempat_lahir:  s.tempat_lahir  || '',
        tanggal_lahir: s.tanggal_lahir || '',
        pekerjaan:     s.pekerjaan     || '',
        alamat_warga:  s.alamat_warga  || '',
        rt:            s.rt            || '',
        rw:            s.rw            || '',
        dusun:         s.dusun         || '',
        keterangan:    s.keterangan    || '',
        stempel:       s.stempel       || null,
        penandatangan_list: s.penandatangan_list || [],
    };
});

// Formatted helpers
const kabupatenFormatted = computed(() => {
    const raw = (l.value.kabupaten || '').trim().toUpperCase();
    if (!raw) return 'PEMERINTAH DAERAH KABUPATEN TASIKMALAYA';
    if (raw.startsWith('PEMERINTAH')) return raw;
    if (raw.startsWith('KABUPATEN')) return `PEMERINTAH DAERAH ${raw}`;
    return `PEMERINTAH DAERAH KABUPATEN ${raw}`;
});
const kecamatanFormatted = computed(() => {
    const raw = (l.value.kecamatan || '').trim().toUpperCase();
    if (!raw) return 'KECAMATAN CIGALONTANG';
    return raw.startsWith('KECAMATAN') ? raw : `KECAMATAN ${raw}`;
});
const desaFormatted = computed(() => {
    const raw = (l.value.nama_desa || '').trim().toUpperCase();
    if (!raw) return 'DESA SIRNAPUTRA';
    return raw.startsWith('DESA') ? raw : `DESA ${raw}`;
});

const tanggalSuratFormatted = computed(() => formatTanggal(l.value.tanggal_surat));
const tanggalLahirFormatted = computed(() => formatTanggal(l.value.tanggal_lahir));

// Auto-build alamat warga
const alamatWargaFormatted = computed(() => {
    if (l.value.alamat_warga) return l.value.alamat_warga;
    const parts = [];
    if (l.value.dusun) parts.push(`Kp. ${l.value.dusun}`);
    if (l.value.rt)    parts.push(`RT ${String(l.value.rt).padStart(3,'0')}`);
    if (l.value.rw)    parts.push(`RW ${String(l.value.rw).padStart(3,'0')}`);
    const desa = (l.value.nama_desa || '').trim();
    if (desa) parts.push(desa.toUpperCase().startsWith('DESA') ? desa : `Desa ${desa}`);
    const kec = (l.value.kecamatan || '').replace(/^KECAMATAN\s*/i, '');
    if (kec) parts.push(`Kecamatan ${kec}`);
    const kab = (l.value.kabupaten || '').replace(/^PEMERINTAH.*?KABUPATEN\s*/i, '');
    if (kab) parts.push(`Kabupaten ${kab}`);
    return parts.join(' ');
});

// Teks isi surat default (sama persis dengan format gambar)
const keteranganText = computed(() => {
    if (l.value.keterangan) return l.value.keterangan;
    return `Orang tersebut diatas betul penduduk/Warga Desa Kami dan bertempat tinggal dialamat tersebut diatas.\n\nKemudian Kami terangkan pula bahwa berdasarkan keterangan dari Ketua RT/RW setempat Orang tersebut di atas adalah keluarga tidak mampu dan sedang akan mengintegrasikan Kartu BPJS Mandiri Ke BPJS PBI, maka dengan ini kami mohon agar dapat merealisasikannya.`;
});

// Split keterangan menjadi paragraf
const keteranganParagraphs = computed(() =>
    keteranganText.value.split('\n\n').map(p => p.trim()).filter(Boolean)
);

// Penandatangan grouped
const leftSigners    = computed(() => l.value.penandatangan_list.filter(s => s.peran === 'kiri'));
const rightSigners   = computed(() => l.value.penandatangan_list.filter(s => s.peran === 'kanan'));
const centerSigners  = computed(() => l.value.penandatangan_list.filter(s => s.peran === 'mengetahui'));
const hasSigners     = computed(() => l.value.penandatangan_list.length > 0);
</script>

<template>
    <div
        ref="wrapper"
        class="letter-scale-wrap mx-auto relative"
        :style="{ height: scaled ? `${scaledHeight}px` : 'auto',
            width: scaled ? `${scaledWidth}px` : '100%', }"
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
                padding: compact ? '15mm 15mm 12mm 20mm' : '20mm 18mm 18mm 22mm',
                boxShadow: '0 10px 30px -12px rgba(0,0,0,0.35)',
                boxSizing: 'border-box',
            }"
        >
            <!-- Watermark stempel -->
            <div v-if="l.stempel"
                class="pointer-events-none absolute inset-0 z-0 flex items-center justify-center overflow-hidden">
                <img :src="l.stempel" class="watermark-img h-[110mm] w-[110mm] object-contain"
                    style="opacity: 0.07 !important;" alt="" />
            </div>

            <div class="relative z-10 flex h-full flex-col">

                <!-- ── KOP SURAT ───────────────────────────────── -->
                <div class="shrink-0">
                    <div class="flex items-center">
                        <!-- Logo Kiri -->
                        <div class="w-[26mm] shrink-0 flex justify-center">
                            <img v-if="l.logo_kiri" :src="l.logo_kiri"
                                class="h-[24mm] w-full object-contain" alt="" />
                            <div v-else
                                class="flex h-[24mm] w-full items-center justify-center rounded border border-dashed border-gray-400 text-[7pt] text-gray-400 print:hidden leading-tight text-center">
                                Logo<br/>Kab
                            </div>
                        </div>

                        <!-- Teks Kop -->
                        <div class="flex-1 text-center leading-snug"
                            :class="{ 'pr-[26mm]': !l.logo_kanan }">
                            <div class="font-bold uppercase tracking-tight"
                                :style="{ fontSize: compact ? '11pt' : '12pt' }">
                                {{ kabupatenFormatted }}
                            </div>
                            <div class="font-bold uppercase tracking-tight mt-0.5"
                                :style="{ fontSize: compact ? '10pt' : '11pt' }">
                                {{ kecamatanFormatted }}
                            </div>
                            <div class="font-bold uppercase tracking-wider mt-0.5"
                                :style="{ fontSize: compact ? '13pt' : '15pt' }">
                                {{ desaFormatted }}
                            </div>
                            <div class="mt-1" :style="{ fontSize: compact ? '8pt' : '8.5pt' }">
                                <span v-if="l.alamat">Kantor : {{ l.alamat }}</span>
                            </div>
                            <div :style="{ fontSize: compact ? '8pt' : '8.5pt' }">
                                <template v-if="l.email_desa">{{ l.email_desa }}</template>
                                <template v-if="l.email_desa && l.kontak"> | </template>
                                <template v-if="l.kontak">Telp : {{ l.kontak }}</template>
                            </div>
                            <div :style="{ fontSize: compact ? '8pt' : '8.5pt' }">
                                <template v-if="l.kode_pos">{{ l.kode_pos }}</template>
                            </div>
                        </div>

                        <!-- Logo Kanan -->
                        <div v-if="l.logo_kanan" class="w-[26mm] shrink-0 flex justify-center">
                            <img :src="l.logo_kanan" class="h-[24mm] w-full object-contain" alt="" />
                        </div>
                    </div>

                    <!-- Garis Kop Double -->
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
                            SURAT KETERANGAN TIDAK MAMPU
                        </div>
                        <div :style="{ fontSize: compact ? '9pt' : '10pt' }">
                            Nomor &nbsp;: {{ l.nomor_surat || '.../Ds/...' }}
                        </div>
                    </div>

                    <!-- Pembuka -->
                    <div
                        class="text-justify"
                        :style="{ marginTop: compact ? '4mm' : '6mm', textJustify: 'inter-word' }"
                    >
                        Yang bertandatangan dibawah ini Kepala {{ desaFormatted }},
                        {{ kecamatanFormatted }}, {{ kabupatenFormatted }},
                        dengan ini menerangkan bahwa&nbsp;:
                    </div>

                    <!-- Data Warga -->
                    <div
                        :style="{
                            marginTop: compact ? '3mm' : '5mm',
                            paddingLeft: '8mm',
                        }"
                    >
                        <table style="width:100%; border-collapse:collapse; line-height:1.7;">
                            <tbody>
                                <tr>
                                    <td style="width:48mm; vertical-align:top; padding:0.4mm 0;">N a m a</td>
                                    <td style="width:6mm; vertical-align:top; padding:0.4mm 0;">:</td>
                                    <td style="vertical-align:top; padding:0.4mm 0; font-weight:500;">{{ l.nama_warga }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; padding:0.4mm 0;">Tempat Tgl Lahir</td>
                                    <td style="vertical-align:top; padding:0.4mm 0;">:</td>
                                    <td style="vertical-align:top; padding:0.4mm 0; font-weight:500;">
                                        {{ l.tempat_lahir }}, {{ tanggalLahirFormatted }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; padding:0.4mm 0;">P e k e r j a a n</td>
                                    <td style="vertical-align:top; padding:0.4mm 0;">:</td>
                                    <td style="vertical-align:top; padding:0.4mm 0; font-weight:500;">{{ l.pekerjaan }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; padding:0.4mm 0;">A l a m a t</td>
                                    <td style="vertical-align:top; padding:0.4mm 0;">:</td>
                                    <td style="vertical-align:top; padding:0.4mm 0; font-weight:500; white-space:pre-line;">{{ alamatWargaFormatted }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Body surat -->
                    <div v-for="(para, idx) in keteranganParagraphs" :key="idx"
                        class="text-justify"
                        :style="{
                            marginTop: compact ? '3mm' : '5mm',
                            textJustify: 'inter-word',
                        }">
                        &nbsp;&nbsp;&nbsp;&nbsp;{{ para }}
                    </div>

                    <!-- Penutup -->
                    <div
                        class="text-justify"
                        :style="{ marginTop: compact ? '3mm' : '5mm', textJustify: 'inter-word' }"
                    >
                        &nbsp;&nbsp;&nbsp;&nbsp;Demikian keterangan ini Kami buat agar dijadikan bahan seperlunya.
                    </div>

                    <!-- ── TANDA TANGAN (3 kolom) ────────────────── -->
                    <div
                        class="shrink-0"
                        :style="{ marginTop: compact ? '5mm' : '8mm' }"
                    >
                        <!-- Tanggal rata kanan -->
                        <div class="text-right" :style="{ marginBottom: compact ? '2mm' : '3mm' }">
                            {{ l.nama_desa }}, {{ tanggalSuratFormatted }}
                        </div>

                        <!-- 3-kolom TTD -->
                        <div class="flex w-full" v-if="hasSigners">
                            <!-- Kolom Kiri -->
                            <div class="flex-1 text-center">
                                <template v-for="(signer, idx) in leftSigners" :key="idx">
                                    <div v-if="signer.label_mengetahui" class="font-medium"
                                        :style="{ fontSize: compact ? '9pt' : '10pt' }">
                                        {{ signer.label_mengetahui }}
                                    </div>
                                    <div class="font-bold" :style="{ fontSize: compact ? '9pt' : '10pt' }">
                                        {{ signer.jabatan }}
                                    </div>
                                    <!-- Area TTD -->
                                    <div class="relative mx-auto flex items-center justify-center"
                                        :style="{ height: compact ? '16mm' : '22mm', width: '90%' }">
                                        <img v-if="signer.stempel" :src="signer.stempel"
                                            class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 object-contain pointer-events-none"
                                            :style="{ height: compact ? '22mm' : '28mm', width: compact ? '22mm' : '28mm', opacity: '0.85' }"
                                            alt="" />
                                        <img v-if="signer.tanda_tangan" :src="signer.tanda_tangan"
                                            class="relative z-10 object-contain"
                                            :style="{ maxHeight: compact ? '14mm' : '20mm', maxWidth: '88%' }"
                                            alt="" />
                                    </div>
                                    <div class="font-extrabold underline uppercase"
                                        :style="{ fontSize: compact ? '9.5pt' : '10.5pt' }">
                                        {{ signer.nama }}
                                    </div>
                                    <div v-if="signer.nip_niapd" class="font-medium mt-0.5"
                                        :style="{ fontSize: compact ? '8pt' : '9pt' }">
                                        NIP. {{ signer.nip_niapd }}
                                    </div>
                                </template>
                                <!-- Placeholder kiri -->
                                <template v-if="leftSigners.length === 0">
                                    <div class="text-gray-400 text-[8pt] print:hidden">Penandatangan Kiri</div>
                                </template>
                            </div>

                            <!-- Kolom Tengah (Mengetahui) -->
                            <div class="flex-1 text-center">
                                <template v-if="centerSigners.length > 0">
                                    <template v-for="(signer, idx) in centerSigners" :key="idx">
                                        <div v-if="signer.label_mengetahui" class="font-medium"
                                            :style="{ fontSize: compact ? '9pt' : '10pt' }">
                                            {{ signer.label_mengetahui }}
                                        </div>
                                        <div class="font-bold" :style="{ fontSize: compact ? '9pt' : '10pt' }">
                                            {{ signer.jabatan }}
                                        </div>
                                        <div class="relative mx-auto flex items-center justify-center"
                                            :style="{ height: compact ? '16mm' : '22mm', width: '90%' }">
                                            <img v-if="signer.stempel" :src="signer.stempel"
                                                class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 object-contain pointer-events-none"
                                                :style="{ height: compact ? '22mm' : '28mm', width: compact ? '22mm' : '28mm', opacity: '0.85' }"
                                                alt="" />
                                            <img v-if="signer.tanda_tangan" :src="signer.tanda_tangan"
                                                class="relative z-10 object-contain"
                                                :style="{ maxHeight: compact ? '14mm' : '20mm', maxWidth: '88%' }"
                                                alt="" />
                                        </div>
                                        <div class="font-extrabold underline uppercase"
                                            :style="{ fontSize: compact ? '9.5pt' : '10.5pt' }">
                                            {{ signer.nama }}
                                        </div>
                                        <div v-if="signer.nip_niapd" class="font-medium mt-0.5"
                                            :style="{ fontSize: compact ? '8pt' : '9pt' }">
                                            NIP. {{ signer.nip_niapd }}
                                        </div>
                                    </template>
                                </template>
                                <!-- Jika tidak ada center signer, tampilkan watermark stempel -->
                                <template v-else-if="l.stempel">
                                    <div :style="{ height: compact ? '22mm' : '28mm' }" class="flex items-center justify-center">
                                        <img :src="l.stempel" class="object-contain"
                                            :style="{ height: compact ? '20mm' : '26mm', opacity: '0.6' }"
                                            alt="" />
                                    </div>
                                </template>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="flex-1 text-center">
                                <template v-for="(signer, idx) in rightSigners" :key="idx">
                                    <div v-if="signer.label_mengetahui" class="font-medium"
                                        :style="{ fontSize: compact ? '9pt' : '10pt' }">
                                        {{ signer.label_mengetahui }}
                                    </div>
                                    <div class="font-bold" :style="{ fontSize: compact ? '9pt' : '10pt' }">
                                        {{ signer.jabatan }}
                                    </div>
                                    <div class="relative mx-auto flex items-center justify-center"
                                        :style="{ height: compact ? '16mm' : '22mm', width: '90%' }">
                                        <img v-if="signer.stempel" :src="signer.stempel"
                                            class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 object-contain pointer-events-none"
                                            :style="{ height: compact ? '22mm' : '28mm', width: compact ? '22mm' : '28mm', opacity: '0.85' }"
                                            alt="" />
                                        <img v-if="signer.tanda_tangan" :src="signer.tanda_tangan"
                                            class="relative z-10 object-contain"
                                            :style="{ maxHeight: compact ? '14mm' : '20mm', maxWidth: '88%' }"
                                            alt="" />
                                    </div>
                                    <div class="font-extrabold underline uppercase"
                                        :style="{ fontSize: compact ? '9.5pt' : '10.5pt' }">
                                        {{ signer.nama }}
                                    </div>
                                    <div v-if="signer.nip_niapd" class="font-medium mt-0.5"
                                        :style="{ fontSize: compact ? '8pt' : '9pt' }">
                                        NIP. {{ signer.nip_niapd }}
                                    </div>
                                </template>
                                <!-- Placeholder kanan -->
                                <template v-if="rightSigners.length === 0">
                                    <div class="text-gray-400 text-[8pt] print:hidden">Penandatangan Kanan</div>
                                </template>
                            </div>
                        </div>

                        <!-- Fallback: tidak ada signer -->
                        <div v-else class="text-center text-[8pt] text-gray-400 print:hidden py-4">
                            Belum ada penandatangan. Tambahkan di form.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
