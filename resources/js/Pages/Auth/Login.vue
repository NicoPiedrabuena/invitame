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

        <div v-if="status" class="mb-4 rounded-lg border border-[#d7ceff] bg-[#f4f1ff] px-4 py-3 text-sm font-medium text-[#6d4aff]">
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
                    <span class="ms-2 text-sm text-[#716b79]"
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
                class="inline-flex w-full items-center justify-center rounded-full border border-[#ded9e8] bg-white px-4 py-3 text-sm font-bold text-[#211b35] shadow-sm transition hover:border-[#b9aaff] hover:bg-[#faf9ff] focus:outline-none focus:ring-2 focus:ring-[#7657ff] focus:ring-offset-2"
            >
                Continuar con Google
            </a>
        </form>
    </GuestLayout>
</template>
