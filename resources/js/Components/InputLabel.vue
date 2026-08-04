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
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
        <span v-if="optional" class="text-xs font-normal text-gray-400">
            (opsional)
        </span>
    </label>
</template>
