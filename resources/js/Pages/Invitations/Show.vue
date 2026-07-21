<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    invitation: { type: Object, required: true },
    publicUrl: { type: String, required: true },
    rsvps: { type: Array, default: () => [] },
    messages: { type: Array, default: () => [] },
    summary: {
        type: Object,
        default: () => ({ confirmedGuests: 0, declinedResponses: 0, responseCount: 0, messageCount: 0 }),
    },
});

const destroyForm = useForm({});
const remove = () => destroyForm.delete(route('invitations.destroy', props.invitation.id));

const formatDate = (value) => new Intl.DateTimeFormat('es-AR', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
}).format(new Date(value));

const dietaryLabel = (value) => ({
    ninguna: 'Sin restricciones',
    vegetariano: 'Vegetariano',
    vegano: 'Vegano',
    celiaco: 'Celíaco',
    diabetico: 'Diabético',
    otro: 'Otra restricción',
}[value] || value || 'Sin restricciones');

const categoryLabel = (value) => ({ familia: 'Familia', amigos: 'Amigos', otros: 'Otros' }[value] || 'Otros');
</script>

<template>
    <Head :title="`Reservas · ${invitation.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">Gestión del evento</p>
                    <h2 class="mt-1 text-2xl font-semibold text-slate-900">{{ invitation.title }}</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a :href="publicUrl" target="_blank" rel="noopener" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Ver invitación</a>
                    <Link :href="route('invitations.edit', invitation.id)" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Editar</Link>
                </div>
            </div>
        </template>

        <div class="bg-[#fff8f1] py-8">
            <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
                <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="rounded-2xl border border-violet-100 bg-white p-5 shadow-sm">
                        <p class="text-sm font-medium text-slate-500">Invitados confirmados</p>
                        <p class="mt-2 text-4xl font-semibold text-violet-700">{{ summary.confirmedGuests }}</p>
                    </article>
                    <article class="rounded-2xl border border-amber-100 bg-white p-5 shadow-sm">
                        <p class="text-sm font-medium text-slate-500">Respuestas recibidas</p>
                        <p class="mt-2 text-4xl font-semibold text-amber-700">{{ summary.responseCount }}</p>
                    </article>
                    <article class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm">
                        <p class="text-sm font-medium text-slate-500">No asistirán</p>
                        <p class="mt-2 text-4xl font-semibold text-rose-700">{{ summary.declinedResponses }}</p>
                    </article>
                    <article class="rounded-2xl border border-violet-100 bg-white p-5 shadow-sm">
                        <p class="text-sm font-medium text-slate-500">Mensajes de cariño</p>
                        <p class="mt-2 text-4xl font-semibold text-violet-700">{{ summary.messageCount }}</p>
                    </article>
                </section>

                <div class="grid items-start gap-8 xl:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.65fr)]">
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-6 py-5">
                            <h3 class="text-xl font-semibold text-slate-900">Lista de invitados</h3>
                            <p class="mt-1 text-sm text-slate-500">Confirmaciones, acompañantes y necesidades alimentarias.</p>
                        </div>

                        <div v-if="rsvps.length" class="divide-y divide-slate-100">
                            <article v-for="rsvp in rsvps" :key="rsvp.id" class="p-6">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="font-semibold text-slate-900">{{ rsvp.guest_name }}</h4>
                                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold" :class="rsvp.attending ? 'bg-violet-50 text-violet-700' : 'bg-rose-50 text-rose-700'">
                                                {{ rsvp.attending ? 'Asistirá' : 'No asistirá' }}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-xs text-slate-400">{{ formatDate(rsvp.created_at) }}</p>
                                    </div>
                                    <p v-if="rsvp.attending" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700">
                                        {{ rsvp.total_attendees }} {{ rsvp.total_attendees === 1 ? 'persona' : 'personas' }}
                                    </p>
                                </div>

                                <div v-if="rsvp.attending && rsvp.guests?.length" class="mt-4 grid gap-3 sm:grid-cols-2">
                                    <div v-for="(guest, index) in rsvp.guests" :key="`${rsvp.id}-${index}`" class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                                        <p class="text-sm font-semibold text-slate-800">{{ guest.name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ dietaryLabel(guest.dietary_restriction) }}</p>
                                        <p v-if="guest.dietary_comment" class="mt-1 text-xs italic text-slate-600">{{ guest.dietary_comment }}</p>
                                    </div>
                                </div>
                                <p v-if="rsvp.message" class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-sm italic leading-6 text-amber-950">“{{ rsvp.message }}”</p>
                            </article>
                        </div>
                        <div v-else class="px-6 py-14 text-center">
                            <p class="text-lg font-semibold text-slate-700">Todavía no hay confirmaciones</p>
                            <p class="mt-2 text-sm text-slate-500">Las respuestas aparecerán aquí cuando tus invitados completen la reserva.</p>
                        </div>
                    </section>

                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-6 py-5">
                            <h3 class="text-xl font-semibold text-slate-900">Mensajes de cariño</h3>
                            <p class="mt-1 text-sm text-slate-500">Saludos publicados en el muro.</p>
                        </div>
                        <div v-if="messages.length" class="max-h-[760px] divide-y divide-slate-100 overflow-y-auto">
                            <article v-for="message in messages" :key="message.id" class="p-6">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-semibold text-slate-900">{{ message.guest_name }}</p>
                                    <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-medium text-violet-700">{{ categoryLabel(message.category) }}</span>
                                </div>
                                <p class="mt-3 text-sm leading-6 text-slate-600">{{ message.message }}</p>
                                <p class="mt-3 text-xs text-slate-400">{{ formatDate(message.created_at) }}</p>
                            </article>
                        </div>
                        <div v-else class="px-6 py-14 text-center">
                            <p class="text-lg font-semibold text-slate-700">Todavía no hay mensajes</p>
                            <p class="mt-2 text-sm text-slate-500">Los saludos del muro aparecerán en este espacio.</p>
                        </div>
                    </section>
                </div>

                <section class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800">Enlace público</p>
                        <a :href="publicUrl" target="_blank" rel="noopener" class="break-all text-sm text-amber-700 hover:underline">{{ publicUrl }}</a>
                    </div>
                    <DangerButton :disabled="destroyForm.processing" @click="remove">Eliminar invitación</DangerButton>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
