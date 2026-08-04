<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({}),
    },
});

const activeTab = ref('hero');

// Form setup with existing settings or fallback defaults
const form = useForm({
    village_name: props.settings.village_name || 'Desa Sirnaputra',
    subdistrict_name: props.settings.subdistrict_name || 'Kecamatan Cigalontang',
    regency_name: props.settings.regency_name || 'Kabupaten Tasikmalaya',
    hero_badge: props.settings.hero_badge || 'Portal Resmi & Layanan Digital Warga Desa Sirnaputra',
    hero_title: props.settings.hero_title || 'Harmoni Alam, Kemajuan Digital, & Kesejahteraan Warga',
    hero_subtitle: props.settings.hero_subtitle || 'Selamat datang di portal informasi dan pelayanan publik Desa Sirnaputra, Kecamatan Cigalontang, Kabupaten Tasikmalaya.',
    hero_image_file: null,

    stats: props.settings.stats || {
        penduduk: '4.852',
        kk: '1.240',
        dusun: '6',
        rt_rw: '24 / 8',
    },

    kades_name: props.settings.kades_name || 'H. Ahmad Supriatna',
    kades_position: props.settings.kades_position || 'Kepala Desa Sirnaputra',
    kades_period: props.settings.kades_period || 'Masa Jabatan 2021 - 2027',
    kades_speech: props.settings.kades_speech || '',
    kades_photo_file: null,

    services: props.settings.services || [],
    potentials: props.settings.potentials || [],
    news: props.settings.news || [],
    officers: props.settings.officers || [],

    balai_desa_title: props.settings.balai_desa_title || 'Balai Desa Sirnaputra',
    balai_desa_desc: props.settings.balai_desa_desc || '',
    balai_desa_photo_file: null,

    contact_address: props.settings.contact_address || '',
    contact_hours: props.settings.contact_hours || '',
    contact_phone: props.settings.contact_phone || '',
    contact_email: props.settings.contact_email || '',
    contact_maps_url: props.settings.contact_maps_url || '',
});

// Item additions
function addServiceItem() {
    form.services.push({
        id: Date.now(),
        icon: '📝',
        title: 'Layanan Baru',
        desc: 'Deskripsi singkat permohonan layanan.',
        estimate: 'Estimasi: 1 hari kerja',
    });
}
function removeServiceItem(index) {
    form.services.splice(index, 1);
}

function addPotentialItem() {
    form.potentials.push({
        id: Date.now(),
        icon: '🌾',
        category: 'Kategori',
        title: 'Potensi Baru',
        desc: 'Deskripsi keunggulan komoditas/wisata.',
    });
}
function removePotentialItem(index) {
    form.potentials.splice(index, 1);
}

function addNewsItem() {
    form.news.push({
        id: Date.now(),
        category: 'Pemerintahan',
        date: new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }),
        title: 'Judul Warta Baru',
        excerpt: 'Ringkasan artikel pengumuman atau berita desa.',
    });
}
function removeNewsItem(index) {
    form.news.splice(index, 1);
}

function addOfficerItem() {
    form.officers.push({
        id: Date.now(),
        icon: '👔',
        name: 'Nama Lengkap',
        position: 'Jabatan Perangkat',
    });
}
function removeOfficerItem(index) {
    form.officers.splice(index, 1);
}

