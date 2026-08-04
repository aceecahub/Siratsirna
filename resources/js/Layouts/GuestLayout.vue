<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="relative flex min-h-screen flex-col justify-between bg-slate-50 font-sans text-slate-900 antialiased selection:bg-emerald-500 selection:text-white dark:bg-slate-950 dark:text-slate-100 transition-colors duration-200 overflow-hidden">
        <!-- Top Accent Gradient Line -->
        <div class="h-1.5 w-full bg-gradient-to-r from-emerald-600 via-teal-500 to-emerald-400"></div>

        <!-- Ambient Background Glow Effects -->
        <div class="pointer-events-none absolute -top-40 -left-40 h-96 w-96 rounded-full bg-emerald-500/10 blur-3xl dark:bg-emerald-500/15"></div>
        <div class="pointer-events-none absolute top-1/2 -right-40 h-96 w-96 rounded-full bg-teal-500/10 blur-3xl dark:bg-teal-500/15"></div>

        <!-- Header Bar -->
        <header class="relative z-10 mx-auto w-full max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <Link href="/" class="flex items-center gap-3 group transition">
                    <ApplicationLogo />
                </Link>

                <div class="flex items-center gap-3">
                    <Link
                        href="/"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200/80 bg-white/80 px-3.5 py-1.5 text-xs font-bold text-slate-700 shadow-sm backdrop-blur-md hover:bg-slate-100 hover:text-slate-900 dark:border-slate-800 dark:bg-slate-900/80 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white transition-all active:scale-95"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-emerald-600 dark:text-emerald-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Beranda Desa
                    </Link>
                    <ThemeToggle />
                </div>
            </div>
        </header>

        <!-- Main Auth Container -->
        <main class="relative z-10 my-auto flex w-full flex-col items-center justify-center px-4 py-8 sm:px-6">
            <div class="w-full max-w-md">
                <!-- Card -->
                <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white/90 p-8 shadow-2xl shadow-slate-950/5 backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-900/90 dark:shadow-black/40 transition-all">
                    <slot />
                </div>

                <!-- Assistance Badge -->
                <div class="mt-6 text-center">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                        Butuh bantuan seputar akun E-Gov Desa Sirnaputra?
                        <a href="https://wa.me/6281234567890" target="_blank" class="font-bold text-emerald-600 hover:underline dark:text-emerald-400">
                            Hubungi Admin Desa
                        </a>
                    </p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 py-6 text-center text-xs font-medium text-slate-500 dark:text-slate-500 border-t border-slate-200/60 dark:border-slate-800/60 bg-white/40 dark:bg-slate-950/40 backdrop-blur-sm">
            <p>&copy; {{ new Date().getFullYear() }} Pemerintah Desa Sirnaputra. Seluruh Hak Cipta Dilindungi.</p>
        </footer>
    </div>
</template>
