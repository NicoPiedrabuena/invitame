<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Verificar correo" />

        <div class="mb-7">
            <p class="cm-eyebrow">Verificación</p>
            <h1 class="cm-title mt-3 text-3xl">Revisá tu correo</h1>
            <p class="cm-body mt-3">
                Te enviamos un enlace para verificar tu dirección. Si no llegó, podés pedir otro.
            </p>
        </div>

        <div
            class="mb-4 rounded-lg border border-[#b7c08d] bg-[#f2f5df] px-4 py-3 text-sm font-medium text-[#40540f]"
            v-if="verificationLinkSent"
        >
            Enviamos un nuevo enlace de verificación a tu correo.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Reenviar correo
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="cm-link"
                    >Cerrar sesión</Link
                >
            </div>
        </form>
    </GuestLayout>
</template>
