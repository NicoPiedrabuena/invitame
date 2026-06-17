<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
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
const now = ref(new Date());
const revealElements = new Set();
let countdownTimer = null;
let revealObserver = null;

const audioUrl = computed(() => props.invitation.theme_settings?.background_audio_url || null);
const sectionBackgrounds = computed(() => props.invitation.theme_settings?.section_backgrounds || {});
const globalTypography = computed(() => props.invitation.theme_settings?.typography?.global || {});
const pageBackgroundColor = computed(() => props.invitation.theme_settings?.appearance?.page_background_color || '#ffffff');
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

const eventDate = computed(() => {
    if (!props.invitation.event_date) {
        return null;
    }

    const date = new Date(props.invitation.event_date);

    return Number.isNaN(date.getTime()) ? null : date;
});

const eventEndDate = computed(() => {
    if (!props.invitation.event_end_date) {
        return eventDate.value ? new Date(eventDate.value.getTime() + 4 * 60 * 60 * 1000) : null;
    }

    const date = new Date(props.invitation.event_end_date);

    return Number.isNaN(date.getTime()) ? null : date;
});

const countdown = computed(() => {
    if (!eventDate.value) {
        return [
            { label: 'dias', value: '00' },
            { label: 'horas', value: '00' },
            { label: 'minutos', value: '00' },
            { label: 'segundos', value: '00' },
        ];
    }

    const diff = Math.max(0, eventDate.value.getTime() - now.value.getTime());
    const totalSeconds = Math.floor(diff / 1000);
    const days = Math.floor(totalSeconds / 86400);
    const hours = Math.floor((totalSeconds % 86400) / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;

    return [
        { label: 'dias', value: String(days).padStart(2, '0') },
        { label: 'horas', value: String(hours).padStart(2, '0') },
        { label: 'minutos', value: String(minutes).padStart(2, '0') },
        { label: 'segundos', value: String(seconds).padStart(2, '0') },
    ];
});

const eventDateDisplay = computed(() => {
    if (!eventDate.value) {
        return {
            weekday: '',
            day: '--',
            month: '--',
            year: '--',
        };
    }

    const weekday = new Intl.DateTimeFormat('es-AR', { weekday: 'long' })
        .format(eventDate.value)
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');

    return {
        weekday: weekday.charAt(0).toUpperCase() + weekday.slice(1),
        day: String(eventDate.value.getDate()).padStart(2, '0'),
        month: String(eventDate.value.getMonth() + 1).padStart(2, '0'),
        year: String(eventDate.value.getFullYear()).slice(-2),
    };
});

const eventTimeDisplay = computed(() => {
    const formatTime = (date) => new Intl.DateTimeFormat('es-AR', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    }).format(date);

    return {
        start: eventDate.value ? formatTime(eventDate.value) : '--:--',
        end: eventEndDate.value ? formatTime(eventEndDate.value) : '--:--',
    };
});

const dateAccentStyle = computed(() => ({
    ...secondaryTextStyle.value,
    color: '#be185d',
}));

const dateNumberStyle = computed(() => ({
    ...primaryTextStyle.value,
    color: '#be185d',
}));

const calendarDateFormat = (date) => date
    .toISOString()
    .replace(/[-:]/g, '')
    .replace(/\.\d{3}Z$/, 'Z');

const googleCalendarUrl = computed(() => {
    if (!eventDate.value) {
        return null;
    }

    const start = eventDate.value;
    const end = eventEndDate.value ?? new Date(start.getTime() + 4 * 60 * 60 * 1000);
    const details = [
        props.invitation.subtitle,
        props.invitation.address ? `Direccion: ${props.invitation.address}` : null,
    ].filter(Boolean).join('\n');

    const params = new URLSearchParams({
        action: 'TEMPLATE',
        text: props.invitation.title || 'Invitacion',
        dates: `${calendarDateFormat(start)}/${calendarDateFormat(end)}`,
        details,
        location: [props.invitation.venue_name, props.invitation.address].filter(Boolean).join(', '),
    });

    return `https://calendar.google.com/calendar/render?${params.toString()}`;
});

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

const vReveal = {
    mounted(el) {
        el.classList.add('reveal-on-scroll');
        revealElements.add(el);

        if (revealObserver) {
            revealObserver.observe(el);
        }
    },
    unmounted(el) {
        revealElements.delete(el);

        if (revealObserver) {
            revealObserver.unobserve(el);
        }
    },
};

onMounted(() => {
    countdownTimer = window.setInterval(() => {
        now.value = new Date();
    }, 1000);

    if (!('IntersectionObserver' in window)) {
        revealElements.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('is-visible');
            revealObserver.unobserve(entry.target);
        });
    }, {
        threshold: 0.18,
    });

    revealElements.forEach((el) => revealObserver.observe(el));
});

