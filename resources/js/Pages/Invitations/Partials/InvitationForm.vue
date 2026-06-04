<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    form: {
        type: Object,
        required: true,
    },
    submitLabel: {
        type: String,
        default: 'Guardar',
    },
});
</script>

<template>
    <form class="space-y-4" @submit.prevent="$emit('submit')">
        <div>
            <InputLabel for="slug" value="Slug" />
            <TextInput id="slug" v-model="form.slug" class="mt-1 block w-full" required />
            <InputError class="mt-1" :message="form.errors.slug" />
        </div>

        <div>
            <InputLabel for="title" value="Titulo" />
            <TextInput id="title" v-model="form.title" class="mt-1 block w-full" required />
            <InputError class="mt-1" :message="form.errors.title" />
        </div>

        <div>
            <InputLabel for="subtitle" value="Subtitulo" />
            <TextInput id="subtitle" v-model="form.subtitle" class="mt-1 block w-full" />
            <InputError class="mt-1" :message="form.errors.subtitle" />
        </div>

        <div>
            <InputLabel for="event_date" value="Fecha del evento" />
            <TextInput id="event_date" type="datetime-local" v-model="form.event_date" class="mt-1 block w-full" required />
            <InputError class="mt-1" :message="form.errors.event_date" />
        </div>

        <div>
            <InputLabel for="venue_name" value="Lugar" />
            <TextInput id="venue_name" v-model="form.venue_name" class="mt-1 block w-full" required />
            <InputError class="mt-1" :message="form.errors.venue_name" />
        </div>

        <div>
            <InputLabel for="google_maps_url" value="Google Maps URL" />
            <TextInput id="google_maps_url" v-model="form.google_maps_url" class="mt-1 block w-full" required />
            <InputError class="mt-1" :message="form.errors.google_maps_url" />
        </div>

        <div>
            <InputLabel for="dress_code" value="Dress code" />
            <TextInput id="dress_code" v-model="form.dress_code" class="mt-1 block w-full" />
            <InputError class="mt-1" :message="form.errors.dress_code" />
        </div>

        <div>
            <InputLabel for="bank_alias" value="Alias bancario" />
            <TextInput id="bank_alias" v-model="form.bank_alias" class="mt-1 block w-full" />
            <InputError class="mt-1" :message="form.errors.bank_alias" />
        </div>

        <div>
            <InputLabel for="drive_photos_url" value="Google Drive fotos URL" />
            <TextInput id="drive_photos_url" v-model="form.drive_photos_url" class="mt-1 block w-full" />
            <InputError class="mt-1" :message="form.errors.drive_photos_url" />
        </div>

        <div>
            <InputLabel for="spotify_playlist_url" value="Spotify playlist URL" />
            <TextInput id="spotify_playlist_url" v-model="form.spotify_playlist_url" class="mt-1 block w-full" />
            <InputError class="mt-1" :message="form.errors.spotify_playlist_url" />
        </div>

        <div>
            <InputLabel for="theme_background_audio_url" value="Audio de fondo URL" />
            <TextInput
                id="theme_background_audio_url"
                v-model="form.theme_settings.background_audio_url"
                class="mt-1 block w-full"
            />
        </div>

        <PrimaryButton :disabled="form.processing">
            {{ submitLabel }}
        </PrimaryButton>
    </form>
</template>
