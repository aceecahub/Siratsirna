<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const page = usePage();

// Sidebar open/close for mobile overlay
const sidebarOpen = ref(false);

// Track which accordion groups are expanded
const suratOpen = ref(
    route().current('surat-masuk.*') ||
    route().current('surat-keluar.*') ||
    route().current('surat-keterangan-domisili.*') ||
    route().current('surat-tidak-mampu.*') ||
    route().current('surat-keterangan-menikah.*') ||
    route().current('surat-pengantar-ktp.*') ||
    route().current('surat-keterangan-kehilangan.*') ||
    route().current('surat-keterangan-penghasilan.*') ||
    route().current('surat-ahli-waris.*') ||
    route().current('surat-permohonan-bantuan.*')
);

function toggleSurat() {
    suratOpen.value = !suratOpen.value;
}

function closeSidebar() {
    sidebarOpen.value = false;
}

const isSuratActive = computed(() =>
    route().current('surat-masuk.*') ||
    route().current('surat-keluar.*') ||
    route().current('surat-keterangan-domisili.*') ||
    route().current('surat-tidak-mampu.*') ||
    route().current('surat-keterangan-menikah.*') ||
    route().current('surat-pengantar-ktp.*') ||
    route().current('surat-keterangan-kehilangan.*') ||
    route().current('surat-keterangan-penghasilan.*') ||
    route().current('surat-ahli-waris.*') ||
    route().current('surat-permohonan-bantuan.*')
);
</script>

