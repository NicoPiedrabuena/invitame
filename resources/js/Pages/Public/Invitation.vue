<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { usePersistentAudio } from '@/Composables/usePersistentAudio';

const props = defineProps({
    invitation: {
        type: Object,
        required: true,
    },
    messages: {
        type: Array,
        default: () => [],
    },
});

const { state: audioState, start, pause, resume, toggleMuted, setSource } = usePersistentAudio();
const showBankAlias = ref(false);
const showDressCodeImages = ref(false);
const showMessageModal = ref(false);
const showSpotifyModal = ref(false);
const spotifySuccessMessage = ref('');
const spotifySearchQuery = ref('');
const spotifySearchResults = ref([]);
const spotifySearchError = ref('');
const spotifySearchLoading = ref(false);
const spotifyAddingUri = ref('');
const hasStartedExperience = ref(false);
const youtubePlayerHost = ref(null);
const youtubePlayer = ref(null);
const youtubePlaying = ref(false);
const youtubeReady = ref(false);
const now = ref(new Date());
const revealElements = new Set();
let countdownTimer = null;
let revealObserver = null;

const messageForm = useForm({
    guest_name: '',
    category: 'otros',
    message: '',
});

const rsvpForm = useForm({
    attending: true,
    guest_name: '',
    guests: [
        {
            name: '',
            dietary_restriction: 'ninguna',
            dietary_comment: '',
        },
    ],
    message: '',
});

const messageFilter = ref('todos');

const audioUrl = computed(() => props.invitation.theme_settings?.background_audio_url || null);
const youtubeVideoId = computed(() => {
    const url = props.invitation.youtube_music_url;

    if (!url) return null;

    try {
        const parsed = new URL(url);
        if (parsed.hostname === 'youtu.be') return parsed.pathname.split('/').filter(Boolean)[0] || null;
        if (parsed.hostname.endsWith('youtube.com')) {
            if (parsed.pathname === '/watch') return parsed.searchParams.get('v');
            const parts = parsed.pathname.split('/').filter(Boolean);
            if (['embed', 'shorts', 'live'].includes(parts[0])) return parts[1] || null;
        }
    } catch {
        return null;
    }

    return null;
});
const hasBackgroundMusic = computed(() => Boolean(youtubeVideoId.value || audioUrl.value));
const sectionBackgrounds = computed(() => props.invitation.theme_settings?.section_backgrounds || {});
const globalTypography = computed(() => props.invitation.theme_settings?.typography?.global || {});
const pageBackgroundColor = computed(() => props.invitation.theme_settings?.appearance?.page_background_color || '#ffffff');
const driveQrUrl = computed(() => {
    if (!props.invitation.drive_photos_url) {
        return null;
    }

    return `https://api.qrserver.com/v1/create-qr-code/?size=260x260&data=${encodeURIComponent(props.invitation.drive_photos_url)}`;
});

