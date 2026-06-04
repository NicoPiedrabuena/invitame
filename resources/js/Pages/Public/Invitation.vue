<script setup>
import { computed, ref } from 'vue';
import { usePersistentAudio } from '@/Composables/usePersistentAudio';

const props = defineProps({
    invitation: {
        type: Object,
        required: true,
    },
});

const { state: audioState, start, pause, resume, toggleMuted, setSource } = usePersistentAudio();
const showBankAlias = ref(false);
const hasStartedExperience = ref(false);

const audioUrl = computed(() => props.invitation.theme_settings?.background_audio_url || null);
const mapEmbeddable = computed(() => props.invitation.google_maps_url?.includes('/maps/embed'));
const sectionBackgrounds = computed(() => props.invitation.theme_settings?.section_backgrounds || {});
const globalTypography = computed(() => props.invitation.theme_settings?.typography?.global || {});
const pageBackgroundColor = computed(() => props.invitation.theme_settings?.appearance?.page_background_color || '#fff7ed');
const driveQrUrl = computed(() => {
    if (!props.invitation.drive_photos_url) {
        return null;
    }

    return `https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encodeURIComponent(props.invitation.drive_photos_url)}`;
});

const resolvedImageUrl = (imagePath) => {
    if (!imagePath) {
        return null;
    }

    return imagePath.startsWith('http') ? imagePath : `/storage/${imagePath}`;
};

const fontFamilyMap = {
    classic_serif: 'Georgia, Cambria, "Times New Roman", Times, serif',
    modern_sans: '"Trebuchet MS", "Segoe UI", Tahoma, Verdana, sans-serif',
    editorial_serif: '"Palatino Linotype", Palatino, "Book Antiqua", serif',
    romantic_script: '"Brush Script MT", "Lucida Handwriting", cursive',
    display_bold: '"Arial Black", Gadget, sans-serif',
};

const primaryTextStyle = computed(() => {
    const primary = globalTypography.value?.primary || {};

    return {
        fontFamily: fontFamilyMap[primary.font_family] || fontFamilyMap.classic_serif,
        color: primary.color || '#0f172a',
    };
});

const secondaryTextStyle = computed(() => {
    const secondary = globalTypography.value?.secondary || {};

    return {
        fontFamily: fontFamilyMap[secondary.font_family] || fontFamilyMap.modern_sans,
        color: secondary.color || '#334155',
    };
});

const coverImageUrl = computed(() => {
    const portada = sectionBackgrounds.value?.portada;

    return portada ? resolvedImageUrl(portada) : null;
});

const coverStyle = computed(() => {
    if (!coverImageUrl.value) {
        return {
            backgroundColor: pageBackgroundColor.value,
        };
    }

    return {
        backgroundImage: `url('${coverImageUrl.value}')`,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        backgroundRepeat: 'no-repeat',
    };
});

const transitionStyle = computed(() => ({
    background: `linear-gradient(180deg, transparent 0%, ${pageBackgroundColor.value} 50%, transparent 100%)`,
}));

const sectionBackgroundStyle = (sectionKey) => {
    const image = sectionBackgrounds.value?.[sectionKey];
    const imageUrl = resolvedImageUrl(image);

    if (!imageUrl) {
        return {
            backgroundColor: pageBackgroundColor.value,
        };
    }

    return {
        backgroundImage: `url('${imageUrl}')`,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        backgroundRepeat: 'no-repeat',
    };
};

