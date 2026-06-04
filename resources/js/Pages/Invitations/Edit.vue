<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InvitationForm from '@/Pages/Invitations/Partials/InvitationForm.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    invitation: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    slug: props.invitation.slug,
    title: props.invitation.title,
    subtitle: props.invitation.subtitle,
    event_date: props.invitation.event_date ? props.invitation.event_date.slice(0, 16) : '',
    venue_name: props.invitation.venue_name,
    google_maps_url: props.invitation.google_maps_url,
    dress_code: props.invitation.dress_code,
    bank_alias: props.invitation.bank_alias,
    drive_photos_url: props.invitation.drive_photos_url,
    spotify_playlist_url: props.invitation.spotify_playlist_url,
    theme_settings: props.invitation.theme_settings || {
        background_audio_url: '',
    },
});

const submit = () => {
    form.put(route('invitations.update', props.invitation.id));
};
</script>

<template>
    <Head title="Editar invitacion" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Editar invitacion</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <InvitationForm :form="form" submit-label="Guardar cambios" @submit="submit" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
