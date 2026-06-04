<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    invitations: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard
                </h2>

                <Link
                    :href="route('invitations.create')"
                    class="rounded-md bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-800"
                >
                    crear invitacion
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl space-y-4 px-4 sm:px-6 lg:px-8">
                <div
                    v-for="invitation in invitations"
                    :key="invitation.id"
                    class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"
                >
                    <p class="text-xs uppercase tracking-wide text-slate-500">
                        /{{ invitation.slug }}
                    </p>

                    <h3 class="mt-1 text-lg font-semibold text-slate-900">
                        {{ invitation.title }}
                    </h3>

                    <p
                        v-if="invitation.subtitle"
                        class="text-sm text-slate-600"
                    >
                        {{ invitation.subtitle }}
                    </p>

                    <p
                        v-if="invitation.venue_name"
                        class="mt-1 text-sm text-slate-500"
                    >
                        {{ invitation.venue_name }}
                    </p>

                    <div class="mt-4 flex gap-4 text-sm">
                        <Link
                            :href="route('invitations.show', invitation.id)"
                            class="font-medium text-sky-700 hover:text-sky-900"
                        >
                            Ver
                        </Link>

                        <Link
                            :href="route('invitations.edit', invitation.id)"
                            class="font-medium text-sky-700 hover:text-sky-900"
                        >
                            Editar
                        </Link>
                    </div>
                </div>

                <p
                    v-if="!invitations.length"
                    class="rounded-xl border border-dashed border-slate-300 bg-white p-6 text-sm text-slate-500"
                >
                    Todavía no tienes invitaciones.
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>