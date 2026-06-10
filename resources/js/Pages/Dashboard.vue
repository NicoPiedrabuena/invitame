<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    invitations: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);

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
    tag: '#4b5f18',
    warm: '#a36d46',
    partyPaper: '#f8eee4',
    partyInk: '#d7907d',
    partyWood: '#d9c2a4',
    partyRibbon: '#8c5b43',
    projectGold: 'linear-gradient(135deg, #d6b47d 0%, #f7efe3 45%, #8b693f 100%)',
    projectNavy: 'linear-gradient(135deg, #23364f 0%, #e7dfd2 48%, #a87a52 100%)',
    textureDark: '#42291b',
};

const projectCards = computed(() => props.invitations.slice(0, 4));

const initials = computed(() => {
    const name = user.value?.name || 'Usuario';

    return name
        .split(' ')
        .map((part) => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase();
});

const formatDate = (value) => {
    if (!value) {
        return 'Editado recientemente';
    }

    return `Editado ${new Date(value).toLocaleDateString('es-AR', {
        day: 'numeric',
        month: 'short',
    })}`;
};
</script>

<template>
    <Head title="Dashboard" />

    <div class="min-h-screen p-0 font-sans md:p-2" :style="{ backgroundColor: theme.shell, color: theme.ink }">
        <div class="mx-auto grid min-h-screen max-w-7xl overflow-hidden border shadow-sm md:min-h-[calc(100vh-1rem)] md:grid-cols-[202px_1fr] md:rounded-[22px]" :style="{ backgroundColor: theme.app, borderColor: theme.frame }">
            <aside class="hidden flex-col justify-between px-5 py-6 md:flex" :style="{ backgroundColor: theme.sidebar }">
                <div>
                    <Link :href="route('dashboard')" class="block font-serif text-xl leading-tight" :style="{ color: theme.ink }">
                        Celebration<br />
                        Memories
                    </Link>
                    <p class="mt-1 text-xs tracking-wide" :style="{ color: theme.muted }">Momentos que perduran</p>

                    <nav class="mt-8 space-y-2 text-sm">
                        <Link class="flex items-center gap-3 rounded-lg px-3 py-3 font-semibold" :style="{ backgroundColor: theme.accentSoft, color: theme.accent }" :href="route('dashboard')">
                            <span class="text-base">⌘</span>
                            Panel
                        </Link>
                        <Link class="flex items-center gap-3 rounded-lg px-3 py-3 transition hover:bg-white/45" :href="route('invitations.create')">
                            <span class="text-base">＋</span>
                            Crear invitación
                        </Link>
                        <Link class="flex items-center gap-3 rounded-lg px-3 py-3 transition hover:bg-white/45" :href="route('dashboard')">
                            <span class="text-base">▣</span>
                            Galería
                        </Link>
                        <Link class="flex items-center gap-3 rounded-lg px-3 py-3 transition hover:bg-white/45" :href="route('dashboard')">
                            <span class="text-base">✉</span>
                            Mis Invitaciones
                        </Link>
                    </nav>

                    <div class="mt-8 border-t pt-5" :style="{ borderColor: theme.line }">
                        <p class="mb-3 text-[10px] font-semibold uppercase tracking-[0.22em]" :style="{ color: theme.muted }">Colecciones</p>
                        <div class="space-y-3 text-xs" :style="{ color: theme.ink }">
                            <p>♡ Weddings</p>
                            <p>♧ Birthdays</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center gap-3 rounded-lg bg-white/60 p-3">
                        <div class="grid h-8 w-8 place-items-center rounded-full text-xs font-bold text-white" :style="{ backgroundColor: theme.ink }">
                            {{ initials }}
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-xs font-bold">{{ user.name }}</p>
                            <p class="text-[10px]" :style="{ color: theme.muted }">Premium Account</p>
                        </div>
                    </div>

                    <Link :href="route('logout')" method="post" as="button" class="flex items-center gap-3 px-3 text-sm">
                        <span>↪</span>
                        Cerrar sesión
                    </Link>
                </div>
            </aside>

            <main class="grid gap-8 px-4 py-6 md:grid-cols-[1fr_222px] md:p-9">
                <section class="px-0 md:px-0">
                    <div class="flex items-start justify-between gap-4 px-0 md:px-0 md:pl-0">
                        <div class="md:px-0">
                            <h1 class="font-serif text-4xl leading-tight md:text-[38px]">Crea Recuerdos Inolvidables</h1>
                            <p class="mt-3 max-w-xl text-sm leading-6" :style="{ color: theme.ink }">
                                Diseña invitaciones que trasciendan el papel. Un espacio cálido para dar vida a los momentos más significativos de tu historia.
                            </p>
                        </div>

                        <div class="flex gap-4 pt-2 text-lg">
                            <button type="button" class="grid h-8 w-8 place-items-center rounded-full transition hover:bg-black/5" aria-label="Notificaciones">♧</button>
                            <Link :href="route('profile.edit')" class="grid h-8 w-8 place-items-center rounded-full transition hover:bg-black/5" aria-label="Ajustes">⚙</Link>
                        </div>
                    </div>

                    <div class="mt-10 grid gap-8 lg:grid-cols-[1fr_222px]">
                        <section class="rounded-xl border px-8 py-7 text-center shadow-sm" :style="{ backgroundColor: theme.card, borderColor: theme.line }">
                            <p class="font-serif text-xl" :style="{ color: theme.accent }">✦ Nuevo Diseño</p>
                            <p class="mx-auto mt-3 max-w-xs text-xs leading-5">Comienza tu viaje creativo ahora mismo. Elige un estilo y hazlo tuyo.</p>

                            <Link :href="route('invitations.create')" class="mx-auto mt-7 flex max-w-xs items-center justify-between rounded-lg px-6 py-5 text-left text-white shadow-lg transition hover:translate-y-[-1px]" :style="{ backgroundColor: theme.accent }">
                                <span>
                                    <span class="block font-serif text-lg font-bold">Empezar a diseñar</span>
                                    <span class="text-xs font-semibold text-white/80">Personaliza tu plantilla ideal</span>
                                </span>
                                <span class="text-3xl">→</span>
                            </Link>
                        </section>

                        <aside class="rounded-2xl p-3" :style="{ backgroundColor: theme.sidebar }">
                            <div class="relative h-52 overflow-hidden rounded-xl" :style="{ backgroundColor: theme.partyWood }">
                                <div class="absolute inset-2 rounded-lg p-4 text-center shadow-inner" :style="{ backgroundColor: theme.partyPaper }">
                                    <p class="mt-3 font-serif text-3xl leading-none" :style="{ color: theme.partyInk }">It's a<br />Party!</p>
                                    <p class="mt-4 text-4xl font-bold" :style="{ color: theme.partyInk }">15</p>
                                </div>
                                <span class="absolute bottom-12 left-4 rounded px-2 py-1 text-[10px] font-bold uppercase text-white" :style="{ backgroundColor: theme.partyRibbon }">Trending</span>
                                <p class="absolute bottom-5 left-4 font-serif text-lg text-white drop-shadow">¡Fiesta de XV!</p>
                            </div>
                            <div class="mt-4 flex items-center justify-between px-3 text-xs">
                                <p class="italic">Nuevos filtros<br />disponibles</p>
                                <Link :href="route('invitations.create')" class="font-medium">Ver más →</Link>
                            </div>
                        </aside>
                    </div>

                    <section class="mt-8">
                        <div class="mb-5 flex items-center justify-between">
                            <h2 class="font-serif text-2xl">Mis Proyectos</h2>
                            <Link :href="route('dashboard')" class="text-xs font-semibold" :style="{ color: theme.accent }">Ver todos</Link>
                        </div>

                        <div v-if="projectCards.length" class="grid gap-6 sm:grid-cols-2">
                            <article v-for="(invitation, index) in projectCards" :key="invitation.id" class="overflow-hidden rounded-xl shadow-sm" :style="{ backgroundColor: theme.card }">
                                <div class="h-40 bg-cover bg-center" :style="{ backgroundImage: index % 2 === 0 ? theme.projectGold : theme.projectNavy }" />
                                <div class="p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <span class="rounded-full px-2 py-1 text-[10px] font-bold uppercase text-white" :style="{ backgroundColor: theme.tag }">
                                            {{ invitation.subtitle || 'Evento' }}
                                        </span>
                                        <Link :href="route('invitations.edit', invitation.id)" class="text-xl leading-none">⋮</Link>
                                    </div>
                                    <h3 class="mt-3 font-serif text-lg font-bold">{{ invitation.title }}</h3>
                                    <p class="text-xs" :style="{ color: theme.muted }">{{ formatDate(invitation.updated_at || invitation.created_at) }}</p>
                                    <div class="mt-4 flex gap-4 text-xs font-semibold">
                                        <Link :href="route('invitations.show', invitation.id)" :style="{ color: theme.accent }">Ver</Link>
                                        <Link :href="route('invitations.edit', invitation.id)" :style="{ color: theme.accent }">Editar</Link>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <div v-else class="rounded-xl border border-dashed p-8 text-center" :style="{ backgroundColor: theme.card, borderColor: theme.line }">
                            <p class="font-serif text-xl">Todavía no tienes invitaciones.</p>
                            <Link :href="route('invitations.create')" class="mt-4 inline-flex rounded-lg px-5 py-3 text-sm font-semibold text-white" :style="{ backgroundColor: theme.accent }">
                                Crear la primera
                            </Link>
                        </div>
                    </section>
                </section>

                <aside class="space-y-6">
                    <section class="rounded-2xl border p-5" :style="{ backgroundColor: theme.surfaceMuted, borderColor: theme.line }">
                        <h2 class="font-serif text-lg font-bold" :style="{ color: theme.accent }">Inspírate</h2>

                        <div class="mt-5 space-y-4">
                            <article class="flex gap-3">
                                <div class="h-11 w-11 rounded-lg bg-white shadow" />
                                <div>
                                    <p class="text-sm font-bold">Texturas: Papel Lino</p>
                                    <p class="text-[11px]" :style="{ color: theme.muted }">Elegancia táctil en cada diseño.</p>
                                </div>
                            </article>

                            <article class="flex gap-3">
                                <div class="h-11 w-11 rounded-lg shadow" :style="{ backgroundColor: theme.textureDark }" />
                                <div>
                                    <p class="text-sm font-bold">Paleta: Atardecer Calmo</p>
                                    <p class="text-[11px]" :style="{ color: theme.muted }">Sage green, terracota y crema.</p>
                                </div>
                            </article>
                        </div>
                    </section>

                    <section class="rounded-2xl border p-5" :style="{ backgroundColor: theme.surface, borderColor: theme.line }">
                        <p class="text-xs uppercase tracking-[0.2em]" :style="{ color: theme.muted }">Actividad</p>
                        <p class="mt-3 font-serif text-3xl">{{ invitations.length }}</p>
                        <p class="text-sm" :style="{ color: theme.muted }">invitaciones guardadas</p>
                    </section>
                </aside>
            </main>
        </div>
    </div>
</template>
