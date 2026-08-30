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

        nama_kades:    s.nama_kades    || 'Suhendar',
        jabatan_kades: s.jabatan_kades || 'Kepala Desa Sirnaputra',

        nama_almarhum:          s.nama_almarhum          || '',
        nik_almarhum:           s.nik_almarhum           || '',
        jenis_kelamin_almarhum: s.jenis_kelamin_almarhum || 'Laki-Laki',
        tempat_lahir_almarhum:  s.tempat_lahir_almarhum  || '',
        tanggal_lahir_almarhum: s.tanggal_lahir_almarhum || '',
        agama_almarhum:         s.agama_almarhum         || 'Islam',
        alamat_almarhum:        s.alamat_almarhum        || '',
        rt_almarhum:            s.rt_almarhum            || '',
        rw_almarhum:            s.rw_almarhum            || '',
        dusun_almarhum:         s.dusun_almarhum         || '',

        hari_meninggal:    s.hari_meninggal    || 'selasa',
        tanggal_meninggal: s.tanggal_meninggal || '',
        tempat_meninggal:  s.tempat_meninggal  || 'Rumah',
        sebab_meninggal:   s.sebab_meninggal   || 'sakit',
        tpu_pemakaman:     s.tpu_pemakaman     || '',

        ahli_waris_list: Array.isArray(s.ahli_waris_list) ? s.ahli_waris_list : [],
        saksi_list:      Array.isArray(s.saksi_list) ? s.saksi_list : [],

        nama_yang_menyatakan:  s.nama_yang_menyatakan  || '',
        label_yang_menyatakan: s.label_yang_menyatakan || 'Ahli waris',

        no_reg_camat:  s.no_reg_camat  || '',
        jabatan_camat: s.jabatan_camat || 'Camat Cigalontang',
        nama_camat:    s.nama_camat    || '',

        tanda_tangan: s.tanda_tangan || null,
        stempel:      s.stempel      || null,
    };
});

// Format Kop
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

const tanggalSuratFormatted     = computed(() => formatTanggal(l.value.tanggal_surat));
const tanggalLahirAlmarhum      = computed(() => formatTanggal(l.value.tanggal_lahir_almarhum));
const tanggalMeninggalFormatted = computed(() => formatTanggal(l.value.tanggal_meninggal));

// Alamat Almarhum Formatted
const alamatAlmarhumFormatted = computed(() => {
    if (l.value.alamat_almarhum) return l.value.alamat_almarhum;
    const parts = [];
    if (l.value.dusun_almarhum) parts.push(`Kp ${l.value.dusun_almarhum}`);
    parts.push(`Rt ${l.value.rt_almarhum ? String(l.value.rt_almarhum).padStart(3,'0') : '...'}`);
    parts.push(`Rw ${l.value.rw_almarhum ? String(l.value.rw_almarhum).padStart(3,'0') : '...'}`);
    const desa = (l.value.nama_desa || '').trim();
    if (desa) parts.push(desa.toUpperCase().startsWith('DESA') ? desa : `Desa ${desa}`);
    const kec = (l.value.kecamatan || '').replace(/^KECAMATAN\s*/i, '');
    if (kec) parts.push(`Kecamatan ${kec}`);
    const kab = (l.value.kabupaten || '').replace(/^PEMERINTAH.*?KABUPATEN\s*/i, '');
    if (kab) parts.push(`Kabupaten ${kab}`);
    return parts.join(' ');
});

function formatTglItem(tgl) {
    if (!tgl) return '';
    return formatTanggal(tgl);
}
</script>

