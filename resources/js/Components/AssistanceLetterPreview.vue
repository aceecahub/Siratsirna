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

        nomor_surat:    s.nomor_surat    || '',
        tanggal_surat:  s.tanggal_surat  || '',
        sifat_surat:    s.sifat_surat    || 'Penting',
        lampiran_surat: s.lampiran_surat || '1 Set',
        perihal_surat:  s.perihal_surat  || '',

        tujuan_nama: s.tujuan_nama || 'Bupati tasikmalaya',
        tujuan_cq:   s.tujuan_cq   || 'Cq. Dinas Sosial PPKB,P3A',
        tujuan_di:   s.tujuan_di   || 'Singaparna, Tasikmalaya',

        no_kk:         s.no_kk         || '',
        nik:           s.nik           || '',
        nama_pasien:   s.nama_pasien   || '',
        jenis_kelamin: s.jenis_kelamin || 'Perempuan',
        tempat_lahir:  s.tempat_lahir  || '',
        tanggal_lahir: s.tanggal_lahir || '',
        pekerjaan:     s.pekerjaan     || 'Belum/Tidak Bekerja',
        alamat_pasien: s.alamat_pasien || '',
        rt:            s.rt            || '',
        rw:            s.rw            || '',
        dusun:         s.dusun         || '',
        id_dtks:       s.id_dtks       || '-',

        teks_permohonan: s.teks_permohonan || '',
        lampiran_list:   Array.isArray(s.lampiran_list) ? s.lampiran_list : [],
        kalimat_penutup: s.kalimat_penutup || '',

        jabatan_ttd_kiri: s.jabatan_ttd_kiri || 'Pendamping PKH /TKSK Kec.Cigalontang',
        nama_ttd_kiri:    s.nama_ttd_kiri    || '',

        label_ttd_tengah:   s.label_ttd_tengah   || 'Mengetahui,',
        jabatan_ttd_tengah: s.jabatan_ttd_tengah || 'Camat Cigalontang',
        nama_ttd_tengah:    s.nama_ttd_tengah    || '',

        label_ttd_kanan:   s.label_ttd_kanan   || '',
        jabatan_ttd_kanan: s.jabatan_ttd_kanan || 'Kepala Desa Sirnaputra',
        nama_ttd_kanan:    s.nama_ttd_kanan    || 'Suhendar',

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

const tanggalSuratFormatted = computed(() => formatTanggal(l.value.tanggal_surat));
const tanggalLahirFormatted = computed(() => formatTanggal(l.value.tanggal_lahir));

// Alamat pasien formatted
const alamatPasienFormatted = computed(() => {
    if (l.value.alamat_pasien) return l.value.alamat_pasien;
    const parts = [];
    if (l.value.dusun) parts.push(`Kp.${l.value.dusun}`);
    parts.push(`RT ${l.value.rt ? String(l.value.rt).padStart(3,'0') : '...'}`);
    parts.push(`RW ${l.value.rw ? String(l.value.rw).padStart(3,'0') : '...'}`);
    const desa = (l.value.nama_desa || '').trim();
    if (desa) parts.push(desa.toUpperCase().startsWith('DESA') ? desa : `Desa ${desa}`);
    const kec = (l.value.kecamatan || '').replace(/^KECAMATAN\s*/i, '');
    if (kec) parts.push(`Kecamatan ${kec}`);
    const kab = (l.value.kabupaten || '').replace(/^PEMERINTAH.*?KABUPATEN\s*/i, '');
    if (kab) parts.push(`Kabupaten ${kab}`);
    return parts.join(' ');
});

const defaultPerihal = computed(() => {
    if (l.value.perihal_surat) return l.value.perihal_surat;
    return `Permohonan Bantuan Biaya Pengobatan a/n ${l.value.nama_pasien}`;
});

const defaultTeksPermohonan = computed(() => {
    if (l.value.teks_permohonan) return l.value.teks_permohonan;
    return 'Dengan ini Kami mengajukan permohonan Bantuan Biaya untuk perawatan dan pengobatan pasien yang tidak ditanggung oleh Program Jamkesmas maupun Jamkesda.';
});

const defaultLampiranList = computed(() => {
    if (l.value.lampiran_list && l.value.lampiran_list.length > 0) {
        return l.value.lampiran_list;
    }
    return [
        'Surat keterangan tidak mampu ( diketahui oleh Camat )',
        'Fotocopy KTP',
        'Fotocopy Kartu Keluarga',
        'Diagnosa/Surat Rujukan',
    ];
});