const spotifyEmbedUrl = computed(() => {
    const playlistUrl = props.invitation.spotify_playlist_url;

    if (!playlistUrl) {
        return null;
    }

    if (playlistUrl.includes('/embed/playlist/')) {
        return playlistUrl;
    }

    const match = playlistUrl.match(/open\.spotify\.com\/playlist\/([A-Za-z0-9]+)/);

    if (!match) {
        return playlistUrl;
    }

    return `https://open.spotify.com/embed/playlist/${match[1]}?utm_source=generator`;
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

const dressCodeText = computed(() => props.invitation.dress_code_description || props.invitation.dress_code || null);
const dressCodeLines = computed(() => (dressCodeText.value || '')
    .split(/\r?\n/)
    .map((line) => line.trim())
    .filter(Boolean));
const dressCodeTitle = computed(() => dressCodeLines.value[0] || dressCodeText.value || '');
const dressCodeDetail = computed(() => dressCodeLines.value.slice(1).join(' '));
const hasDressCodeImages = computed(() => Boolean(
    props.invitation.dress_code_allowed_images?.length
    || props.invitation.dress_code_not_allowed_images?.length,
));
const wallMessages = computed(() => props.messages || []);
const messageCategories = [
    { label: 'Todos', value: 'todos' },
    { label: 'Amigos', value: 'amigos' },
    { label: 'Familia', value: 'familia' },
    { label: 'Otros', value: 'otros' },
];
const filteredWallMessages = computed(() => {
    if (messageFilter.value === 'todos') {
        return wallMessages.value;
    }

    return wallMessages.value.filter((message) => message.category === messageFilter.value);
});
const formatMessageDate = (dateValue) => {
    if (!dateValue) {
        return '';
    }

    const date = new Date(dateValue);

    if (Number.isNaN(date.getTime())) {
        return '';
    }

    return new Intl.DateTimeFormat('es-AR', {
        day: '2-digit',
        month: '2-digit',
        year: '2-digit',
    }).format(date);
};
const dietaryOptions = [
    { label: 'Sin restricciones', value: 'ninguna' },
    { label: 'Vegetariano', value: 'vegetariano' },
    { label: 'Vegano', value: 'vegano' },
    { label: 'Celiaco', value: 'celiaco' },
    { label: 'Diabetico', value: 'diabetico' },
    { label: 'Otro', value: 'otro' },
];
const rsvpDeadlineText = computed(() => {
    if (!props.invitation.rsvp_deadline) {
        return null;
    }

    const date = new Date(props.invitation.rsvp_deadline);

    if (Number.isNaN(date.getTime())) {
        return props.invitation.rsvp_deadline;
    }

    return new Intl.DateTimeFormat('es-AR', {
        day: 'numeric',
        month: 'long',
    }).format(date);
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

const loadYoutubeApi = () => new Promise((resolve) => {
    if (window.YT?.Player) {
        resolve(window.YT);
        return;
    }

    const previousReady = window.onYouTubeIframeAPIReady;
    window.onYouTubeIframeAPIReady = () => {
        previousReady?.();
        resolve(window.YT);
    };

    if (!document.querySelector('script[src="https://www.youtube.com/iframe_api"]')) {
        const script = document.createElement('script');
        script.src = 'https://www.youtube.com/iframe_api';
        document.head.appendChild(script);
    }
});

const startYoutube = async () => {
    if (!youtubeVideoId.value) return;

    const YT = await loadYoutubeApi();
    youtubePlayer.value = new YT.Player(youtubePlayerHost.value, {
        width: '1',
        height: '1',
        videoId: youtubeVideoId.value,
        playerVars: { autoplay: 1, controls: 0, loop: 1, playlist: youtubeVideoId.value, playsinline: 1 },
        events: {
            onReady: (event) => {
                youtubeReady.value = true;
                event.target.playVideo();
            },
            onStateChange: (event) => {
                youtubePlaying.value = event.data === YT.PlayerState.PLAYING;
            },
        },
    });
};

const startExperience = async () => {
    hasStartedExperience.value = true;

    if (youtubeVideoId.value) {
        await startYoutube();
    } else if (audioUrl.value) {
        setSource(audioUrl.value);
        await start();
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const togglePlay = async () => {
    if (youtubeVideoId.value && youtubePlayer.value) {
        if (youtubePlaying.value) youtubePlayer.value.pauseVideo();
        else youtubePlayer.value.playVideo();
        return;
    }

    if (audioState.playing) {
        pause();
        return;
    }

    await resume();
};

const submitWallMessage = () => {
    messageForm.post(route('invitations.public.messages.store', props.invitation.slug), {
        preserveScroll: true,
        onSuccess: () => {
            messageForm.reset();
            messageForm.category = 'otros';
            showMessageModal.value = false;
        },
    });
};

const addRsvpGuest = () => {
    if (rsvpForm.guests.length >= 20) {
        return;
    }

    rsvpForm.guests.push({
        name: '',
        dietary_restriction: 'ninguna',
        dietary_comment: '',
    });
};

const removeRsvpGuest = () => {
    if (rsvpForm.guests.length <= 1) {
        return;
    }

    rsvpForm.guests.pop();
};

const setRsvpAttendance = (attending) => {
    rsvpForm.attending = attending;

    if (attending && !rsvpForm.guests.length) {
        addRsvpGuest();
    }
};

const submitRsvp = () => {
    rsvpForm.guest_name = rsvpForm.guests[0]?.name || rsvpForm.guest_name;

    rsvpForm.post(route('invitations.public.rsvp.store', props.invitation.slug), {
        preserveScroll: true,
        onSuccess: () => {
            rsvpForm.reset();
            rsvpForm.attending = true;
            rsvpForm.guests = [
                {
                    name: '',
                    dietary_restriction: 'ninguna',
                    dietary_comment: '',
                },
            ];
        },
    });
};

const searchSpotifyTracks = async () => {
    spotifySuccessMessage.value = '';
    spotifySearchError.value = '';
    spotifySearchResults.value = [];

    if (!spotifySearchQuery.value.trim()) {
        spotifySearchError.value = 'Escribi una cancion o artista para buscar.';
        return;
    }

    spotifySearchLoading.value = true;

    try {
        const response = await window.axios.get(route('invitations.public.spotify.tracks.search', props.invitation.slug), {
            params: {
                query: spotifySearchQuery.value,
            },
        });

        spotifySearchResults.value = response.data.tracks || [];

        if (!spotifySearchResults.value.length) {
            spotifySearchError.value = 'No encontramos canciones con esa busqueda.';
        }
    } catch (error) {
        spotifySearchError.value = error.response?.data?.message
            || error.response?.data?.errors?.song_query?.[0]
            || 'No se pudo buscar en Spotify.';
    } finally {
        spotifySearchLoading.value = false;
    }
};

const addSpotifySearchResult = async (track) => {
    spotifySearchError.value = '';
    spotifyAddingUri.value = track.uri;

    try {
        await window.axios.post(route('invitations.public.spotify.tracks.add', props.invitation.slug), {
            track_uri: track.uri,
        });

        spotifySuccessMessage.value = `${track.name} se agrego a la playlist.`;
        showSpotifyModal.value = false;
        spotifySearchQuery.value = '';
        spotifySearchResults.value = [];
    } catch (error) {
        spotifySearchError.value = error.response?.data?.message
            || error.response?.data?.errors?.track_uri?.[0]
            || 'No se pudo agregar la cancion.';
    } finally {
        spotifyAddingUri.value = '';
    }
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
    youtubePlayer.value?.destroy?.();
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
        <div v-if="youtubeVideoId" ref="youtubePlayerHost" class="pointer-events-none fixed -left-[9999px] top-0 h-px w-px overflow-hidden" aria-hidden="true" />
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

            <button
                v-if="hasBackgroundMusic"
                type="button"
                class="fixed bottom-5 right-5 z-40 grid h-14 w-14 place-items-center rounded-full bg-pink-700 text-white shadow-2xl transition hover:scale-105 hover:bg-pink-800 focus:outline-none focus:ring-4 focus:ring-pink-300"
                :aria-label="(youtubeVideoId ? youtubePlaying : audioState.playing) ? 'Pausar música' : 'Reanudar música'"
                :title="(youtubeVideoId ? youtubePlaying : audioState.playing) ? 'Pausar música' : 'Reanudar música'"
                @click="togglePlay"
            >
                <svg v-if="youtubeVideoId ? youtubePlaying : audioState.playing" viewBox="0 0 24 24" class="h-6 w-6 fill-current" aria-hidden="true"><path d="M6 5h4v14H6zm8 0h4v14h-4z" /></svg>
                <svg v-else viewBox="0 0 24 24" class="ml-0.5 h-7 w-7 fill-current" aria-hidden="true"><path d="M8 5v14l11-7z" /></svg>
            </button>

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

            <article v-reveal class="public-invitation-section relative mx-auto flex min-h-screen w-full max-w-md flex-col items-center justify-center overflow-hidden px-6 py-16 text-center" v-if="dressCodeText || hasDressCodeImages" :style="sectionBackgroundStyle('dress_code')">
                <div class="w-full">
                    <div class="mx-auto flex h-24 w-24 items-center justify-center">
                        <span class="relative block h-20 w-20">
                            <span class="absolute left-1/2 top-2 h-14 w-14 -translate-x-1/2 rotate-45 border-[5px] border-pink-700" />
                            <span class="absolute left-[18px] top-[18px] h-[5px] w-11 bg-pink-700" />
                            <span class="absolute left-[28px] top-[6px] h-[44px] w-[5px] rotate-[32deg] bg-pink-700" />
                            <span class="absolute right-[28px] top-[6px] h-[44px] w-[5px] rotate-[-32deg] bg-pink-700" />
                        </span>
                    </div>

                    <p class="mt-4 text-6xl leading-none md:text-7xl" :style="dateAccentStyle">Dress code</p>
                    <p class="mx-auto mt-6 max-w-xs text-4xl font-semibold leading-tight md:text-5xl" :style="dateNumberStyle" v-if="dressCodeTitle">
                        {{ dressCodeTitle }}
                    </p>
                    <p class="mx-auto mt-4 max-w-sm text-xl font-semibold italic leading-snug text-pink-700" v-if="dressCodeDetail">
                        {{ dressCodeDetail }}
                    </p>

                    <button
                        v-if="hasDressCodeImages"
                        type="button"
                        class="mt-8 inline-flex rounded-full bg-pink-700 px-10 py-3 text-sm font-semibold lowercase text-white shadow-sm transition hover:bg-pink-800"
                        @click="showDressCodeImages = !showDressCodeImages"
                    >
                        {{ showDressCodeImages ? 'ocultar referencias' : 'ver referencias' }}
                    </button>

                    <div v-if="showDressCodeImages && invitation.dress_code_allowed_images?.length" class="mt-8 space-y-3">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-pink-700">Permitido</p>
                        <div class="grid grid-cols-3 gap-2">
                            <img v-for="image in invitation.dress_code_allowed_images" :key="image" :src="resolvedImageUrl(image)" alt="Permitido" class="h-24 w-full rounded-2xl object-cover shadow" />
                        </div>
                    </div>

                    <div v-if="showDressCodeImages && invitation.dress_code_not_allowed_images?.length" class="mt-6 space-y-3">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-pink-700">No permitido</p>
                        <div class="grid grid-cols-3 gap-2">
                            <img v-for="image in invitation.dress_code_not_allowed_images" :key="image" :src="resolvedImageUrl(image)" alt="No permitido" class="h-24 w-full rounded-2xl object-cover shadow" />
                        </div>
                    </div>
                </div>
            </article>

            <article v-reveal class="public-invitation-section relative mx-auto flex min-h-screen w-full max-w-md flex-col items-center justify-center overflow-hidden px-6 py-16 text-center" v-if="invitation.gifts_message || invitation.bank_alias" :style="sectionBackgroundStyle('regalos')">
                <div class="mx-auto flex min-h-[78vh] w-full max-w-[340px] flex-col items-center justify-center rounded-[50%] bg-pink-700 px-9 py-14 text-white shadow-xl">
                    <div class="relative h-20 w-20">
                        <span class="absolute bottom-1 left-1/2 h-12 w-12 -translate-x-1/2 rounded-b-lg border-[5px] border-white" />
                        <span class="absolute bottom-12 left-1/2 h-4 w-16 -translate-x-1/2 rounded-sm bg-white" />
                        <span class="absolute bottom-1 left-1/2 h-16 w-[5px] -translate-x-1/2 bg-white" />
                        <span class="absolute left-[18px] top-1 h-10 w-7 rounded-full border-[4px] border-white" />
                        <span class="absolute right-[18px] top-1 h-10 w-7 rounded-full border-[4px] border-white" />
                    </div>

                    <p class="mt-6 text-6xl leading-none text-white md:text-7xl" :style="{ fontFamily: secondaryTextStyle.fontFamily }">Regalos</p>
                    <p class="mt-6 text-lg font-bold italic leading-snug text-white" v-if="invitation.gifts_message">
                        {{ invitation.gifts_message }}
                    </p>
                    <p class="mt-6 text-lg font-bold italic leading-snug text-white" v-else>
                        Nada es mas importante que tu presencia, pero si deseas hacerme un presente, podes depositarlo en la siguiente cuenta
                    </p>

                    <button
                        v-if="invitation.bank_alias"
                        class="mt-10 w-full rounded-full bg-white px-8 py-3 text-lg font-medium lowercase text-pink-500 shadow-sm transition hover:bg-pink-50"
                        @click="showBankAlias = !showBankAlias"
                    >
                        {{ showBankAlias ? 'ocultar cuenta' : 'ver cuenta' }}
                    </button>
                    <p v-if="showBankAlias" class="mt-5 rounded-2xl bg-white/15 px-4 py-3 text-base font-semibold text-white">{{ invitation.bank_alias }}</p>
                </div>
            </article>

            <article v-reveal class="public-invitation-section relative mx-auto flex min-h-screen w-full max-w-md flex-col items-center justify-center overflow-hidden px-6 py-16 text-center" v-if="invitation.drive_photos_url" :style="sectionBackgroundStyle('fotos')">
                <div class="w-full">
                    <svg class="mx-auto h-20 w-20 text-pink-700" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                        <rect x="9" y="19" width="46" height="32" rx="5" stroke="currentColor" stroke-width="4" />
                        <path d="M22 19l4-7h12l4 7" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M48 16h5" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                        <circle cx="32" cy="35" r="10" stroke="currentColor" stroke-width="4" />
                        <circle cx="29" cy="31" r="2" fill="currentColor" />
                    </svg>

                    <p class="mt-4 text-6xl leading-none md:text-7xl" :style="dateAccentStyle">Fotos</p>
                    <p class="mx-auto mt-5 max-w-sm text-3xl font-medium leading-tight text-pink-700 md:text-4xl">
                        Si sacas fotos, subilas!
                    </p>

                    <div class="mx-auto mt-8 grid h-48 w-48 place-items-center rounded-3xl bg-white p-4 shadow-lg md:h-56 md:w-56">
                        <img v-if="driveQrUrl" :src="driveQrUrl" alt="QR para subir fotos" class="h-full w-full object-contain" />
                    </div>

                    <a :href="invitation.drive_photos_url" target="_blank" rel="noopener" class="mt-8 inline-flex rounded-full bg-pink-700 px-16 py-4 text-base font-semibold text-white shadow-sm transition hover:bg-pink-800">
                        Subir aca
                    </a>
                </div>
            </article>

            <article v-reveal class="public-invitation-section relative mx-auto flex min-h-screen w-full max-w-md flex-col items-center justify-center overflow-hidden px-6 py-16 text-center" v-if="invitation.spotify_playlist_url" :style="sectionBackgroundStyle('musica')">
                <div class="w-full">
                    <svg class="mx-auto h-24 w-24 text-pink-700" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                        <path d="M31 12v30" stroke="currentColor" stroke-width="7" stroke-linecap="round" />
                        <path d="M31 12l19-5v28" stroke="currentColor" stroke-width="7" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="22" cy="45" r="9" fill="currentColor" />
                        <circle cx="48" cy="38" r="9" fill="currentColor" />
                    </svg>

                    <p class="mt-2 text-6xl leading-none md:text-7xl" :style="dateAccentStyle">Musica</p>
                    <p class="mx-auto mt-5 max-w-sm text-3xl font-medium leading-tight text-pink-700 md:text-4xl">
                        Agrega musica a la playlist
                    </p>

                    <iframe
                        class="mx-auto mt-8 h-[360px] w-full max-w-[320px] rounded-[28px] shadow-2xl"
                        :src="spotifyEmbedUrl"
                        loading="lazy"
                        allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                    />

                    <p v-if="spotifySuccessMessage" class="mx-auto mt-5 max-w-xs rounded-full bg-white/85 px-4 py-2 text-sm font-semibold text-pink-700 shadow-sm">
                        {{ spotifySuccessMessage }}
                    </p>

                    <button
                        type="button"
                        class="mt-8 inline-flex rounded-full bg-pink-700 px-20 py-4 text-base font-semibold text-white shadow-sm transition hover:bg-pink-800"
                        @click="showSpotifyModal = true"
                    >
                        Agregar
                    </button>
                </div>
            </article>

            <article v-reveal class="public-invitation-section relative mx-auto flex min-h-screen w-full max-w-md flex-col items-center justify-center overflow-hidden px-6 py-16 text-center" v-if="invitation.message_wall_enabled" :style="sectionBackgroundStyle('muro')">
                <div class="mx-auto flex min-h-[82vh] w-full max-w-[350px] flex-col rounded-[44%] bg-pink-700 px-7 py-9 text-white shadow-xl">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-lg bg-white text-pink-700">
                        <span class="text-2xl leading-none">♥</span>
                    </div>

                    <h2 class="mt-4 text-5xl leading-none text-white md:text-6xl" :style="{ fontFamily: secondaryTextStyle.fontFamily }">Mensajes</h2>
                    <p class="mt-3 text-xs text-white">Deja tu mensaje especial</p>

                    <div class="mt-4 flex flex-wrap justify-center gap-2">
                        <button
                            v-for="category in messageCategories"
                            :key="category.value"
                            type="button"
                            class="rounded-full border border-white px-3 py-1 text-[11px] lowercase transition"
                            :class="messageFilter === category.value ? 'bg-white text-pink-700' : 'text-white hover:bg-white/15'"
                            @click="messageFilter = category.value"
                        >
                            {{ category.label }}
                        </button>
                    </div>

                    <div class="mt-4 max-h-[486px] min-h-0 space-y-3 overflow-y-auto pr-1 invitation-message-scroll">
                        <article v-for="message in filteredWallMessages" :key="message.id" class="min-h-[72px] rounded-2xl bg-white p-3 text-left text-pink-700 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-sm font-bold">{{ message.guest_name }}</p>
                                <p class="shrink-0 text-[11px]">{{ formatMessageDate(message.created_at) }}</p>
                            </div>
                            <p class="mt-1 line-clamp-2 whitespace-pre-line text-xs leading-5 text-slate-500">{{ message.message }}</p>
                        </article>

                        <div v-if="!filteredWallMessages.length" class="rounded-2xl bg-white/90 p-4 text-sm text-pink-700">
                            Todavia no hay mensajes en esta categoria.
                        </div>
                    </div>

                    <button
                        type="button"
                        class="mt-4 w-full rounded-full bg-white px-5 py-3 text-sm font-semibold text-pink-500 shadow-sm transition hover:bg-pink-50"
                        @click="showMessageModal = true"
                    >
                        Escribir aca
                    </button>
                </div>
            </article>

            <article v-reveal class="public-invitation-section relative mx-auto flex min-h-screen w-full max-w-md flex-col items-center justify-center overflow-hidden px-6 py-16 text-center" :style="sectionBackgroundStyle('rsvp')">
                <div class="w-full rounded-[34px] bg-pink-700 px-5 py-8 text-white shadow-xl">
                    <h2 class="text-5xl leading-none text-white md:text-6xl" :style="{ fontFamily: secondaryTextStyle.fontFamily }">Confirmar asistencia</h2>
                    <p class="mt-3 text-sm font-semibold" v-if="rsvpDeadlineText">Confirmar asistencia<br />antes del {{ rsvpDeadlineText }}</p>
                    <p class="mt-3 text-sm" v-if="invitation.rsvp_message">{{ invitation.rsvp_message }}</p>

                    <div class="mt-8 space-y-3">
                        <button type="button" class="w-full rounded-full px-5 py-3 text-sm font-bold italic shadow-sm transition" :class="rsvpForm.attending ? 'bg-white text-pink-700' : 'bg-white/70 text-pink-500'" @click="setRsvpAttendance(true)">
                            Si, voy a asistir
                        </button>
                        <button type="button" class="w-full rounded-full px-5 py-3 text-sm font-bold italic shadow-sm transition" :class="!rsvpForm.attending ? 'bg-white text-pink-700' : 'bg-white/70 text-pink-500'" @click="setRsvpAttendance(false)">
                            No podre asistir
                        </button>
                    </div>

                    <form class="mt-6 rounded-3xl bg-white px-4 py-5 text-pink-700 shadow-lg" @submit.prevent="submitRsvp">
                        <template v-if="rsvpForm.attending">
                        <p class="text-sm font-medium">Cantidad de invitados que confirmas</p>
                        <div class="mt-4 flex items-center justify-center gap-4">
                            <button type="button" class="grid h-8 w-8 place-items-center rounded-full bg-pink-300 text-lg font-bold text-white disabled:opacity-50" :disabled="rsvpForm.guests.length <= 1" @click="removeRsvpGuest">-</button>
                            <span class="min-w-24 text-sm font-semibold">{{ rsvpForm.guests.length }} {{ rsvpForm.guests.length === 1 ? 'persona' : 'personas' }}</span>
                            <button type="button" class="grid h-8 w-8 place-items-center rounded-full bg-pink-700 text-lg font-bold text-white" @click="addRsvpGuest">+</button>
                        </div>

                        <div class="mt-6 max-h-[430px] space-y-5 overflow-y-auto pr-1 invitation-message-scroll">
                            <div v-for="(guest, index) in rsvpForm.guests" :key="index" class="space-y-2">
                                <p class="text-xs font-bold uppercase tracking-[0.2em]">Invitado {{ index + 1 }}</p>
                                <label class="block text-xs font-semibold">Nombre y apellido</label>
                                <input v-model="guest.name" type="text" class="block w-full rounded-xl border-pink-200 px-3 py-2 text-sm text-pink-700 focus:border-pink-600 focus:ring-pink-600" placeholder="Nombre completo" />
                                <p v-if="rsvpForm.errors[`guests.${index}.name`]" class="text-left text-xs font-semibold text-rose-600">{{ rsvpForm.errors[`guests.${index}.name`] }}</p>

                                <label class="block text-xs font-semibold">Restriccion alimentaria</label>
                                <select v-model="guest.dietary_restriction" class="block w-full rounded-xl border-pink-200 px-3 py-2 text-sm text-pink-700 focus:border-pink-600 focus:ring-pink-600">
                                    <option v-for="option in dietaryOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
                                </select>

                                <label class="block text-xs font-semibold">Aclaraciones</label>
                                <input v-model="guest.dietary_comment" type="text" class="block w-full rounded-xl border-pink-200 px-3 py-2 text-sm text-pink-700 focus:border-pink-600 focus:ring-pink-600" placeholder="Ej: alergias, detalles, etc." />
                            </div>
                        </div>
                        </template>

                        <div v-else class="space-y-2">
                            <label class="block text-xs font-semibold">Nombre y apellido</label>
                            <input v-model="rsvpForm.guest_name" type="text" class="block w-full rounded-xl border-pink-200 px-3 py-2 text-sm text-pink-700 focus:border-pink-600 focus:ring-pink-600" placeholder="Nombre completo" />
                        </div>

                        <div class="mt-5 space-y-2">
                            <label class="block text-xs font-semibold">Mensaje opcional</label>
                            <textarea v-model="rsvpForm.message" rows="3" class="block w-full resize-none rounded-xl border-pink-200 px-3 py-2 text-sm text-pink-700 focus:border-pink-600 focus:ring-pink-600" placeholder="Deja un mensaje privado" />
                        </div>

                        <input v-model="rsvpForm.guest_name" type="hidden" />

                        <button type="submit" class="mt-5 w-full rounded-full bg-pink-700 px-5 py-3 text-sm font-bold italic text-white shadow-sm disabled:opacity-60" :disabled="rsvpForm.processing">
                            {{ rsvpForm.processing ? 'Enviando...' : 'Enviar' }}
                        </button>
                        <p v-if="rsvpForm.errors.guest_name" class="mt-2 text-xs font-semibold text-rose-600">{{ rsvpForm.errors.guest_name }}</p>
                    </form>
                </div>
            </article>

            </section>
        </Transition>

        <Teleport to="body">
            <Transition name="invitation-enter">
                <div v-if="showMessageModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/55 px-5 py-8 backdrop-blur-sm">
                    <form class="w-full max-w-sm rounded-3xl bg-white p-5 shadow-2xl" @submit.prevent="submitWallMessage">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-3xl leading-none" :style="dateAccentStyle">Nuevo mensaje</h2>
                                <p class="mt-2 text-sm text-slate-500">Completa tus datos para dejar un saludo.</p>
                            </div>
                            <button type="button" class="rounded-full px-3 py-1 text-2xl leading-none text-pink-700 hover:bg-pink-50" @click="showMessageModal = false">
                                x
                            </button>
                        </div>

                        <div class="mt-5 space-y-4">
                            <div>
                                <label for="wall_guest_name" class="text-xs font-semibold uppercase tracking-[0.18em] text-pink-700">Nombre</label>
                                <input id="wall_guest_name" v-model="messageForm.guest_name" type="text" maxlength="120" class="mt-2 block w-full rounded-2xl border-pink-100 bg-white px-4 py-3 text-sm text-pink-700 shadow-sm placeholder:text-pink-300 focus:border-pink-600 focus:ring-pink-600" placeholder="Tu nombre" />
                                <p v-if="messageForm.errors.guest_name" class="mt-1 text-xs font-semibold text-rose-600">{{ messageForm.errors.guest_name }}</p>
                            </div>

                            <div>
                                <label for="wall_category" class="text-xs font-semibold uppercase tracking-[0.18em] text-pink-700">Categoria</label>
                                <select id="wall_category" v-model="messageForm.category" class="mt-2 block w-full rounded-2xl border-pink-100 bg-white px-4 py-3 text-sm text-pink-700 shadow-sm focus:border-pink-600 focus:ring-pink-600">
                                    <option value="amigos">Amigos</option>
                                    <option value="familia">Familia</option>
                                    <option value="otros">Otros</option>
                                </select>
                                <p v-if="messageForm.errors.category" class="mt-1 text-xs font-semibold text-rose-600">{{ messageForm.errors.category }}</p>
                            </div>

                            <div>
                                <label for="wall_message" class="text-xs font-semibold uppercase tracking-[0.18em] text-pink-700">Comentario</label>
                                <textarea id="wall_message" v-model="messageForm.message" rows="5" maxlength="1000" class="mt-2 block w-full resize-none rounded-2xl border-pink-100 bg-white px-4 py-3 text-sm text-pink-700 shadow-sm placeholder:text-pink-300 focus:border-pink-600 focus:ring-pink-600" placeholder="Escribi tu mensaje" />
                                <p v-if="messageForm.errors.message" class="mt-1 text-xs font-semibold text-rose-600">{{ messageForm.errors.message }}</p>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <button type="button" class="rounded-full bg-pink-50 px-4 py-3 text-sm font-semibold text-pink-700" @click="showMessageModal = false">
                                cancelar
                            </button>
                            <button type="submit" class="rounded-full bg-pink-700 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-pink-800 disabled:opacity-60" :disabled="messageForm.processing">
                                {{ messageForm.processing ? 'publicando...' : 'publicar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </Transition>

            <Transition name="invitation-enter">
                <div v-if="showSpotifyModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/55 px-5 py-8 backdrop-blur-sm">
                    <div class="w-full max-w-md rounded-[28px] bg-white p-6 text-slate-950 shadow-2xl">
                        <div class="flex items-start justify-between gap-4">
                            <h2 class="text-2xl font-black leading-tight">Agregar Cancion</h2>
                            <button type="button" class="rounded-full px-2 py-1 text-3xl leading-none text-slate-500 hover:bg-slate-100" @click="showSpotifyModal = false">
                                x
                            </button>
                        </div>

                        <form class="mt-6 flex items-center gap-2" @submit.prevent="searchSpotifyTracks">
                            <input
                                id="spotify_song_query"
                                v-model="spotifySearchQuery"
                                type="search"
                                maxlength="255"
                                class="block min-w-0 flex-1 rounded-full border border-slate-950 bg-white px-4 py-2 text-sm text-slate-950 shadow-sm placeholder:text-slate-400 focus:border-slate-950 focus:ring-slate-950"
                                placeholder="Buscar cancion o artista..."
                            />
                            <button type="submit" class="shrink-0 rounded-full bg-black px-6 py-2 text-xs font-black uppercase text-white transition hover:bg-slate-800 disabled:opacity-60" :disabled="spotifySearchLoading">
                                {{ spotifySearchLoading ? '...' : 'Buscar' }}
                            </button>
                        </form>

                        <p v-if="spotifySearchError" class="mt-3 text-sm font-semibold text-rose-600">{{ spotifySearchError }}</p>

                        <div class="mt-6 max-h-[420px] space-y-5 overflow-y-auto pr-2 invitation-message-scroll">
                            <article v-for="track in spotifySearchResults" :key="track.uri" class="flex items-center gap-3">
                                <div class="grid h-12 w-12 shrink-0 place-items-center overflow-hidden rounded-md bg-slate-100">
                                    <img v-if="track.image_url" :src="track.image_url" :alt="track.name" class="h-full w-full object-cover" />
                                    <span v-else class="text-xs font-black text-slate-400">SP</span>
                                </div>

                                <div class="min-w-0 flex-1 text-left">
                                    <p class="truncate text-base font-black leading-tight">{{ track.name }}</p>
                                    <p class="truncate text-sm leading-tight text-slate-500">{{ track.artist }}</p>
                                </div>

                                <button
                                    type="button"
                                    class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-black text-2xl font-black leading-none text-white transition hover:bg-slate-800 disabled:opacity-50"
                                    :disabled="spotifyAddingUri === track.uri"
                                    @click="addSpotifySearchResult(track)"
                                    :aria-label="`Agregar ${track.name}`"
                                >
                                    {{ spotifyAddingUri === track.uri ? '...' : '+' }}
                                </button>
                            </article>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>
