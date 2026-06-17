<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    googleLoginUrl: {
        type: String,
        default: null,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Ingresar" />

        <div class="mb-7">
            <p class="cm-eyebrow">Bienvenido</p>
            <h1 class="cm-title mt-3 text-3xl">Volvé a tus invitaciones</h1>
            <p class="cm-body mt-3">Entrá para seguir diseñando tus celebraciones y recuerdos guardados.</p>
        </div>

        <div v-if="status" class="mb-4 rounded-lg border border-[#b7c08d] bg-[#f2f5df] px-4 py-3 text-sm font-medium text-[#40540f]">
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

            <div>
                <InputLabel for="password" value="Contraseña" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-2 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-[#7c7168]"
                        >Recordarme</span
                    >
                </label>
            </div>

            <div class="flex flex-col-reverse gap-4 sm:flex-row sm:items-center sm:justify-between">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="cm-link"
                >
                    Olvidé mi contraseña
                </Link>

                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Ingresar
                </PrimaryButton>
            </div>

            <a
                v-if="googleLoginUrl"
                :href="googleLoginUrl"
                class="inline-flex w-full items-center justify-center rounded-lg border border-[#d8c8b6] bg-[#fffaf5] px-4 py-3 text-sm font-semibold text-[#15120f] shadow-sm transition hover:bg-white focus:outline-none focus:ring-2 focus:ring-[#40540f] focus:ring-offset-2 focus:ring-offset-[#fff7ef]"
            >
                Continuar con Google
            </a>
        </form>
    </GuestLayout>
</template>
