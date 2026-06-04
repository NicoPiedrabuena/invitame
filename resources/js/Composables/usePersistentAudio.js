import { reactive } from 'vue';

const state = reactive({
    started: false,
    playing: false,
    muted: false,
    volume: 0.65,
    source: null,
});

let audio = null;

function buildAudio() {
    if (audio) {
        return audio;
    }

    audio = new Audio();
    audio.loop = true;
    audio.preload = 'metadata';
    audio.volume = state.volume;

    audio.addEventListener('play', () => {
        state.playing = true;
    });

    audio.addEventListener('pause', () => {
        state.playing = false;
    });

    return audio;
}

function setSource(url) {
    if (!url) {
        return;
    }

    const player = buildAudio();

    if (state.source !== url) {
        state.source = url;
        player.src = url;
    }
}

async function start(url) {
    if (url) {
        setSource(url);
    }

    if (!state.source) {
        return;
    }

    const player = buildAudio();
    player.muted = state.muted;

    await player.play();
    state.started = true;
}

function pause() {
    if (!audio) {
        return;
    }

    audio.pause();
}

async function resume() {
    if (!audio) {
        return;
    }

    await audio.play();
}

function toggleMuted() {
    if (!audio) {
        return;
    }

    state.muted = !state.muted;
    audio.muted = state.muted;
}

function setVolume(value) {
    const parsed = Number(value);
    state.volume = Number.isNaN(parsed) ? 0.65 : Math.min(1, Math.max(0, parsed));

    if (audio) {
        audio.volume = state.volume;
    }
}

export function usePersistentAudio() {
    return {
        state,
        setSource,
        start,
        pause,
        resume,
        toggleMuted,
        setVolume,
    };
}
