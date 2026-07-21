<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Recuperar contraseña" />

        <div class="mb-7">
            <p class="cm-eyebrow">Recuperar acceso</p>
            <h1 class="cm-title mt-3 text-3xl">Restablecé tu contraseña</h1>
            <p class="cm-body mt-3">
                Escribí tu correo y te enviaremos un enlace para crear una contraseña nueva.
            </p>
        </div>

        <div
            v-if="status"
            class="mb-4 rounded-lg border border-[#d7ceff] bg-[#f4f1ff] px-4 py-3 text-sm font-medium text-[#6d4aff]"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="email" value="Correo" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-2 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Enviar enlace
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
