<script setup>
import Modal from '@/Components/Modal.vue';
import { formatTanggal } from '@/lib/format';
import { computed } from 'vue';

const props = defineProps({
    mail: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);

const isPdf = computed(
    () => props.mail?.file_extension?.toLowerCase() === 'pdf',
);
</script>

<template>
    <Modal :show="!!mail" max-width="4xl" @close="emit('close')">
        <template v-if="mail">
            <div
                class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 p-6 pb-4 dark:border-gray-700"
            >
                <div>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Preview Surat Masuk
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ mail.nomor_agenda }} &middot; {{ formatTanggal(mail.tanggal_diterima) }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <a
                        :href="route('surat-masuk.download', mail.id)"
                        class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white"
                    >
                        Unduh
                    </a>
                    <a
                        :href="mail.file_url"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300"
                    >
                        Buka Tab Baru
                    </a>
                </div>
            </div>

            <div class="p-6">
                <dl
                    class="mb-4 grid grid-cols-1 gap-x-6 gap-y-2 text-sm sm:grid-cols-2"
                >
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Pengirim</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">
                            {{ mail.pengirim }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Kategori</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">
                            {{ mail.kategori }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">
                            Nomor Surat Asal
                        </dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">
                            {{ mail.nomor_surat_asal || '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">
                            Tanggal Surat
                        </dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">
                            {{ formatTanggal(mail.tanggal_surat) }}
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500 dark:text-gray-400">Perihal</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">
                            {{ mail.perihal }}
                        </dd>
                    </div>
                </dl>

                <div
                    class="flex h-[480px] items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-900"
                >
                    <iframe
                        v-if="isPdf"
                        :src="mail.file_url"
                        class="h-full w-full"
                        title="Preview dokumen"
                    />
                    <img
                        v-else
                        :src="mail.file_url"
                        alt="Preview scan surat"
                        class="h-full w-full object-contain"
                    />
                </div>
            </div>
        </template>
    </Modal>
</template>
