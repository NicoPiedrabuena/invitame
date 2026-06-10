<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

const theme = {
    shell: '#f7eee6',
    app: '#fff7ef',
    frame: '#d8c8b6',
    sidebar: '#faead7',
    surface: '#fffaf5',
    card: '#ffffff',
    surfaceMuted: '#efe7dc',
    line: '#ead8c2',
    ink: '#15120f',
    muted: '#7c7168',
    accent: '#40540f',
    accentSoft: '#e9ddcc',
    warm: '#a36d46',
    blush: '#d7907d',
    image: 'https://images.unsplash.com/photo-1529634806980-85c3dd6d34ac?auto=format&fit=crop&w=1400&q=85',
};
</script>

<template>
    <Head title="Celebration Memories" />

    <div class="min-h-screen p-0 font-sans md:p-2" :style="{ backgroundColor: theme.shell, color: theme.ink }">
        <main class="mx-auto min-h-screen max-w-7xl overflow-hidden border shadow-sm md:min-h-[calc(100vh-1rem)] md:rounded-[22px]" :style="{ backgroundColor: theme.app, borderColor: theme.frame }">
            <section class="relative min-h-screen overflow-hidden">
                <img class="absolute inset-0 h-full w-full object-cover" :src="theme.image" alt="Invitaciones elegantes sobre una mesa" />
                <div class="absolute inset-0 bg-gradient-to-r from-[#fff7ef] via-[#fff7ef]/92 to-[#fff7ef]/35" />

                <div class="relative flex min-h-screen flex-col px-5 py-5 md:px-10 md:py-8">
                    <header class="flex items-center justify-between gap-4">
                        <Link :href="route('dashboard')" class="font-serif text-xl leading-tight">
                            Celebration<br />
                            Memories
                        </Link>

                        <nav v-if="canLogin" class="flex items-center gap-2 text-sm">
                            <Link
                                v-if="$page.props.auth.user"
                                :href="route('dashboard')"
                                class="rounded-full px-4 py-2 font-semibold text-white"
                                :style="{ backgroundColor: theme.accent }"
                            >
                                Panel
                            </Link>

                            <template v-else>
                                <Link :href="route('login')" class="rounded-full px-4 py-2 font-semibold transition hover:bg-white/60">
                                    Ingresar
                                </Link>

                                <Link
                                    v-if="canRegister"
                                    :href="route('register')"
                                    class="rounded-full px-4 py-2 font-semibold text-white"
                                    :style="{ backgroundColor: theme.accent }"
                                >
                                    Crear cuenta
                                </Link>
                            </template>
                        </nav>
                    </header>

                    <div class="grid flex-1 items-center gap-10 py-16 lg:grid-cols-[minmax(0,1fr)_430px]">
                        <div class="max-w-3xl">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em]" :style="{ color: theme.warm }">Invitaciones digitales</p>
                            <h1 class="mt-5 font-serif text-5xl leading-[0.95] md:text-7xl">
                                Celebration Memories
                            </h1>
                            <p class="mt-6 max-w-xl text-base leading-7 md:text-lg">
                                Crea invitaciones elegantes, personalizables y listas para compartir. Diseña la experiencia completa del evento desde un solo lugar.
                            </p>

                            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                                <Link
                                    :href="$page.props.auth.user ? route('invitations.create') : route('register')"
                                    class="inline-flex items-center justify-center rounded-xl px-6 py-4 text-sm font-bold text-white shadow-lg transition hover:translate-y-[-1px]"
                                    :style="{ backgroundColor: theme.accent }"
                                >
                                    Empezar a diseñar
                                    <span class="ml-3 text-xl">→</span>
                                </Link>
                                <Link
                                    :href="$page.props.auth.user ? route('dashboard') : route('login')"
                                    class="inline-flex items-center justify-center rounded-xl border px-6 py-4 text-sm font-bold transition hover:bg-white/50"
                                    :style="{ borderColor: theme.line }"
                                >
                                    Ver mi panel
                                </Link>
                            </div>
                        </div>

                        <aside class="rounded-2xl border p-4 shadow-xl backdrop-blur" :style="{ backgroundColor: `${theme.surface}e8`, borderColor: theme.line }">
                            <div class="rounded-xl p-4" :style="{ backgroundColor: theme.sidebar }">
                                <div class="overflow-hidden rounded-xl bg-white shadow-sm">
                                    <div class="h-56 bg-cover bg-center" :style="{ backgroundImage: `url(${theme.image})` }" />
                                    <div class="p-5">
                                        <span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase text-white" :style="{ backgroundColor: theme.tag || theme.accent }">Nuevo diseño</span>
                                        <h2 class="mt-4 font-serif text-3xl">Sofía & Tomás</h2>
                                        <p class="mt-2 text-sm" :style="{ color: theme.muted }">Boda · 12 de octubre · RSVP online</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-3 gap-3 text-center text-xs">
                                <div class="rounded-xl p-3" :style="{ backgroundColor: theme.accentSoft }">
                                    <p class="font-serif text-2xl">9</p>
                                    <p :style="{ color: theme.muted }">secciones</p>
                                </div>
                                <div class="rounded-xl p-3" :style="{ backgroundColor: theme.accentSoft }">
                                    <p class="font-serif text-2xl">QR</p>
                                    <p :style="{ color: theme.muted }">fotos</p>
                                </div>
                                <div class="rounded-xl p-3" :style="{ backgroundColor: theme.accentSoft }">
                                    <p class="font-serif text-2xl">RSVP</p>
                                    <p :style="{ color: theme.muted }">invitados</p>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </section>

            <section class="grid gap-5 px-5 py-12 md:grid-cols-3 md:px-10" :style="{ backgroundColor: theme.app }">
                <article class="rounded-2xl border p-6" :style="{ backgroundColor: theme.card, borderColor: theme.line }">
                    <p class="font-serif text-2xl">Diseño guiado</p>
                    <p class="mt-3 text-sm leading-6" :style="{ color: theme.muted }">Un asistente por pasos para portada, música, regalos, ubicación, fotos y confirmaciones.</p>
                </article>
                <article class="rounded-2xl border p-6" :style="{ backgroundColor: theme.card, borderColor: theme.line }">
                    <p class="font-serif text-2xl">Estilo editable</p>
                    <p class="mt-3 text-sm leading-6" :style="{ color: theme.muted }">Colores, tipografías y fondos listos para adaptar cada invitación a la identidad del evento.</p>
                </article>
                <article class="rounded-2xl border p-6" :style="{ backgroundColor: theme.card, borderColor: theme.line }">
                    <p class="font-serif text-2xl">Lista para compartir</p>
                    <p class="mt-3 text-sm leading-6" :style="{ color: theme.muted }">Cada proyecto genera una URL pública para enviar a invitados sin pasos extra.</p>
                </article>
            </section>
        </main>
    </div>
</template>