<template>
    <div ref="wrapper" class="letter-scale-wrap mx-auto relative" style="height: auto;">
        <div
            class="print-sheet auto-height relative mx-auto bg-white text-black"
            :class="[
                props.paper === 'a4' ? 'paper-a4' : 'paper-f4',
                { 'compact-mode': props.compact }
            ]"
            :style="{
                width: `${SHEET_W_MM}mm`,
                minHeight: `${heightMm}mm`,
                height: 'auto',
                fontFamily: SERIF,
                transform: scaled ? `scale(${scale})` : 'none',
                transformOrigin: 'top left',
                padding: compact ? '15mm 15mm 15mm 20mm' : '20mm 18mm 20mm 22mm',
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

            <div class="relative z-10 flex flex-col">

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
                    class="flex flex-col"
                    :style="{
                        paddingTop: compact ? '3mm' : '5mm',
                        fontSize:   compact ? '10pt' : '11pt',
                        lineHeight: compact ? '1.5'  : '1.6',
                    }"
                >
                    <!-- Judul -->
                    <div class="text-center" :style="{ marginBottom: compact ? '1mm' : '2mm' }">
                        <div class="font-bold underline tracking-wide"
                            :style="{ fontSize: compact ? '11pt' : '12pt' }">
                            SURAT KETERANGAN AHLI WARIS DAN SAKSI AHLI WARIS
                        </div>
                        <div :style="{ fontSize: compact ? '9pt' : '10pt' }">
                            Nomor &nbsp;: {{ l.nomor_surat || '.../050/170/Ds/...' }}
                        </div>
                    </div>

                    <!-- Pihak Menerangkan -->
                    <div class="text-justify" :style="{ marginTop: compact ? '3mm' : '4mm' }">
                        Yang bertanda tangan dibawah ini:
                    </div>
                    <div :style="{ marginTop: '1mm', paddingLeft: '8mm' }">
                        <table style="width:100%; border-collapse:collapse; line-height:1.6;">
                            <tbody>
                                <tr>
                                    <td style="width:36mm; vertical-align:top;">N a m a</td>
                                    <td style="width:6mm; vertical-align:top;">:</td>
                                    <td style="vertical-align:top; font-weight:500;">{{ l.nama_kades }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top;">Jabatan</td>
                                    <td style="vertical-align:top;">:</td>
                                    <td style="vertical-align:top; font-weight:500;">{{ l.jabatan_kades }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-justify underline font-medium" :style="{ marginTop: compact ? '2mm' : '3mm' }">
                        Menerangkan dengan sebenarnya bahwa:
                    </div>

                    <!-- Data Almarhum -->
                    <div :style="{ marginTop: '1mm', paddingLeft: '8mm' }">
                        <table style="width:100%; border-collapse:collapse; line-height:1.6;">
                            <tbody>
                                <tr>
                                    <td style="width:36mm; vertical-align:top;">N a m a</td>
                                    <td style="width:6mm; vertical-align:top;">:</td>
                                    <td style="vertical-align:top; font-weight:500;">{{ l.nama_almarhum }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top;">Nik</td>
                                    <td style="vertical-align:top;">:</td>
                                    <td style="vertical-align:top; font-weight:500;">{{ l.nik_almarhum }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top;">Jenis Kelamin</td>
                                    <td style="vertical-align:top;">:</td>
                                    <td style="vertical-align:top; font-weight:500;">{{ l.jenis_kelamin_almarhum }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top;">Tempat Tanggal Lahir</td>
                                    <td style="vertical-align:top;">:</td>
                                    <td style="vertical-align:top; font-weight:500;">
                                        {{ l.tempat_lahir_almarhum }}<template v-if="l.tempat_lahir_almarhum && l.tanggal_lahir_almarhum">, </template>{{ tanggalLahirAlmarhum }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top;">Agama</td>
                                    <td style="vertical-align:top;">:</td>
                                    <td style="vertical-align:top; font-weight:500;">{{ l.agama_almarhum }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top;">A l a m a t</td>
                                    <td style="vertical-align:top;">:</td>
                                    <td style="vertical-align:top; font-weight:500;">{{ alamatAlmarhumFormatted }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paragraf Meninggal -->
                    <div class="text-justify" :style="{ marginTop: compact ? '3mm' : '4mm', textJustify: 'inter-word' }">
                        Adalah benar penduduk {{ desaFormatted }} dan sudah meninggal dunia pada hari {{ l.hari_meninggal }} {{ tanggalMeninggalFormatted }} di {{ l.tempat_meninggal }} disebabkan {{ l.sebab_meninggal }}<template v-if="l.tpu_pemakaman"> dan telah dikebumikan di {{ l.tpu_pemakaman }}</template>. Selanjutnya, diterangkan bahwa daftar nama berikut merupakan ahli waris yang sah dari Almarhum {{ l.nama_almarhum }} dan kami bertanggung jawab atas pernyataan ini.
                    </div>

                    <!-- LIST NAMA AHLI WARIS (DINAMIS NUMBERED) -->
                    <div :style="{ marginTop: compact ? '3mm' : '4mm' }" class="space-y-3">
                        <div v-for="(item, idx) in l.ahli_waris_list" :key="idx" class="flex items-start gap-2">
                            <span class="font-bold min-w-[20px]">{{ idx + 1 }}.</span>
                            <div class="flex-1">
                                <table style="width:100%; border-collapse:collapse; line-height:1.6;">
                                    <tbody>
                                        <tr>
                                            <td style="width:34mm; vertical-align:top;">Nama</td>
                                            <td style="width:5mm; vertical-align:top;">:</td>
                                            <td style="vertical-align:top; font-weight:500;">{{ item.nama }}</td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align:top;">Tempat/Tgl Lahir</td>
                                            <td style="vertical-align:top;">:</td>
                                            <td style="vertical-align:top; font-weight:500;">
                                                {{ item.tempat_lahir }}<template v-if="item.tempat_lahir && item.tanggal_lahir">, </template>{{ formatTglItem(item.tanggal_lahir) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align:top;">Pekerjaan</td>
                                            <td style="vertical-align:top;">:</td>
                                            <td style="vertical-align:top; font-weight:500;">{{ item.pekerjaan }}</td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align:top;">Alamat</td>
                                            <td style="vertical-align:top;">:</td>
                                            <td style="vertical-align:top; font-weight:500;">{{ item.alamat }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TABEL AHlI WARIS (DINAMIS) -->
                    <div :style="{ marginTop: compact ? '4mm' : '6mm' }">
                        <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-size:9.5pt; text-align:left;">
                            <thead>
                                <tr style="background-color: #f3f4f6; border-bottom:1px solid #000;">
                                    <th style="border:1px solid #000; padding:2mm 2mm; text-align:center; width:10mm;">NO</th>
                                    <th style="border:1px solid #000; padding:2mm 2mm;">Nama</th>
                                    <th style="border:1px solid #000; padding:2mm 2mm;">Tempat/Tanggal Lahir</th>
                                    <th style="border:1px solid #000; padding:2mm 2mm;">NIK</th>
                                    <th style="border:1px solid #000; padding:2mm 2mm;">Hubungan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, idx) in l.ahli_waris_list" :key="idx" style="border-bottom:1px solid #000;">
                                    <td style="border:1px solid #000; padding:2mm 2mm; text-align:center; font-weight:bold;">{{ idx + 1 }}</td>
                                    <td style="border:1px solid #000; padding:2mm 2mm; font-weight:bold; text-transform:uppercase;">{{ item.nama }}</td>
                                    <td style="border:1px solid #000; padding:2mm 2mm;">
                                        {{ item.tempat_lahir }}<template v-if="item.tempat_lahir && item.tanggal_lahir">, </template>{{ formatTglItem(item.tanggal_lahir) }}
                                    </td>
                                    <td style="border:1px solid #000; padding:2mm 2mm; font-family:monospace;">{{ item.nik }}</td>
                                    <td style="border:1px solid #000; padding:2mm 2mm;">{{ item.hubungan }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Penutup -->
                    <div class="text-justify" :style="{ marginTop: compact ? '3mm' : '5mm' }">
                        Demikian Surat keterangan ini dibuat dan diberikan kepada yang bersangkutan untuk dipergunakan sebagaimana mestinya.
                    </div>

                    <!-- TTD YANG MENYATAKAN (AHLI WARIS / PEMOHON) -->
                    <div class="flex justify-end" :style="{ marginTop: compact ? '4mm' : '6mm' }">
                        <div class="text-center" style="width: 75mm;">
                            <div>{{ l.kabupaten || 'Tasikmalaya' }}, {{ tanggalSuratFormatted }}</div>
                            <div class="underline">Yang menyatakan,</div>
                            <div class="font-bold">{{ l.label_yang_menyatakan }}</div>
                            <div class="my-3 flex items-center justify-center">
                                <span class="rounded border border-gray-400 px-2 py-1 text-[8pt] text-gray-500">Materai 10.000</span>
                            </div>
                            <div class="font-bold underline uppercase tracking-wide">
                                {{ l.nama_yang_menyatakan || (l.ahli_waris_list[0]?.nama ?? '') }}
                            </div>
                        </div>
                    </div>

                    <!-- SAKSI - SAKSI (DINAMIS) -->
                    <div :style="{ marginTop: compact ? '3mm' : '5mm' }">
                        <div class="font-bold underline mb-2">Saksi – Saksi</div>
                        <div class="space-y-3">
                            <div v-for="(saksi, idx) in l.saksi_list" :key="idx" class="flex items-center gap-2">
                                <span class="min-w-[20px] font-bold">{{ idx + 1 }}.</span>
                                <span class="font-bold underline min-w-[160px]">{{ saksi.nama }}</span>
                                <span>( ................... )</span>
                            </div>
                        </div>
                    </div>

                    <!-- PENGESAHAN DUA KOLOM (CAMAT & KADES) -->
                    <div class="shrink-0 grid grid-cols-2 gap-4 border border-dashed border-gray-300 p-4 print:border-gray-400"
                        :style="{ marginTop: compact ? '6mm' : '10mm' }">
                        <!-- Kolom Kiri: Camat -->
                        <div class="text-left">
                            <div class="text-xs">No Reg :</div>
                            <div class="font-bold mt-1">{{ l.jabatan_camat }}</div>
                            <div class="h-16 flex items-end">
                                <span>...........................</span>
                            </div>
                            <div v-if="l.nama_camat" class="font-bold underline mt-1">{{ l.nama_camat }}</div>
                        </div>

                        <!-- Kolom Kanan: Kades -->
                        <div class="text-center relative">
                            <div class="font-bold">{{ l.jabatan_kades }}</div>
                            <div class="relative mx-auto flex h-16 items-center justify-center my-1">
                                <img v-if="l.stempel" :src="l.stempel"
                                    class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 object-contain pointer-events-none"
                                    style="height:26mm; width:26mm; opacity:0.85;" alt="" />
                                <img v-if="l.tanda_tangan" :src="l.tanda_tangan"
                                    class="relative z-10 object-contain max-h-14" alt="" />
                            </div>
                            <div class="font-bold underline uppercase tracking-wide">{{ l.nama_kades }}</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
