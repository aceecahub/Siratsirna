<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    showLabel: {
        type: Boolean,
        default: false,
    },
    compact: {
        type: Boolean,
        default: false,
    },
});

const isDark = ref(false);

function updateMetaThemeColor(dark) {
    const metaThemeColor = document.querySelector('meta[name="theme-color"]');
    if (metaThemeColor) {
        metaThemeColor.setAttribute('content', dark ? '#0f172a' : '#ffffff');
    }
}

function checkTheme() {
    if (typeof window !== 'undefined') {
        isDark.value = document.documentElement.classList.contains('dark');
        updateMetaThemeColor(isDark.value);
    }
}

function toggleTheme() {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
    updateMetaThemeColor(isDark.value);
}

onMounted(() => {
    checkTheme();
});
</script>

<template>
    <button
        type="button"
        @click="toggleTheme"
        class="relative inline-flex items-center gap-2 rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 active:scale-95 select-none"
        :class="[
            compact
                ? 'p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white'
                : 'border border-slate-200/80 bg-white/90 px-3 py-1.5 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-100/80 hover:text-slate-900 dark:border-slate-800 dark:bg-slate-900/90 dark:text-slate-200 dark:hover:bg-slate-800'
        ]"
        :title="isDark ? 'Beralih ke Mode Cerah' : 'Beralih ke Mode Gelap'"
        :aria-label="isDark ? 'Beralih ke Mode Cerah' : 'Beralih ke Mode Gelap'"
    >
        <!-- Sun Icon (shown when dark mode is ON) -->
        <svg
            v-if="isDark"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="2"
            stroke="currentColor"
            class="h-4 w-4 text-amber-400 transition-transform duration-300 rotate-0 hover:rotate-45"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"
            />
        </svg>

        <!-- Moon Icon (shown when light mode is ON) -->
        <svg
            v-else
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="2"
            stroke="currentColor"
            class="h-4 w-4 text-slate-600 transition-transform duration-300 hover:-rotate-12"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"
            />
        </svg>

        <span v-if="showLabel" class="text-xs font-semibold">
            {{ isDark ? 'Mode Cerah' : 'Mode Gelap' }}
        </span>
    </button>
</template>