const startExperience = async () => {
    hasStartedExperience.value = true;

    if (audioUrl.value) {
        setSource(audioUrl.value);
        await start();
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const togglePlay = async () => {
    if (audioState.playing) {
        pause();
        return;
    }

    await resume();
};
</script>

<template>
    <div class="min-h-screen text-slate-900" :style="[secondaryTextStyle, { backgroundColor: pageBackgroundColor }]">
        <section v-if="!hasStartedExperience" class="mx-auto flex min-h-screen w-full max-w-md flex-col justify-between px-6 pb-10 pt-12" :style="coverStyle">
            <div class="rounded-2xl bg-white/75 px-5 py-4 shadow-lg backdrop-blur-sm">
                <p class="text-xs uppercase tracking-[0.2em]" :style="primaryTextStyle">Invitacion digital</p>
                <h1 class="mt-4 text-5xl font-bold leading-tight" :style="primaryTextStyle">{{ invitation.title }}</h1>
                <p class="mt-3 text-lg" :style="secondaryTextStyle">{{ invitation.subtitle }}</p>
            </div>

            <div class="rounded-2xl bg-white/75 px-5 py-4 shadow-lg backdrop-blur-sm">
                <button
                    class="w-full rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white"
                    @click="startExperience"
                >
                    Ingresar
                </button>
            </div>
        </section>

        <section v-else class="mx-auto w-full max-w-md px-6 pb-16 pt-6">
            <article class="relative mx-auto flex min-h-screen w-full max-w-md flex-col justify-center overflow-hidden rounded-[28px] border border-white/40 p-6 shadow-xl" :style="sectionBackgroundStyle('portada')">
                <div class="rounded-2xl bg-white/75 px-5 py-4 shadow-lg backdrop-blur-sm">
                    <p class="text-xs uppercase tracking-[0.2em]" :style="primaryTextStyle">Invitacion digital</p>
                    <h1 class="mt-4 text-5xl font-bold leading-tight" :style="primaryTextStyle">{{ invitation.title }}</h1>
                    <p class="mt-3 text-lg" :style="secondaryTextStyle">{{ invitation.subtitle }}</p>
                </div>
            </article>

            <div class="h-16 w-full" :style="transitionStyle" />

            <div class="grid grid-cols-2 gap-3" v-if="audioUrl">
                <button class="rounded-2xl border border-slate-400 bg-white/80 px-4 py-2 text-sm" @click="togglePlay">
                    {{ audioState.playing ? 'Pausar' : 'Reproducir' }}
                </button>
                <button class="rounded-2xl border border-slate-400 bg-white/80 px-4 py-2 text-sm" @click="toggleMuted">
                    {{ audioState.muted ? 'Activar sonido' : 'Silenciar' }}
                </button>
            </div>

            <div class="h-16 w-full" :style="transitionStyle" v-if="audioUrl" />

            <article class="relative mx-auto w-full max-w-md overflow-hidden rounded-[28px] border border-white/40 p-5 shadow-xl" :style="sectionBackgroundStyle('cuenta_regresiva')">
                <div class="rounded-2xl bg-white/75 px-5 py-4 shadow-lg backdrop-blur-sm">
                    <h2 class="text-lg font-semibold" :style="primaryTextStyle">Datos del evento</h2>
                    <p class="mt-2 text-sm">{{ invitation.event_date }}</p>
                    <p class="text-sm">{{ invitation.venue_name }}</p>
                    <p class="text-sm" v-if="invitation.address">{{ invitation.address }}</p>
                    <p class="mt-2 text-sm" v-if="invitation.dress_code_description || invitation.dress_code">Dress code: {{ invitation.dress_code_description || invitation.dress_code }}</p>
                </div>
            </article>

            <div class="h-16 w-full" :style="transitionStyle" v-if="invitation.spotify_playlist_url" />

            <article class="relative mx-auto w-full max-w-md overflow-hidden rounded-[28px] border border-white/40 p-5 shadow-xl" v-if="invitation.spotify_playlist_url" :style="sectionBackgroundStyle('musica')">
                <div class="rounded-2xl bg-white/75 px-5 py-4 shadow-lg backdrop-blur-sm">
                    <h2 class="text-lg font-semibold" :style="primaryTextStyle">Spotify</h2>
                    <iframe
                        class="mt-3 h-40 w-full rounded-xl"
                        :src="invitation.spotify_playlist_url"
                        loading="lazy"
                        allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                    />
                </div>
            </article>

            <div class="h-16 w-full" :style="transitionStyle" />

            <article class="relative mx-auto w-full max-w-md overflow-hidden rounded-[28px] border border-white/40 p-5 shadow-xl" :style="sectionBackgroundStyle('ubicacion')">
                <div class="rounded-2xl bg-white/75 px-5 py-4 shadow-lg backdrop-blur-sm">
                    <h2 class="text-lg font-semibold" :style="primaryTextStyle">Google Maps</h2>

                    <iframe
                        v-if="mapEmbeddable"
                        class="mt-3 h-56 w-full rounded-xl"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        :src="invitation.google_maps_url"
                    />

                    <a :href="invitation.google_maps_url" target="_blank" class="mt-3 inline-block text-sm underline">
                        Abrir en Google Maps
                    </a>
                </div>
            </article>

            <div class="h-16 w-full" :style="transitionStyle" v-if="invitation.gifts_message || invitation.bank_alias" />

            <article class="relative mx-auto w-full max-w-md overflow-hidden rounded-[28px] border border-white/40 p-5 shadow-xl" v-if="invitation.gifts_message || invitation.bank_alias" :style="sectionBackgroundStyle('regalos')">
                <div class="rounded-2xl bg-white/75 px-5 py-4 shadow-lg backdrop-blur-sm">
                    <h2 class="text-lg font-semibold" :style="primaryTextStyle">Regalos</h2>
                    <p class="mt-2 text-sm" v-if="invitation.gifts_message">{{ invitation.gifts_message }}</p>

                    <div v-if="invitation.bank_alias" class="mt-3 rounded-2xl bg-amber-50 p-4">
                        <button class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white" @click="showBankAlias = !showBankAlias">
                            {{ showBankAlias ? 'Ocultar cuenta' : 'Ver cuenta' }}
                        </button>
                        <p v-if="showBankAlias" class="mt-3 text-sm font-medium text-slate-900">{{ invitation.bank_alias }}</p>
                    </div>
                </div>
            </article>

            <div class="h-16 w-full" :style="transitionStyle" v-if="invitation.drive_photos_url" />

            <article class="relative mx-auto w-full max-w-md overflow-hidden rounded-[28px] border border-white/40 p-5 shadow-xl" v-if="invitation.drive_photos_url" :style="sectionBackgroundStyle('fotos')">
                <div class="rounded-2xl bg-white/75 px-5 py-4 shadow-lg backdrop-blur-sm">
                    <h2 class="text-lg font-semibold" :style="primaryTextStyle">Fotos</h2>
                    <img v-if="driveQrUrl" :src="driveQrUrl" alt="QR para Drive" class="mt-3 h-48 w-48 rounded-2xl bg-white p-3" />
                    <a :href="invitation.drive_photos_url" target="_blank" class="mt-2 inline-block text-sm underline">
                        Ver album en Drive
                    </a>
                </div>
            </article>

            <div class="h-16 w-full" :style="transitionStyle" v-if="invitation.message_wall_enabled" />

            <article class="relative mx-auto w-full max-w-md overflow-hidden rounded-[28px] border border-white/40 p-5 shadow-xl" v-if="invitation.message_wall_enabled" :style="sectionBackgroundStyle('muro')">
                <div class="rounded-2xl bg-white/75 px-5 py-4 shadow-lg backdrop-blur-sm">
                    <h2 class="text-lg font-semibold" :style="primaryTextStyle">Muro de mensajes</h2>
                    <div class="mt-3 rounded-2xl bg-slate-50 p-4 text-sm">
                        El muro de mensajes estará disponible para tus invitados.
                    </div>
                </div>
            </article>

            <div class="h-16 w-full" :style="transitionStyle" v-if="invitation.rsvp_deadline || invitation.rsvp_message || invitation.rsvp_companions?.length" />

            <article class="relative mx-auto w-full max-w-md overflow-hidden rounded-[28px] border border-white/40 p-5 shadow-xl" v-if="invitation.rsvp_deadline || invitation.rsvp_message || invitation.rsvp_companions?.length" :style="sectionBackgroundStyle('rsvp')">
                <div class="rounded-2xl bg-white/75 px-5 py-4 shadow-lg backdrop-blur-sm">
                    <h2 class="text-lg font-semibold" :style="primaryTextStyle">RSVP</h2>
                    <p class="mt-2 text-sm" v-if="invitation.rsvp_deadline">Confirmar antes de: {{ invitation.rsvp_deadline }}</p>
                    <p class="mt-2 text-sm" v-if="invitation.rsvp_message">{{ invitation.rsvp_message }}</p>

                    <div v-if="invitation.rsvp_companions?.length" class="mt-3 space-y-2">
                        <div v-for="(companion, index) in invitation.rsvp_companions" :key="index" class="rounded-2xl bg-slate-50 p-3 text-sm">
                            <p class="font-semibold">{{ companion.name }}</p>
                            <p v-if="companion.dietary_restrictions">{{ companion.dietary_restrictions }}</p>
                        </div>
                    </div>
                </div>
            </article>

            <div class="h-16 w-full" :style="transitionStyle" v-if="invitation.dress_code_allowed_images?.length || invitation.dress_code_not_allowed_images?.length" />

            <article class="relative mx-auto w-full max-w-md overflow-hidden rounded-[28px] border border-white/40 p-5 shadow-xl" v-if="invitation.dress_code_allowed_images?.length || invitation.dress_code_not_allowed_images?.length" :style="sectionBackgroundStyle('dress_code')">
                <div class="rounded-2xl bg-white/75 px-5 py-4 shadow-lg backdrop-blur-sm">
                    <h2 class="text-lg font-semibold" :style="primaryTextStyle">Referencias visuales</h2>

                    <div v-if="invitation.dress_code_allowed_images?.length" class="mt-3 space-y-2">
                        <p class="text-sm font-medium text-emerald-700">Permitido</p>
                        <div class="grid grid-cols-3 gap-2">
                            <img v-for="image in invitation.dress_code_allowed_images" :key="image" :src="resolvedImageUrl(image)" alt="Permitido" class="h-24 w-full rounded-2xl object-cover" />
                        </div>
                    </div>

                    <div v-if="invitation.dress_code_not_allowed_images?.length" class="mt-4 space-y-2">
                        <p class="text-sm font-medium text-rose-700">No permitido</p>
                        <div class="grid grid-cols-3 gap-2">
                            <img v-for="image in invitation.dress_code_not_allowed_images" :key="image" :src="resolvedImageUrl(image)" alt="No permitido" class="h-24 w-full rounded-2xl object-cover" />
                        </div>
                    </div>
                </div>
            </article>
        </section>
    </div>
</template>
