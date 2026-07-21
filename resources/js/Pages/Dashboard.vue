<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({ invitations: { type: Array, default: () => [] } });
const user = computed(() => usePage().props.auth.user);
const initials = computed(() => (user.value?.name || 'U').split(' ').map(word => word[0]).join('').slice(0, 2).toUpperCase());
const greeting = computed(() => new Date().getHours() < 12 ? 'Buen día' : new Date().getHours() < 19 ? 'Buenas tardes' : 'Buenas noches');
const formatDate = value => value ? new Intl.DateTimeFormat('es-AR', { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(value)) : 'Sin fecha';
const daysUntil = value => value ? Math.ceil((new Date(value) - new Date()) / 86400000) : null;
</script>

<template>
    <Head title="Mi panel" />
    <div class="min-h-screen bg-[#f7f5fc] font-sans text-[#211b35]">
        <aside class="fixed inset-y-0 left-0 z-30 hidden w-[250px] flex-col border-r border-black/[.06] bg-white px-5 py-6 lg:flex">
            <Link href="/" class="flex items-center gap-3 px-2 font-black tracking-[-.04em]"><span class="grid h-10 w-10 place-items-center rounded-xl bg-[#7657ff] text-white shadow-[0_8px_20px_rgba(118,87,255,.25)]">C</span><span class="text-xl">Celebration.</span></Link>
            <nav class="mt-10 space-y-1.5 text-sm font-semibold">
                <Link :href="route('dashboard')" class="flex items-center gap-3 rounded-xl bg-[#eee9ff] px-4 py-3.5 text-[#6d4aff]"><span>⌂</span> Vista general</Link>
                <Link :href="route('invitations.create')" class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[#687069] transition hover:bg-[#f3f6f2] hover:text-[#172019]"><span>＋</span> Nueva invitación</Link>
                <Link :href="route('dashboard')" class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[#687069] transition hover:bg-[#f3f6f2] hover:text-[#172019]"><span>▦</span> Mis invitaciones <span class="ml-auto rounded-full bg-[#edf1ec] px-2 py-0.5 text-[11px]">{{ invitations.length }}</span></Link>
                <Link :href="route('profile.edit')" class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[#687069] transition hover:bg-[#f3f6f2] hover:text-[#172019]"><span>⚙</span> Configuración</Link>
            </nav>
            <div class="mt-auto"><div class="rounded-2xl bg-[#211b35] p-5 text-white"><p class="text-sm font-bold">¿Necesitás ayuda?</p><p class="mt-2 text-xs leading-5 text-white/55">Estamos para acompañarte a crear tu evento.</p><a href="mailto:soporte@celebration.com" class="mt-4 inline-block text-xs font-bold text-[#b9a9ff]">Contactar soporte →</a></div><div class="mt-5 flex items-center gap-3 px-2"><span class="grid h-10 w-10 place-items-center rounded-full bg-[#eee9ff] text-xs font-black text-[#6d4aff]">{{ initials }}</span><div class="min-w-0"><p class="truncate text-sm font-bold">{{ user.name }}</p><p class="truncate text-xs text-[#899089]">{{ user.email }}</p></div><Link :href="route('logout')" method="post" as="button" class="ml-auto text-[#899089]" title="Cerrar sesión">↪</Link></div></div>
        </aside>

        <main class="min-h-screen lg:ml-[250px]">
            <header class="flex items-center justify-between border-b border-black/[.05] bg-white/75 px-5 py-4 backdrop-blur-xl md:px-10 lg:px-12"><Link href="/" class="font-black lg:hidden">Celebration.</Link><div class="hidden lg:block"><p class="text-xs font-bold uppercase tracking-[.14em] text-[#929992]">Panel de control</p></div><div class="flex items-center gap-3"><Link :href="route('profile.edit')" class="grid h-10 w-10 place-items-center rounded-full border border-black/[.07] bg-white">⚙</Link><span class="grid h-10 w-10 place-items-center rounded-full bg-[#eee9ff] text-xs font-black text-[#6d4aff] lg:hidden">{{ initials }}</span></div></header>

            <div class="mx-auto max-w-[1400px] px-5 py-9 md:px-10 lg:px-12 lg:py-11">
                <section class="flex flex-col justify-between gap-6 md:flex-row md:items-end"><div><p class="text-sm font-bold text-[#7657ff]">{{ greeting }}, {{ user.name?.split(' ')[0] }} 👋</p><h1 class="mt-2 text-4xl font-black tracking-[-.045em] md:text-5xl">Tus celebraciones</h1><p class="mt-3 text-[#707970]">Creá, personalizá y administrá todos tus eventos desde acá.</p></div><Link :href="route('invitations.create')" class="inline-flex items-center justify-center gap-3 rounded-full bg-[#7657ff] px-6 py-3.5 text-sm font-extrabold text-white shadow-[0_10px_28px_rgba(118,87,255,.28)] transition hover:-translate-y-0.5 hover:bg-[#6848ee]">＋ Nueva invitación</Link></section>

                <section class="mt-9 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-[22px] border border-black/[.055] bg-white p-5"><p class="text-sm font-semibold text-[#7c847d]">Invitaciones</p><div class="mt-3 flex items-end justify-between"><strong class="text-4xl font-black">{{ invitations.length }}</strong><span class="grid h-10 w-10 place-items-center rounded-xl bg-[#eee9ff] text-[#7657ff]">▦</span></div></div>
                    <div class="rounded-[22px] border border-black/[.055] bg-white p-5"><p class="text-sm font-semibold text-[#7c847d]">Próximo evento</p><div class="mt-3 flex items-end justify-between"><strong class="text-2xl font-black">{{ invitations[0]?.event_date ? formatDate(invitations[0].event_date) : '—' }}</strong><span class="grid h-10 w-10 place-items-center rounded-xl bg-[#f3f1e6]">◷</span></div></div>
                    <div class="rounded-[22px] border border-black/[.055] bg-white p-5"><p class="text-sm font-semibold text-[#7c847d]">Días restantes</p><div class="mt-3 flex items-end justify-between"><strong class="text-4xl font-black">{{ daysUntil(invitations[0]?.event_date) ?? '—' }}</strong><span class="grid h-10 w-10 place-items-center rounded-xl bg-[#f0eafa]">✦</span></div></div>
                    <div class="rounded-[22px] border border-black/[.055] bg-[#211b35] p-5 text-white"><p class="text-sm font-semibold text-white/55">Estado de tu cuenta</p><div class="mt-3 flex items-end justify-between"><strong class="text-2xl font-black">Lista para crear</strong><span class="grid h-10 w-10 place-items-center rounded-xl bg-[#8b70ff] text-white">✓</span></div></div>
                </section>

                <section class="mt-10"><div class="mb-5 flex items-center justify-between"><div><h2 class="text-2xl font-black tracking-[-.025em]">Mis invitaciones</h2><p class="mt-1 text-sm text-[#808880]">Tus proyectos recientes y sus accesos rápidos.</p></div></div>
                    <div v-if="invitations.length" class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        <article v-for="(invitation, index) in invitations" :key="invitation.id" class="group overflow-hidden rounded-[26px] border border-black/[.06] bg-white shadow-[0_12px_40px_rgba(27,49,33,.055)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_55px_rgba(27,49,33,.1)]">
                            <div class="relative h-48 overflow-hidden" :class="index % 3 === 0 ? 'bg-[#dcecdf]' : index % 3 === 1 ? 'bg-[#eee5dc]' : 'bg-[#e9e4f2]'">
                                <div class="absolute -right-12 -top-14 h-48 w-48 rounded-full bg-white/35" /><div class="absolute -bottom-16 -left-12 h-40 w-40 rounded-full bg-white/30" />
                                <div class="absolute inset-0 grid place-items-center p-8 text-center"><div><p class="text-[10px] font-black uppercase tracking-[.25em] opacity-50">{{ invitation.subtitle || 'Evento especial' }}</p><h3 class="mt-3 font-serif text-3xl italic">{{ invitation.title }}</h3><p class="mt-3 text-xs font-semibold opacity-60">{{ formatDate(invitation.event_date) }}</p></div></div>
                                <span class="absolute left-4 top-4 rounded-full bg-white/85 px-3 py-1.5 text-[10px] font-black uppercase tracking-wider text-[#6d4aff] backdrop-blur">Publicada</span>
                            </div>
                            <div class="p-5"><div class="flex items-start justify-between gap-3"><div><h3 class="text-lg font-extrabold">{{ invitation.title }}</h3><p class="mt-1 text-sm text-[#858d86]">{{ invitation.venue_name || 'Ubicación por definir' }}</p></div><Link :href="route('invitations.edit', invitation.id)" class="grid h-9 w-9 place-items-center rounded-full bg-[#f3f6f2]">✎</Link></div><div class="mt-5 flex gap-2"><Link :href="route('invitations.show', invitation.id)" class="flex-1 rounded-full bg-[#172019] px-4 py-2.5 text-center text-xs font-bold text-white">Gestionar</Link><a :href="'/' + invitation.slug" target="_blank" class="rounded-full border border-black/10 px-4 py-2.5 text-xs font-bold">Ver ↗</a></div></div>
                        </article>
                    </div>
                    <div v-else class="rounded-[30px] border border-dashed border-[#c8bfff] bg-white px-6 py-16 text-center"><span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-[#eee9ff] text-3xl text-[#7657ff]">✦</span><h3 class="mt-5 text-2xl font-black">Tu primera invitación empieza acá</h3><p class="mx-auto mt-2 max-w-md text-[#747d75]">Diseñá una experiencia única para compartir con todas las personas que querés.</p><Link :href="route('invitations.create')" class="mt-6 inline-flex rounded-full bg-[#7657ff] px-6 py-3 text-sm font-extrabold text-white">Crear invitación →</Link></div>
                </section>
            </div>
        </main>
    </div>
</template>