<template>
    <div class="min-h-screen bg-canvas text-slate-900 print:bg-white dark:bg-slate-950 dark:text-slate-100 font-sans antialiased selection:bg-primary selection:text-white">

        <!-- ═══════════════════════════════════════════════════════
             MOBILE SIDEBAR OVERLAY
        ═══════════════════════════════════════════════════════ -->
        <Transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="sidebarOpen"
                class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm lg:hidden print:hidden"
                @click="closeSidebar"
            />
        </Transition>

        <!-- ═══════════════════════════════════════════════════════
             SIDEBAR
        ═══════════════════════════════════════════════════════ -->
        <aside
            :class="[
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                'fixed inset-y-0 left-0 z-50 w-64 lg:translate-x-0 lg:z-auto print:hidden',
                'flex flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 lg:shadow-none shadow-xl',
                'transition-transform duration-300 ease-in-out'
            ]"
        >
            <!-- Brand / Logo -->
            <div class="flex items-center gap-3 px-4 py-4 shrink-0 border-b border-slate-200 dark:border-slate-800">
                <Link :href="route('dashboard')" class="flex items-center gap-3 group flex-1 min-w-0">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md bg-primary text-white shadow-sm transition-transform group-hover:scale-105">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-bold tracking-tight text-slate-900 dark:text-white truncate">DESA SIRNAPUTRA</div>
                        <div class="text-[9px] font-bold uppercase tracking-widest text-primary dark:text-primary">E-Gov Surat</div>
                    </div>
                </Link>
                <!-- Close button mobile -->
                <button @click="closeSidebar" class="lg:hidden p-1.5 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 dark:hover:text-slate-200 transition shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <!-- Nav Items -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

                <!-- Label -->
                <div class="px-2 pb-1 text-[10px] font-medium text-slate-400 dark:text-slate-500">Main Menu</div>

                <!-- Dashboard -->
                <Link
                    :href="route('dashboard')"
                    @click="closeSidebar"
                    :class="[
                        route().current('dashboard')
                            ? 'bg-primary/10 text-primary font-bold dark:bg-primary/20 dark:text-white'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 font-medium',
                        'flex items-center gap-3 rounded-md px-3 py-2.5 text-xs transition-colors duration-150'
                    ]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                    Dashboard
                </Link>

                <!-- ── Surat Accordion ── -->
                <div class="mt-4">
                    <!-- Label group -->
                    <div class="px-2 pb-1 text-[10px] font-medium text-slate-400 dark:text-slate-500">Projects</div>

                    <!-- Trigger: Surat Masuk -->
                    <Link
                        :href="route('surat-masuk.index')"
                        @click="closeSidebar"
                        :class="[
                            route().current('surat-masuk.*')
                                ? 'bg-primary/10 text-primary font-bold dark:bg-primary/20 dark:text-white'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 font-medium',
                            'flex items-center gap-3 rounded-md px-3 py-2.5 text-xs transition-colors duration-150'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75H6.912a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859M12 3v8.25m0 0l-3-3m3 3l3-3" />
                        </svg>
                        Surat Masuk
                    </Link>

                    <Link
                        :href="route('surat-keluar.index')"
                        @click="closeSidebar"
                        :class="[
                            route().current('surat-keluar.*')
                                ? 'bg-primary/10 text-primary font-bold dark:bg-primary/20 dark:text-white'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 font-medium',
                            'flex items-center gap-3 rounded-md px-3 py-2.5 text-xs transition-colors duration-150 mt-0.5'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                        Surat Keluar
                    </Link>

                    <!-- Pembuatan Surat accordion trigger -->
                    <button
                        type="button"
                        @click="toggleSurat"
                        :class="[
                            isSuratActive && suratOpen
                                ? 'bg-slate-100 text-slate-900 dark:bg-slate-800 dark:text-white font-medium'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 font-medium',
                            'w-full flex items-center gap-3 rounded-md px-3 py-2.5 text-xs transition-colors duration-150 mt-0.5'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                        </svg>
                        <span class="flex-1 text-left">Pembuatan Surat</span>
                        <!-- Chevron -->
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                            stroke="currentColor"
                            class="h-3.5 w-3.5 shrink-0 transition-transform duration-200"
                            :class="suratOpen ? 'rotate-180' : ''"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <!-- Accordion sub-menu -->
                    <div
                        class="overflow-hidden transition-all duration-200 ease-in-out"
                        :style="suratOpen ? 'max-height: 500px; opacity: 1' : 'max-height: 0px; opacity: 0'"
                    >
                        <div class="mt-0.5 ml-3 pl-3 border-l border-slate-200 dark:border-slate-800 space-y-0.5">

                            <!-- Surat Keterangan Domisili -->
                            <Link
                                :href="route('surat-keterangan-domisili.index')"
                                @click="closeSidebar"
                                :class="[
                                    route().current('surat-keterangan-domisili.*')
                                        ? 'bg-primary/10 text-primary font-bold dark:bg-primary/20 dark:text-white'
                                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 font-medium',
                                    'flex items-center gap-2.5 rounded-md px-3 py-2 text-xs transition-colors duration-150'
                                ]"
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                Surat Ket. Domisili
                            </Link>

                            <!-- Surat Keterangan Tidak Mampu -->
                            <Link
                                :href="route('surat-tidak-mampu.index')"
                                @click="closeSidebar"
                                :class="[
                                    route().current('surat-tidak-mampu.*')
                                        ? 'bg-primary/10 text-primary font-bold dark:bg-primary/20 dark:text-white'
                                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 font-medium',
                                    'flex items-center gap-2.5 rounded-md px-3 py-2 text-xs transition-colors duration-150'
                                ]"
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                Surat Ket. Tidak Mampu
                            </Link>

                            <!-- Surat Keterangan Menikah -->
                            <Link
                                :href="route('surat-keterangan-menikah.index')"
                                @click="closeSidebar"
                                :class="[
                                    route().current('surat-keterangan-menikah.*')
                                        ? 'bg-primary/10 text-primary font-bold dark:bg-primary/20 dark:text-white'
                                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 font-medium',
                                    'flex items-center gap-2.5 rounded-md px-3 py-2 text-xs transition-colors duration-150'
                                ]"
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                Surat Ket. Menikah
                            </Link>

                            <!-- Surat Pengantar Pencetakan E-KTP -->
                            <Link
                                :href="route('surat-pengantar-ktp.index')"
                                @click="closeSidebar"
                                :class="[
                                    route().current('surat-pengantar-ktp.*')
                                        ? 'bg-primary/10 text-primary font-bold dark:bg-primary/20 dark:text-white'
                                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 font-medium',
                                    'flex items-center gap-2.5 rounded-md px-3 py-2 text-xs transition-colors duration-150'
                                ]"
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                Surat Pengantar E-KTP
                            </Link>

                            <!-- Surat Keterangan Kehilangan -->
                            <Link
                                :href="route('surat-keterangan-kehilangan.index')"
                                @click="closeSidebar"
                                :class="[
                                    route().current('surat-keterangan-kehilangan.*')
                                        ? 'bg-primary/10 text-primary font-bold dark:bg-primary/20 dark:text-white'
                                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 font-medium',
                                    'flex items-center gap-2.5 rounded-md px-3 py-2 text-xs transition-colors duration-150'
                                ]"
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                Surat Ket. Kehilangan
                            </Link>

                            <!-- Surat Keterangan Penghasilan -->
                            <Link
                                :href="route('surat-keterangan-penghasilan.index')"
                                @click="closeSidebar"
                                :class="[
                                    route().current('surat-keterangan-penghasilan.*')
                                        ? 'bg-primary/10 text-primary font-bold dark:bg-primary/20 dark:text-white'
                                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 font-medium',
                                    'flex items-center gap-2.5 rounded-md px-3 py-2 text-xs transition-colors duration-150'
                                ]"
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                Surat Ket. Penghasilan
                            </Link>

                            <!-- Surat Keterangan Ahli Waris -->
                            <Link
                                :href="route('surat-ahli-waris.index')"
                                @click="closeSidebar"
                                :class="[
                                    route().current('surat-ahli-waris.*')
                                        ? 'bg-primary/10 text-primary font-bold dark:bg-primary/20 dark:text-white'
                                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 font-medium',
                                    'flex items-center gap-2.5 rounded-md px-3 py-2 text-xs transition-colors duration-150'
                                ]"
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                Surat Ket. Ahli Waris
                            </Link>

                            <!-- Surat Permohonan Bantuan -->
                            <Link
                                :href="route('surat-permohonan-bantuan.index')"
                                @click="closeSidebar"
                                :class="[
                                    route().current('surat-permohonan-bantuan.*')
                                        ? 'bg-primary/10 text-primary font-bold dark:bg-primary/20 dark:text-white'
                                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 font-medium',
                                    'flex items-center gap-2.5 rounded-md px-3 py-2 text-xs transition-colors duration-150'
                                ]"
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                Surat Permohonan Bantuan
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- ── CMS ── -->
                <div class="mt-2">
                    <div class="px-2 pb-1 mt-3 text-[9px] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Pengaturan</div>
                    <Link
                        :href="route('landing-setting.edit')"
                        @click="closeSidebar"
                        :class="[
                            route().current('landing-setting.*')
                                ? 'bg-primary/10 text-primary font-bold dark:bg-primary/20 dark:text-white'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 font-medium',
                            'flex items-center gap-3 rounded-md px-3 py-2.5 text-xs transition-colors duration-150'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Kelola Landing Page
                    </Link>
                </div>
            </nav>

            <!-- Sidebar Footer: User Profile (Workspace Style) -->
            <div class="shrink-0 p-3">
                <Dropdown align="top-left" width="48">
                    <template #trigger>
                        <button type="button" class="flex w-full items-center justify-between rounded-lg border border-slate-100 bg-slate-50 p-2 transition hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-900/50 dark:hover:bg-slate-800">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-500 text-white font-bold shadow-sm shadow-orange-500/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                    </svg>
                                </div>
                                <div class="text-left min-w-0 flex-1">
                                    <div class="truncate text-xs font-bold text-slate-800 dark:text-slate-200">{{ $page.props.auth.user.name }}</div>
                                    <div class="truncate text-[10px] text-slate-500 dark:text-slate-400">Workspace</div>
                                </div>
                            </div>
                            <div class="text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                </svg>
                            </div>
                        </button>
                    </template>
                    <template #content>
                        <DropdownLink :href="route('profile.edit')">Profil Pengguna</DropdownLink>
                        <DropdownLink :href="route('logout')" method="post" as="button">Keluar</DropdownLink>
                    </template>
                </Dropdown>
            </div>
        </aside>

        <!-- ═══════════════════════════════════════════════════════
             MAIN LAYOUT (content area)
        ═══════════════════════════════════════════════════════ -->
        <div class="lg:pl-64 flex flex-col min-h-screen print:pl-0">

            <!-- TOP BAR -->
            <header class="sticky top-0 z-30 flex h-14 items-center gap-3 border-b border-slate-200 bg-white/90 backdrop-blur-md px-4 sm:px-6 print:hidden dark:border-slate-800 dark:bg-slate-900/90 shrink-0">
                <!-- Hamburger (mobile) -->
                <button
                    @click="sidebarOpen = true"
                    class="lg:hidden p-1.5 rounded-md text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-slate-800 transition"
                    aria-label="Buka Sidebar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Breadcrumb / Page Title area -->
                <div class="flex-1 min-w-0">
                    <slot name="header" />
                </div>

                <!-- Right side actions -->
                <div class="flex items-center gap-3 shrink-0">
                    <ThemeToggle />

                    <!-- User Profile Dropdown in Top Header -->
                    <div class="relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50/80 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none dark:border-slate-800 dark:bg-slate-800/80 dark:text-slate-200 dark:hover:bg-slate-800 cursor-pointer">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-600 text-[11px] font-bold text-white uppercase shadow-sm">
                                        {{ $page.props.auth.user.name ? $page.props.auth.user.name.charAt(0) : 'A' }}
                                    </div>
                                    <span class="max-w-[120px] truncate">{{ $page.props.auth.user.name }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5 text-slate-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </template>
                            <template #content>
                                <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $page.props.auth.user.name }}</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">{{ $page.props.auth.user.email }}</p>
                                </div>
                                <DropdownLink :href="route('profile.edit')" class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-slate-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                    Profil Pengguna
                                </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button" class="flex items-center gap-2 text-rose-600 dark:text-rose-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H2.25" />
                                    </svg>
                                    Keluar (Logout)
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </header>

            <!-- PAGE CONTENT -->
            <main class="flex-1 pb-8 print:pb-0">
                <slot />
            </main>
        </div>
    </div>
</template>