onBeforeUnmount(() => {
    if (countdownTimer) {
        window.clearInterval(countdownTimer);
    }

    if (revealObserver) {
        revealObserver.disconnect();
    }
});
</script>

<template>
    <div class="min-h-screen text-slate-900" :style="[secondaryTextStyle, { backgroundColor: pageBackgroundColor, '--page-bg': pageBackgroundColor }]">
        <Transition name="invitation-enter" mode="out-in">
            <section v-if="!hasStartedExperience" key="intro" class="mx-auto flex min-h-screen w-full max-w-md flex-col justify-between px-6 pb-10 pt-20 text-center" :style="coverStyle">
                <div class="pt-16">
                    <h1 class="text-5xl font-bold leading-none md:text-6xl" :style="primaryTextStyle">{{ invitation.title }}</h1>
                    <p class="mt-4 text-6xl leading-none md:text-7xl" :style="secondaryTextStyle">{{ invitation.subtitle }}</p>
                </div>

                <div class="space-y-3">
                    <button
                        class="w-full rounded-full bg-white/85 px-5 py-3 text-sm font-semibold lowercase text-pink-700 shadow-lg backdrop-blur-sm transition hover:bg-white"
                        @click="startExperience"
                    >
                        Ingresar
                    </button>
                </div>
            </section>

            <section v-else key="invitation" class="mx-auto w-full max-w-md pb-16">
            <article v-reveal class="public-invitation-section relative mx-auto flex min-h-screen w-full max-w-md flex-col justify-between overflow-hidden px-6 pb-10 pt-20 text-center" :style="sectionBackgroundStyle('portada')">
                <div class="pt-16">
                    <h1 class="text-5xl font-bold leading-none md:text-6xl" :style="primaryTextStyle">{{ invitation.title }}</h1>
                    <p class="mt-4 text-6xl leading-none md:text-7xl" :style="secondaryTextStyle">{{ invitation.subtitle }}</p>
                </div>

                <div class="space-y-3">
                    <div class="grid grid-cols-4 gap-1 bg-white px-3 py-4 shadow-lg">
                        <div v-for="item in countdown" :key="item.label" class="min-w-0">
                            <p class="text-3xl font-black leading-none text-pink-700 md:text-4xl">{{ item.value }}</p>
                            <p class="mt-1 text-[11px] lowercase text-pink-500 md:text-xs">{{ item.label }}</p>
                        </div>
                    </div>

                    <a
                        v-if="googleCalendarUrl"
                        :href="googleCalendarUrl"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex rounded-full bg-pink-700 px-10 py-3 text-sm font-semibold lowercase text-white shadow-sm transition hover:bg-pink-800"
                    >
                        agregar al calendario
                    </a>
                </div>
            </article>

            <div v-reveal class="grid grid-cols-2 gap-3" v-if="audioUrl">
                <button class="rounded-2xl border border-slate-400 bg-white/80 px-4 py-2 text-sm" @click="togglePlay">
                    {{ audioState.playing ? 'Pausar' : 'Reproducir' }}
                </button>
                <button class="rounded-2xl border border-slate-400 bg-white/80 px-4 py-2 text-sm" @click="toggleMuted">
                    {{ audioState.muted ? 'Activar sonido' : 'Silenciar' }}
                </button>
            </div>

            <article v-reveal class="public-invitation-section relative mx-auto flex min-h-screen w-full max-w-md flex-col items-center justify-center overflow-hidden px-6 py-16 text-center" :style="sectionBackgroundStyle('cuenta_regresiva')">
                <div class="w-full">
                    <p class="text-6xl leading-none md:text-7xl" :style="dateAccentStyle">{{ eventDateDisplay.weekday }}</p>
                    <div class="mt-4 flex flex-col items-center">
                        <span class="block text-[112px] font-light leading-[0.82] md:text-[132px]" :style="dateNumberStyle">{{ eventDateDisplay.day }}</span>
                        <span class="block text-[112px] font-light leading-[0.82] md:text-[132px]" :style="dateNumberStyle">{{ eventDateDisplay.month }}</span>
                        <span class="block text-[112px] font-light leading-[0.82] md:text-[132px]" :style="dateNumberStyle">{{ eventDateDisplay.year }}</span>
                    </div>
                </div>
            </article>

            <article v-reveal class="public-invitation-section relative mx-auto flex min-h-screen w-full max-w-md flex-col items-center justify-center overflow-hidden px-6 py-16 text-center" :style="sectionBackgroundStyle('ubicacion')">
                <div class="w-full">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-pink-700 text-white shadow-lg">
                        <span class="relative block h-12 w-9 rounded-t-full rounded-b-[22px] bg-white/0">
                            <span class="absolute left-1/2 top-0 h-12 w-9 -translate-x-1/2 rounded-t-full rounded-b-[22px] bg-pink-700" />
                            <span class="absolute left-1/2 top-4 h-4 w-4 -translate-x-1/2 rounded-full bg-white" />
                            <span class="absolute bottom-[-7px] left-1/2 h-6 w-6 -translate-x-1/2 rotate-45 bg-pink-700" />
                        </span>
                    </div>

                    <p class="mt-6 text-6xl leading-none md:text-7xl" :style="dateAccentStyle">Lugar</p>
                    <h2 class="mt-3 text-4xl font-semibold leading-tight md:text-5xl" :style="dateNumberStyle">{{ invitation.venue_name }}</h2>
                    <p class="mx-auto mt-3 max-w-xs text-sm text-pink-700" v-if="invitation.address">{{ invitation.address }}</p>

                    <a
                        :href="invitation.google_maps_url"
                        target="_blank"
                        rel="noopener"
                        class="mt-8 inline-flex rounded-full bg-pink-700 px-12 py-4 text-base font-semibold lowercase text-white shadow-sm transition hover:bg-pink-800"
                    >
                        ver en Google Maps
                    </a>
                </div>
            </article>

            <article v-reveal class="public-invitation-section relative mx-auto flex min-h-screen w-full max-w-md flex-col items-center justify-center overflow-hidden px-6 py-16 text-center" :style="sectionBackgroundStyle('ubicacion')">
                <div class="w-full">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-pink-700 shadow-lg">
                        <span class="relative block h-12 w-12 rounded-full border-[5px] border-white">
                            <span class="absolute left-1/2 top-1/2 h-5 w-[5px] -translate-x-1/2 -translate-y-full rounded-full bg-white" />
                            <span class="absolute left-1/2 top-1/2 h-[5px] w-4 -translate-y-1/2 rounded-full bg-white" />
                        </span>
                    </div>

                    <p class="mt-6 text-6xl leading-none md:text-7xl" :style="dateAccentStyle">Hora</p>
                    <p class="mt-8 text-4xl font-semibold leading-tight md:text-5xl" :style="dateNumberStyle">
                        {{ eventTimeDisplay.start }} hs a {{ eventTimeDisplay.end }} hs
                    </p>
                </div>
            </article>

            <article v-reveal class="public-invitation-section relative mx-auto flex min-h-[70vh] w-full max-w-md flex-col justify-center overflow-hidden p-5" v-if="invitation.spotify_playlist_url" :style="sectionBackgroundStyle('musica')">
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

            <article v-reveal class="public-invitation-section relative mx-auto flex min-h-[70vh] w-full max-w-md flex-col justify-center overflow-hidden p-5" v-if="invitation.gifts_message || invitation.bank_alias" :style="sectionBackgroundStyle('regalos')">
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

            <article v-reveal class="public-invitation-section relative mx-auto flex min-h-[70vh] w-full max-w-md flex-col justify-center overflow-hidden p-5" v-if="invitation.drive_photos_url" :style="sectionBackgroundStyle('fotos')">
                <div class="rounded-2xl bg-white/75 px-5 py-4 shadow-lg backdrop-blur-sm">
                    <h2 class="text-lg font-semibold" :style="primaryTextStyle">Fotos</h2>
                    <img v-if="driveQrUrl" :src="driveQrUrl" alt="QR para Drive" class="mt-3 h-48 w-48 rounded-2xl bg-white p-3" />
                    <a :href="invitation.drive_photos_url" target="_blank" class="mt-2 inline-block text-sm underline">
                        Ver album en Drive
                    </a>
                </div>
            </article>

            <article v-reveal class="public-invitation-section relative mx-auto flex min-h-[70vh] w-full max-w-md flex-col justify-center overflow-hidden p-5" v-if="invitation.message_wall_enabled" :style="sectionBackgroundStyle('muro')">
                <div class="rounded-2xl bg-white/75 px-5 py-4 shadow-lg backdrop-blur-sm">
                    <h2 class="text-lg font-semibold" :style="primaryTextStyle">Muro de mensajes</h2>
                    <div class="mt-3 rounded-2xl bg-slate-50 p-4 text-sm">
                        El muro de mensajes estará disponible para tus invitados.
                    </div>
                </div>
            </article>

            <article v-reveal class="public-invitation-section relative mx-auto flex min-h-[70vh] w-full max-w-md flex-col justify-center overflow-hidden p-5" v-if="invitation.rsvp_deadline || invitation.rsvp_message || invitation.rsvp_companions?.length" :style="sectionBackgroundStyle('rsvp')">
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

            <article v-reveal class="public-invitation-section relative mx-auto flex min-h-[70vh] w-full max-w-md flex-col justify-center overflow-hidden p-5" v-if="invitation.dress_code_allowed_images?.length || invitation.dress_code_not_allowed_images?.length" :style="sectionBackgroundStyle('dress_code')">
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
        </Transition>
    </div>
</template>
