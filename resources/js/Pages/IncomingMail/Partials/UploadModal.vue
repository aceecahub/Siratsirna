<script setup>
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'uploaded']);

const form = useForm({
    file: null,
    nomor_surat_asal: '',
    tanggal_surat: '',
    tanggal_diterima: new Date().toISOString().slice(0, 10),
    pengirim: '',
    perihal: '',
    kategori: props.categories[0] ?? 'Surat Dinas',
});

const ALLOWED_EXTENSIONS = ['pdf', 'png', 'jpg', 'jpeg'];
const MAX_SIZE = 5 * 1024 * 1024;

function onFileChange(event) {
    const file = event.target.files[0];

    if (!file) {
        form.file = null;

        return;
    }

    const extension = file.name.split('.').pop().toLowerCase();

    if (!ALLOWED_EXTENSIONS.includes(extension)) {
        alert('Format file harus PDF, PNG, atau JPG.');
        event.target.value = '';

        return;
    }

    if (file.size > MAX_SIZE) {
        alert('Ukuran file maksimal 5MB.');
        event.target.value = '';

        return;
    }

    form.file = file;
}

function submit() {
    form.post(route('surat-masuk.store'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('uploaded');
            form.reset();
            emit('close');
        },
    });
}

function close() {
    if (form.processing) return;

    form.reset();
    form.clearErrors();
    emit('close');
}
</script>

<template>
    <Modal :show="show" max-width="2xl" @close="close">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Arsip Surat Masuk
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Unggah hasil scan surat masuk. Mendukung PDF, PNG, dan JPG
                (maks. 5MB). Nomor agenda akan dibuat otomatis.
            </p>

            <form @submit.prevent="submit" class="mt-6 space-y-5">
                <div>
                    <InputLabel value="File Scan Surat" />
                    <input
                        type="file"
                        accept=".pdf,.png,.jpg,.jpeg,application/pdf,image/png,image/jpeg"
                        @change="onFileChange"
                        class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-4 file:rounded-md file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-xs file:font-semibold file:uppercase file:tracking-widest file:text-white file:transition hover:file:bg-gray-700 dark:text-gray-400 dark:file:bg-gray-200 dark:file:text-gray-800"
                    />
                    <InputError class="mt-2" :message="form.errors.file" />
                    <div
                        v-if="form.progress"
                        class="mt-2 h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"
                    >
                        <div
                            class="h-full bg-indigo-500 transition-all"
                            :style="{
                                width: `${form.progress.percentage}%`,
                            }"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <InputLabel
                            value="Nomor Surat Asal"
                            :optional="true"
                        />
                        <input
                            v-model="form.nomor_surat_asal"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            placeholder="cth. 140/24/2026"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.nomor_surat_asal"
                        />
                    </div>

                    <div>
                        <InputLabel value="Kategori" />
                        <select
                            v-model="form.kategori"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                        >
                            <option
                                v-for="category in categories"
                                :key="category"
                                :value="category"
                            >
                                {{ category }}
                            </option>
                        </select>
                        <InputError
                            class="mt-2"
                            :message="form.errors.kategori"
                        />
                    </div>

                    <div>
                        <InputLabel value="Tanggal Surat" />
                        <input
                            v-model="form.tanggal_surat"
                            type="date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.tanggal_surat"
                        />
                    </div>

                    <div>
                        <InputLabel value="Tanggal Diterima" />
                        <input
                            v-model="form.tanggal_diterima"
                            type="date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.tanggal_diterima"
                        />
                    </div>

                    <div>
                        <InputLabel value="Pengirim" />
                        <input
                            v-model="form.pengirim"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            placeholder="Instansi / nama pengirim"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.pengirim"
                        />
                    </div>

                    <div>
                        <InputLabel value="Perihal / Ringkasan" />
                        <input
                            v-model="form.perihal"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                            placeholder="Perihal surat"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.perihal"
                        />
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <SecondaryButton @click="close">
                        Batal
                    </SecondaryButton>
                    <PrimaryButton
                        :disabled="form.processing"
                        :class="{ 'opacity-25': form.processing }"
                    >
                        {{ form.processing ? 'Mengunggah...' : 'Simpan Arsip' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
