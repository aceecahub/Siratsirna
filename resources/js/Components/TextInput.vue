<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-md border-slate-300 text-xs shadow-sm focus:border-primary focus:ring-1 focus:ring-primary/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:focus:border-primary dark:focus:ring-primary/50"
        v-model="model"
        ref="input"
    />
</template>
