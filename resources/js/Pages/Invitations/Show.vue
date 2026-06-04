<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    invitation: {
        type: Object,
        required: true,
    },
    publicUrl: {
        type: String,
        required: true,
    },
});

const destroyForm = useForm({});

const remove = () => {
    destroyForm.delete(route('invitations.destroy', props.invitation.id));
};
</script>

<template>
    <Head title="Detalle invitacion" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Detalle invitacion</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Slug publico</p>
                <a :href="publicUrl" target="_blank" class="break-all text-sky-700">{{ publicUrl }}</a>

                <h3 class="text-2xl font-semibold text-slate-900">{{ invitation.title }}</h3>
                <p class="text-slate-600">{{ invitation.subtitle }}</p>

                <div class="flex items-center gap-3">
                    <Link :href="route('invitations.edit', invitation.id)" class="rounded-md bg-slate-900 px-4 py-2 text-sm text-white">
                        Editar
                    </Link>
                    <DangerButton @click="remove">Eliminar</DangerButton>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
