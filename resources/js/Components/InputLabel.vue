<script setup>
defineProps({
    value: {
        type: String,
    },
    optional: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
        <span v-if="optional" class="ml-1 text-[11px] font-normal text-slate-400">
            (opsional)
        </span>
    </label>
</template>