const submit = () => {
    form.post(route('landing-setting.update'), {
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Kelola Landing Page - Admin CMS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                        Manajemen Konten CMS
                    </span>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                        Editor Landing Page Desa
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <a
                        href="/"
                        target="_blank"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-emerald-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                        Pratinjau Landing Page
                    </a>
                </div>
            </div>
        </template>

        <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8 space-y-6">
            <!-- Tabs Navigation -->
            <div class="flex overflow-x-auto gap-2 border-b border-slate-200 pb-2 dark:border-slate-800 no-scrollbar">
                <button
                    type="button"
                    @click="activeTab = 'hero'"
                    :class="[
                        activeTab === 'hero'
                            ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-600/20'
                            : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 font-semibold',
                        'inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs transition select-none shrink-0 cursor-pointer'
                    ]"
                >
                    🌟 Hero & Identitas
                </button>

                <button
                    type="button"
                    @click="activeTab = 'kades'"
                    :class="[
                        activeTab === 'kades'
                            ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-600/20'
                            : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 font-semibold',
                        'inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs transition select-none shrink-0 cursor-pointer'
                    ]"
                >
                    👨‍💼 Sambutan Kades
                </button>

                <button
                    type="button"
                    @click="activeTab = 'stats'"
                    :class="[
                        activeTab === 'stats'
                            ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-600/20'
                            : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 font-semibold',
                        'inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs transition select-none shrink-0 cursor-pointer'
                    ]"
                >
                    📊 Demografi
                </button>

                <button
                    type="button"
                    @click="activeTab = 'services'"
                    :class="[
                        activeTab === 'services'
                            ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-600/20'
                            : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 font-semibold',
                        'inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs transition select-none shrink-0 cursor-pointer'
                    ]"
                >
                    📝 Kartu Layanan ({{ form.services.length }})
                </button>

                <button
                    type="button"
                    @click="activeTab = 'potentials'"
                    :class="[
                        activeTab === 'potentials'
                            ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-600/20'
                            : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 font-semibold',
                        'inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs transition select-none shrink-0 cursor-pointer'
                    ]"
                >
                    🌾 Potensi Desa ({{ form.potentials.length }})
                </button>

                <button
                    type="button"
                    @click="activeTab = 'news'"
                    :class="[
                        activeTab === 'news'
                            ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-600/20'
                            : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 font-semibold',
                        'inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs transition select-none shrink-0 cursor-pointer'
                    ]"
                >
                    📰 Berita Desa ({{ form.news.length }})
                </button>

                <button
                    type="button"
                    @click="activeTab = 'officers'"
                    :class="[
                        activeTab === 'officers'
                            ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-600/20'
                            : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 font-semibold',
                        'inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs transition select-none shrink-0 cursor-pointer'
                    ]"
                >
                    👔 Perangkat Desa ({{ form.officers.length }})
                </button>

                <button
                    type="button"
                    @click="activeTab = 'contact'"
                    :class="[
                        activeTab === 'contact'
                            ? 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-600/20'
                            : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 font-semibold',
                        'inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs transition select-none shrink-0 cursor-pointer'
                    ]"
                >
                    📍 Kontak & Peta
                </button>
            </div>

            <!-- Form Content Wrapper -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- TAB 1: HERO & GENERAL -->
                <div v-show="activeTab === 'hero'" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-5">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">Identitas & Hero Section</h3>

                    <div class="grid sm:grid-cols-3 gap-4">
                        <div>
                            <InputLabel value="Nama Desa" class="text-xs font-bold" />
                            <TextInput v-model="form.village_name" class="mt-1 block w-full text-xs" />
                        </div>
                        <div>
                            <InputLabel value="Kecamatan" class="text-xs font-bold" />
                            <TextInput v-model="form.subdistrict_name" class="mt-1 block w-full text-xs" />
                        </div>
                        <div>
                            <InputLabel value="Kabupaten" class="text-xs font-bold" />
                            <TextInput v-model="form.regency_name" class="mt-1 block w-full text-xs" />
                        </div>
                    </div>

                    <div>
                        <InputLabel value="Badge Teks Top Hero" class="text-xs font-bold" />
                        <TextInput v-model="form.hero_badge" class="mt-1 block w-full text-xs" />
                    </div>

                    <div>
                        <InputLabel value="Judul Utama (Headline Hero)" class="text-xs font-bold" />
                        <TextInput v-model="form.hero_title" class="mt-1 block w-full text-xs" />
                    </div>

                    <div>
                        <InputLabel value="Sub-judul (Subheadline Hero)" class="text-xs font-bold" />
                        <textarea
                            v-model="form.hero_subtitle"
                            rows="3"
                            class="mt-1 block w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs dark:border-slate-800 dark:bg-slate-950 dark:text-white"
                        ></textarea>
                    </div>

                    <div>
                        <InputLabel value="Ganti Foto Utama Hero (Landscape)" class="text-xs font-bold" />
                        <input
                            type="file"
                            @change="form.hero_image_file = $event.target.files[0]"
                            accept="image/*"
                            class="mt-1.5 block w-full text-xs text-slate-500 file:mr-4 file:rounded-xl file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-xs file:font-bold file:text-emerald-700 hover:file:bg-emerald-100"
                        />
                        <p class="mt-1 text-[11px] text-slate-500">Foto landscape pedesaan / ikonik desa. Format: PNG, JPG, WEBP.</p>
                    </div>
                </div>

                <!-- TAB 2: SAMBUTAN KADES -->
                <div v-show="activeTab === 'kades'" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-5">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">Sambutan Kepala Desa & Visi Misi</h3>

                    <div class="grid sm:grid-cols-3 gap-4">
                        <div>
                            <InputLabel value="Nama Kepala Desa" class="text-xs font-bold" />
                            <TextInput v-model="form.kades_name" class="mt-1 block w-full text-xs" />
                        </div>
                        <div>
                            <InputLabel value="Jabatan" class="text-xs font-bold" />
                            <TextInput v-model="form.kades_position" class="mt-1 block w-full text-xs" />
                        </div>
                        <div>
                            <InputLabel value="Masa Jabatan" class="text-xs font-bold" />
                            <TextInput v-model="form.kades_period" class="mt-1 block w-full text-xs" />
                        </div>
                    </div>

                    <div>
                        <InputLabel value="Teks Sambutan Kepala Desa" class="text-xs font-bold" />
                        <textarea
                            v-model="form.kades_speech"
                            rows="4"
                            class="mt-1 block w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs dark:border-slate-800 dark:bg-slate-950 dark:text-white"
                        ></textarea>
                    </div>

                    <div>
                        <InputLabel value="Unggah Foto Kepala Desa (Opsional)" class="text-xs font-bold" />
                        <input
                            type="file"
                            @change="form.kades_photo_file = $event.target.files[0]"
                            accept="image/*"
                            class="mt-1.5 block w-full text-xs text-slate-500 file:mr-4 file:rounded-xl file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-xs file:font-bold file:text-emerald-700 hover:file:bg-emerald-100"
                        />
                    </div>
                </div>

                <!-- TAB 3: STATISTIK DEMOGRAFI -->
                <div v-show="activeTab === 'stats'" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-5">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">Statistik Demografi Desa</h3>

                    <div class="grid sm:grid-cols-4 gap-4">
                        <div>
                            <InputLabel value="Jumlah Penduduk" class="text-xs font-bold" />
                            <TextInput v-model="form.stats.penduduk" class="mt-1 block w-full text-xs" />
                        </div>
                        <div>
                            <InputLabel value="Jumlah KK" class="text-xs font-bold" />
                            <TextInput v-model="form.stats.kk" class="mt-1 block w-full text-xs" />
                        </div>
                        <div>
                            <InputLabel value="Jumlah Dusun" class="text-xs font-bold" />
                            <TextInput v-model="form.stats.dusun" class="mt-1 block w-full text-xs" />
                        </div>
                        <div>
                            <InputLabel value="RT / RW" class="text-xs font-bold" />
                            <TextInput v-model="form.stats.rt_rw" class="mt-1 block w-full text-xs" />
                        </div>
                    </div>
                </div>

                <!-- TAB 4: KARTU LAYANAN -->
                <div v-show="activeTab === 'services'" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">Kelola Kartu Layanan Digital</h3>
                        <button
                            type="button"
                            @click="addServiceItem"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-emerald-500 transition"
                        >
                            + Tambah Layanan
                        </button>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div v-for="(item, idx) in form.services" :key="idx" class="rounded-2xl border border-slate-200 p-4 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-950/50 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-extrabold text-emerald-600">Layanan #{{ idx + 1 }}</span>
                                <button type="button" @click="removeServiceItem(idx)" class="text-xs font-bold text-rose-500 hover:underline">Hapus</button>
                            </div>
                            <div class="grid grid-cols-4 gap-2">
                                <div class="col-span-1">
                                    <InputLabel value="Ikon Emoji" class="text-[11px] font-bold" />
                                    <TextInput v-model="item.icon" class="mt-1 block w-full text-xs text-center" />
                                </div>
                                <div class="col-span-3">
                                    <InputLabel value="Nama Surat / Layanan" class="text-[11px] font-bold" />
                                    <TextInput v-model="item.title" class="mt-1 block w-full text-xs" />
                                </div>
                            </div>
                            <div>
                                <InputLabel value="Deskripsi Syarat & Peruntukan" class="text-[11px] font-bold" />
                                <TextInput v-model="item.desc" class="mt-1 block w-full text-xs" />
                            </div>
                            <div>
                                <InputLabel value="Estimasi Waktu" class="text-[11px] font-bold" />
                                <TextInput v-model="item.estimate" class="mt-1 block w-full text-xs" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 5: POTENSI DESA -->
                <div v-show="activeTab === 'potentials'" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">Kelola Potensi & Komoditas Desa</h3>
                        <button
                            type="button"
                            @click="addPotentialItem"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-emerald-500 transition"
                        >
                            + Tambah Potensi
                        </button>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div v-for="(item, idx) in form.potentials" :key="idx" class="rounded-2xl border border-slate-200 p-4 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-950/50 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-extrabold text-emerald-600">Potensi #{{ idx + 1 }}</span>
                                <button type="button" @click="removePotentialItem(idx)" class="text-xs font-bold text-rose-500 hover:underline">Hapus</button>
                            </div>
                            <div class="grid grid-cols-4 gap-2">
                                <div class="col-span-1">
                                    <InputLabel value="Ikon Emoji" class="text-[11px] font-bold" />
                                    <TextInput v-model="item.icon" class="mt-1 block w-full text-xs text-center" />
                                </div>
                                <div class="col-span-3">
                                    <InputLabel value="Kategori Sektor" class="text-[11px] font-bold" />
                                    <TextInput v-model="item.category" class="mt-1 block w-full text-xs" />
                                </div>
                            </div>
                            <div>
                                <InputLabel value="Nama Komoditas / Wisata" class="text-[11px] font-bold" />
                                <TextInput v-model="item.title" class="mt-1 block w-full text-xs" />
                            </div>
                            <div>
                                <InputLabel value="Deskripsi Ringkas" class="text-[11px] font-bold" />
                                <TextInput v-model="item.desc" class="mt-1 block w-full text-xs" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 6: BERITA DESA -->
                <div v-show="activeTab === 'news'" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">Kelola Berita & Warta Terkini</h3>
                        <button
                            type="button"
                            @click="addNewsItem"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-emerald-500 transition"
                        >
                            + Tambah Warta
                        </button>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div v-for="(item, idx) in form.news" :key="idx" class="rounded-2xl border border-slate-200 p-4 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-950/50 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-extrabold text-emerald-600">Berita #{{ idx + 1 }}</span>
                                <button type="button" @click="removeNewsItem(idx)" class="text-xs font-bold text-rose-500 hover:underline">Hapus</button>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <InputLabel value="Kategori" class="text-[11px] font-bold" />
                                    <TextInput v-model="item.category" class="mt-1 block w-full text-xs" />
                                </div>
                                <div>
                                    <InputLabel value="Tanggal Terbit" class="text-[11px] font-bold" />
                                    <TextInput v-model="item.date" class="mt-1 block w-full text-xs" />
                                </div>
                            </div>
                            <div>
                                <InputLabel value="Judul Berita Utama" class="text-[11px] font-bold" />
                                <TextInput v-model="item.title" class="mt-1 block w-full text-xs" />
                            </div>
                            <div>
                                <InputLabel value="Ringkasan Artikel" class="text-[11px] font-bold" />
                                <textarea
                                    v-model="item.excerpt"
                                    rows="2"
                                    class="mt-1 block w-full rounded-xl border border-slate-200 bg-white p-2 text-xs dark:border-slate-800 dark:bg-slate-950"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 7: PERANGKAT DESA -->
                <div v-show="activeTab === 'officers'" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">Kelola Perangkat / Pamong Desa</h3>
                        <button
                            type="button"
                            @click="addOfficerItem"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-emerald-500 transition"
                        >
                            + Tambah Perangkat
                        </button>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div v-for="(item, idx) in form.officers" :key="idx" class="rounded-2xl border border-slate-200 p-4 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-950/50 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-extrabold text-emerald-600">Aparatur #{{ idx + 1 }}</span>
                                <button type="button" @click="removeOfficerItem(idx)" class="text-xs font-bold text-rose-500 hover:underline">Hapus</button>
                            </div>
                            <div>
                                <InputLabel value="Nama Lengkap & Gelar" class="text-[11px] font-bold" />
                                <TextInput v-model="item.name" class="mt-1 block w-full text-xs" />
                            </div>
                            <div>
                                <InputLabel value="Jabatan Perangkat" class="text-[11px] font-bold" />
                                <TextInput v-model="item.position" class="mt-1 block w-full text-xs" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 8: KONTAK & PETA -->
                <div v-show="activeTab === 'contact'" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-5">
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">Informasi Balai Desa & Kontak</h3>

                    <div>
                        <InputLabel value="Alamat Lengkap Kantor Desa" class="text-xs font-bold" />
                        <TextInput v-model="form.contact_address" class="mt-1 block w-full text-xs" />
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <InputLabel value="Hotline WhatsApp" class="text-xs font-bold" />
                            <TextInput v-model="form.contact_phone" class="mt-1 block w-full text-xs" placeholder="0812-3456-7890" />
                        </div>
                        <div>
                            <InputLabel value="Email Resmi Desa" class="text-xs font-bold" />
                            <TextInput v-model="form.contact_email" class="mt-1 block w-full text-xs" placeholder="pemdes@sirnaputra.desa.id" />
                        </div>
                    </div>

                    <div>
                        <InputLabel value="Jam Operasional Pelayanan" class="text-xs font-bold" />
                        <textarea
                            v-model="form.contact_hours"
                            rows="2"
                            class="mt-1 block w-full rounded-xl border border-slate-200 bg-slate-50 p-2.5 text-xs dark:border-slate-800 dark:bg-slate-950 dark:text-white"
                        ></textarea>
                    </div>

                    <div>
                        <InputLabel value="URL Embed Google Maps" class="text-xs font-bold" />
                        <TextInput v-model="form.contact_maps_url" class="mt-1 block w-full text-xs" />
                    </div>

                    <div>
                        <InputLabel value="Ganti Foto Balai Desa" class="text-xs font-bold" />
                        <input
                            type="file"
                            @change="form.balai_desa_photo_file = $event.target.files[0]"
                            accept="image/*"
                            class="mt-1.5 block w-full text-xs text-slate-500 file:mr-4 file:rounded-xl file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-xs file:font-bold file:text-emerald-700 hover:file:bg-emerald-100"
                        />
                    </div>
                </div>

                <!-- Floating Bottom Action Bar -->
                <div class="sticky bottom-4 z-30 flex items-center justify-between rounded-2xl border border-slate-200 bg-white/90 p-4 shadow-xl backdrop-blur-md dark:border-slate-800 dark:bg-slate-900/90">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                        Pastikan seluruh data yang diisi telah sesuai sebelum menyimpan.
                    </p>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-2.5 text-xs font-bold text-white shadow-md shadow-emerald-600/20 hover:from-emerald-500 hover:to-teal-500 active:scale-95 disabled:opacity-50 transition cursor-pointer"
                    >
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        💾 Simpan Perubahan Landing Page
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