const defaultKalimatPenutup = computed(() => {
    if (l.value.kalimat_penutup) return l.value.kalimat_penutup;
    return 'Demikian surat keterangan ini kami buat dengan sebenarnya agar yang berkepentingan mengetahui dan maklum hendaknya serta untuk di pergunakan sebagai mana mestinya. atas terkabulnya permohonan ini Kami sangat haturkan banyak terima kasih.';
});
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
                padding: compact ? '15mm 15mm 12mm 20mm' : '20mm 18mm 16mm 22mm',
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
                                <template v-if="l.alamat">Kantor : Jln.{{ l.alamat }}</template>
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
                        paddingTop: compact ? '3mm' : '4mm',
                        fontSize:   compact ? '9.5pt' : '10.5pt',
                        lineHeight: compact ? '1.5'   : '1.6',
                    }"
                >
                    <!-- Header Grid (Kiri: No/Sifat/Lam/Perihal, Kanan: Kepada Yth) -->
                    <div class="grid grid-cols-12 gap-2" :style="{ marginBottom: compact ? '2mm' : '3mm' }">
                        <!-- Left Column (7 cols) -->
                        <div class="col-span-7">
                            <table style="width:100%; border-collapse:collapse; line-height:1.5;">
                                <tbody>
                                    <tr>
                                        <td style="width:18mm; vertical-align:top;">No</td>
                                        <td style="width:4mm; vertical-align:top;">:</td>
                                        <td style="vertical-align:top; font-weight:500;">{{ l.nomor_surat || '.../475/Ds/...' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align:top;" class="underline">Sifat</td>
                                        <td style="vertical-align:top;">:</td>
                                        <td style="vertical-align:top;">{{ l.sifat_surat }}</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align:top;">Lam</td>
                                        <td style="vertical-align:top;">:</td>
                                        <td style="vertical-align:top;">{{ l.lampiran_surat }}</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align:top;" class="underline">Perihal</td>
                                        <td style="vertical-align:top;">:</td>
                                        <td style="vertical-align:top; font-weight:500;">
                                            {{ defaultPerihal }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Right Column (5 cols) -->
                        <div class="col-span-5 text-left" style="padding-left: 5mm;">
                            <div>{{ l.kabupaten || 'Tasikmalaya' }}, {{ tanggalSuratFormatted }}</div>
                            <div class="underline">Kepada Yth.</div>
                            <div class="font-bold underline">{{ l.tujuan_nama }}</div>
                            <div v-if="l.tujuan_cq">{{ l.tujuan_cq }}</div>
                            <div>Di</div>
                            <div class="underline pl-3">{{ l.tujuan_di }}</div>
                        </div>
                    </div>

                    <!-- Arabic Salam Opening -->
                    <div class="text-center font-bold my-2" style="font-size: 15pt; font-family: 'Amiri', 'Traditional Arabic', serif;">
                        السلام عليكم ورحمة الله وبركاته
                    </div>

                    <!-- Paragraf Pembuka -->
                    <div class="text-justify" :style="{ textJustify: 'inter-word' }">
                        &nbsp;&nbsp;&nbsp;&nbsp;{{ defaultTeksPermohonan }}
                    </div>

                    <!-- Identitas Pasien Table -->
                    <div :style="{ marginTop: compact ? '2mm' : '3mm', paddingLeft: '8mm' }">
                        <div class="underline font-medium mb-1">Identitas Pasien adalah sebagai berikut :</div>
                        <table style="width:100%; border-collapse:collapse; line-height:1.6;">
                            <tbody>
                                <tr v-if="l.no_kk">
                                    <td style="width:38mm; vertical-align:top;">NO KK</td>
                                    <td style="width:5mm; vertical-align:top;">:</td>
                                    <td style="vertical-align:top; font-weight:500;">{{ l.no_kk }}</td>
                                </tr>
                                <tr v-if="l.nik">
                                    <td style="vertical-align:top;">NIK</td>
                                    <td style="vertical-align:top;">:</td>
                                    <td style="vertical-align:top; font-weight:500;">{{ l.nik }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top;">N a m a</td>
                                    <td style="vertical-align:top;">:</td>
                                    <td style="vertical-align:top; font-weight:bold;">{{ l.nama_pasien }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top;" class="underline">Jenis Kelamin</td>
                                    <td style="vertical-align:top;">:</td>
                                    <td style="vertical-align:top; font-weight:500;">{{ l.jenis_kelamin }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top;" class="underline">Tempat Tgl Lahir</td>
                                    <td style="vertical-align:top;">:</td>
                                    <td style="vertical-align:top; font-weight:500;">
                                        {{ l.tempat_lahir }}, {{ tanggalLahirFormatted }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top;" class="underline">Pekerjaan</td>
                                    <td style="vertical-align:top;">:</td>
                                    <td style="vertical-align:top; font-weight:500;">{{ l.pekerjaan }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top;">A l a m a t</td>
                                    <td style="vertical-align:top;">:</td>
                                    <td style="vertical-align:top; font-weight:500;">{{ alamatPasienFormatted }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top;">ID DTKS</td>
                                    <td style="vertical-align:top;">:</td>
                                    <td style="vertical-align:top; font-weight:500;">{{ l.id_dtks }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Lampiran Pertimbangan -->
                    <div :style="{ marginTop: compact ? '2mm' : '3mm' }">
                        <div class="underline font-medium mb-1">Sebagai bahan pertimbangan Bapak,Kami lampirkan :</div>
                        <div class="pl-4 space-y-0.5">
                            <div v-for="(item, idx) in defaultLampiranList" :key="idx" class="flex items-start gap-2">
                                <span class="min-w-[15px] font-bold">{{ idx + 1 }}.</span>
                                <span>{{ item }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Paragraf Penutup -->
                    <div class="text-justify" :style="{ marginTop: compact ? '2mm' : '3mm', textJustify: 'inter-word' }">
                        &nbsp;&nbsp;&nbsp;&nbsp;{{ defaultKalimatPenutup }}
                    </div>

                    <!-- Arabic Salam Closing -->
                    <div class="text-center font-bold my-2" style="font-size: 15pt; font-family: 'Amiri', 'Traditional Arabic', serif;">
                        والسلام عليكم ورحمة الله وبركاته
                    </div>

                    <!-- ── TANDA TANGAN 3 KOLOM ───────────────────────── -->
                    <div class="shrink-0 grid grid-cols-3 gap-2 text-center" :style="{ marginTop: compact ? '3mm' : '5mm' }">

                        <!-- Kolom Kiri: PKH / TKSK -->
                        <div class="flex flex-col justify-between" style="min-height: 38mm;">
                            <div>
                                <div class="font-medium">{{ l.jabatan_ttd_kiri }}</div>
                            </div>
                            <div>
                                <div>( ........................ )</div>
                                <div v-if="l.nama_ttd_kiri" class="font-bold underline mt-0.5">{{ l.nama_ttd_kiri }}</div>
                            </div>
                        </div>

                        <!-- Kolom Tengah: Camat -->
                        <div class="flex flex-col justify-between" style="min-height: 38mm;">
                            <div>
                                <div v-if="l.label_ttd_tengah">{{ l.label_ttd_tengah }}</div>
                                <div class="font-bold underline">{{ l.jabatan_ttd_tengah }}</div>
                            </div>
                            <div>
                                <div v-if="!l.nama_ttd_tengah">( ........................ )</div>
                                <div v-else class="font-bold underline">{{ l.nama_ttd_tengah }}</div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Kepala Desa -->
                        <div class="flex flex-col justify-between" style="min-height: 38mm;">
                            <div>
                                <div>{{ l.nama_desa || 'Sirnaputra' }}, {{ tanggalSuratFormatted }}</div>
                                <div class="font-bold underline">{{ l.jabatan_ttd_kanan || 'Kepala Desa Sirnaputra' }}</div>
                            </div>

                            <!-- Area TTD + Stempel -->
                            <div class="relative mx-auto flex items-center justify-center my-1"
                                :style="{ height: '18mm', width: '50mm' }">
                                <img v-if="l.stempel" :src="l.stempel"
                                    class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 object-contain pointer-events-none"
                                    style="height:24mm; width:24mm; opacity:0.85;" alt="" />
                                <img v-if="l.tanda_tangan" :src="l.tanda_tangan"
                                    class="relative z-10 object-contain max-h-14" alt="" />
                            </div>

                            <div>
                                <div class="font-extrabold underline uppercase tracking-wide">
                                    {{ l.nama_ttd_kanan }}
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
