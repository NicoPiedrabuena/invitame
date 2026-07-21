<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({ canLogin: Boolean, canRegister: Boolean });

const features = [
    { icon: 'calendar', title: 'Cuenta regresiva', text: 'La fecha especial siempre presente, con todos los detalles del evento.' },
    { icon: 'check', title: 'Confirmación RSVP', text: 'Recibí confirmaciones y acompañantes en tiempo real desde tu panel.' },
    { icon: 'camera', title: 'Álbum colaborativo', text: 'Tus invitados pueden compartir sus mejores fotos en un mismo lugar.' },
    { icon: 'music', title: 'Música y mensajes', text: 'Una experiencia personal con playlist, dedicatorias y muro de deseos.' },
];

const steps = [
    ['01', 'Creá tu evento', 'Completá la información principal y elegí el estilo que mejor cuenta tu historia.'],
    ['02', 'Personalizalo', 'Ajustá colores, tipografías, fotos y cada sección desde un editor simple.'],
    ['03', 'Compartí y celebrá', 'Enviá tu enlace único y gestioná las respuestas desde un solo lugar.'],
];
</script>

<template>
    <Head title="Invitaciones digitales que emocionan" />
    <div class="landing min-h-screen bg-[#fbfcfa] text-[#161a17]">
        <header class="fixed inset-x-0 top-0 z-50 px-4 pt-4">
            <nav class="mx-auto flex max-w-7xl items-center justify-between rounded-[24px] border border-black/[0.06] bg-white/90 px-5 py-3 shadow-[0_14px_45px_rgba(20,50,30,0.08)] backdrop-blur-xl md:px-7">
                <Link href="/" class="flex items-center gap-3 font-black tracking-[-0.04em] text-[#172019]">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-[#7657ff] text-white shadow-[0_8px_22px_rgba(118,87,255,.28)]">C</span>
                    <span class="text-lg md:text-xl">Celebration.</span>
                </Link>
                <div class="hidden items-center gap-7 text-sm font-medium text-[#606861] lg:flex">
                    <a href="#inicio" class="transition hover:text-[#6d4aff]">Inicio</a>
                    <a href="#funciones" class="transition hover:text-[#6d4aff]">Funcionalidades</a>
                    <a href="#como-funciona" class="transition hover:text-[#6d4aff]">Cómo funciona</a>
                    <a href="#inspiracion" class="transition hover:text-[#6d4aff]">Inspiración</a>
                </div>
                <div v-if="canLogin" class="flex items-center gap-2">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="rounded-full bg-[#211b35] px-5 py-2.5 text-sm font-bold text-white transition hover:bg-[#6d4aff]">Mi panel</Link>
                    <template v-else>
                        <Link :href="route('login')" class="hidden px-3 py-2 text-sm font-semibold sm:block">Ingresar</Link>
                        <Link v-if="canRegister" :href="route('register')" class="rounded-full bg-[#7657ff] px-5 py-2.5 text-sm font-bold text-white shadow-[0_8px_22px_rgba(118,87,255,.28)] transition hover:-translate-y-0.5 hover:bg-[#6848ee]">Crear invitación</Link>
                    </template>
                </div>
            </nav>
        </header>

        <main>
            <section id="inicio" class="relative overflow-hidden px-5 pb-20 pt-36 md:pb-28 md:pt-44">
                <div class="pointer-events-none absolute left-[-10rem] top-28 h-80 w-80 rounded-full bg-[#ded6ff]/70 blur-3xl" />
                <div class="pointer-events-none absolute right-[-7rem] top-44 h-96 w-96 rounded-full bg-[#ffe2ef]/70 blur-3xl" />
                <div class="relative mx-auto grid max-w-7xl items-center gap-16 lg:grid-cols-[.9fr_1.1fr]">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full border border-[#d7ceff] bg-[#f4f1ff] px-4 py-2 text-xs font-extrabold uppercase tracking-[0.15em] text-[#6d4aff]">
                            <span>✦</span> Invitaciones digitales únicas
                        </div>
                        <h1 class="mt-7 max-w-3xl text-[3.6rem] font-black leading-[.92] tracking-[-.065em] sm:text-7xl xl:text-[5.6rem]">
                            Momentos que<br />merecen ser<br /><span class="bg-gradient-to-r from-[#7657ff] to-[#e45b98] bg-clip-text text-transparent">inolvidables.</span>
                        </h1>
                        <p class="mt-7 max-w-xl text-lg leading-8 text-[#677069] md:text-xl">Diseñá una invitación tan especial como tu evento. Moderna, personal, fácil de compartir y con todo lo que necesitás.</p>
                        <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                            <Link :href="$page.props.auth.user ? route('invitations.create') : route('register')" class="inline-flex items-center justify-center gap-3 rounded-full bg-[#7657ff] px-7 py-4 font-extrabold text-white shadow-[0_14px_35px_rgba(118,87,255,.3)] transition hover:-translate-y-1 hover:bg-[#6848ee]">
                                Crear mi invitación <span class="text-xl">→</span>
                            </Link>
                            <a href="#inspiracion" class="inline-flex items-center justify-center rounded-full border border-black/10 bg-white px-7 py-4 font-bold transition hover:border-[#7657ff]">Ver cómo funciona</a>
                        </div>
                        <div class="mt-8 flex flex-wrap gap-x-6 gap-y-2 text-sm font-semibold text-[#687169]">
                            <span>✓ 100% personalizable</span><span>✓ Sin conocimientos técnicos</span>
                        </div>
                    </div>

                    <div class="relative mx-auto h-[520px] w-full max-w-[610px] md:h-[620px]">
                        <div class="absolute left-[4%] top-[5%] h-[490px] w-[255px] -rotate-[7deg] overflow-hidden rounded-[38px] border-[9px] border-white bg-[#e9dccb] shadow-[0_35px_75px_rgba(25,45,31,.2)] md:h-[570px] md:w-[295px]">
                            <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=700&q=85" alt="Celebración de boda" class="h-full w-full object-cover" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/65 via-transparent to-white/10" />
                            <div class="absolute inset-x-5 bottom-10 text-center text-white"><p class="text-xs font-bold uppercase tracking-[.25em]">Nos casamos</p><p class="mt-2 font-serif text-4xl italic">Sofi & Tomi</p><p class="mt-2 text-sm">12 · 10 · 2026</p></div>
                        </div>
                        <div class="absolute right-[2%] top-[15%] h-[455px] w-[235px] rotate-[7deg] overflow-hidden rounded-[36px] border-[9px] border-white bg-[#dce6d5] shadow-[0_35px_75px_rgba(25,45,31,.2)] md:h-[535px] md:w-[275px]">
                            <img src="https://images.unsplash.com/photo-1531058020387-3be344556be6?auto=format&fit=crop&w=700&q=85" alt="Fiesta especial" class="h-full w-full object-cover" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent" />
                            <div class="absolute inset-x-4 bottom-9 text-center text-white"><p class="text-xs font-bold uppercase tracking-[.2em]">Mis quince</p><p class="mt-2 font-serif text-4xl italic">Emma</p></div>
                        </div>
                        <div class="absolute right-0 top-24 rounded-2xl border border-white/70 bg-white/90 px-4 py-3 shadow-xl backdrop-blur"><p class="text-[10px] font-bold uppercase text-[#869087]">RSVP</p><p class="mt-1 font-bold"><span class="mr-2 text-[#45c86a]">✓</span>Confirmado</p></div>
                        <div class="absolute bottom-12 left-[33%] rounded-2xl border border-white/70 bg-white/90 px-4 py-3 shadow-xl backdrop-blur"><p class="text-[10px] font-bold uppercase text-[#869087]">Nuevo mensaje</p><p class="mt-1 font-bold">¡Qué emoción! 🥂</p></div>
                    </div>
                </div>
            </section>

            <section id="funciones" class="bg-[#211b35] px-5 py-24 text-white md:py-28">
                <div class="mx-auto max-w-7xl">
                    <div class="max-w-2xl"><p class="text-xs font-black uppercase tracking-[.2em] text-[#a995ff]">Todo en un solo lugar</p><h2 class="mt-4 text-4xl font-black tracking-[-.045em] md:text-6xl">Más que una invitación.</h2><p class="mt-5 text-lg leading-8 text-white/60">Una experiencia completa para vos y para cada persona que forma parte de tu celebración.</p></div>
                    <div class="mt-14 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <article v-for="feature in features" :key="feature.title" class="group rounded-[28px] border border-white/10 bg-white/[.055] p-7 transition duration-300 hover:-translate-y-1 hover:bg-white/[.09]">
                            <div class="grid h-12 w-12 place-items-center rounded-2xl bg-gradient-to-br from-[#8b70ff] to-[#e45b98] text-xl text-white">✦</div>
                            <h3 class="mt-7 text-xl font-extrabold">{{ feature.title }}</h3><p class="mt-3 text-sm leading-6 text-white/55">{{ feature.text }}</p>
                        </article>
                    </div>
                </div>
            </section>

            <section id="como-funciona" class="px-5 py-24 md:py-32">
                <div class="mx-auto max-w-7xl"><div class="text-center"><p class="text-xs font-black uppercase tracking-[.2em] text-[#6d4aff]">Simple de principio a fin</p><h2 class="mt-4 text-4xl font-black tracking-[-.045em] md:text-6xl">Tu invitación en 3 pasos</h2></div>
                    <div class="mt-16 grid gap-5 md:grid-cols-3"><article v-for="step in steps" :key="step[0]" class="relative overflow-hidden rounded-[30px] border border-black/[.07] bg-white p-8 shadow-[0_15px_50px_rgba(46,31,91,.07)]"><span class="text-6xl font-black tracking-[-.06em] text-[#e7e1ff]">{{ step[0] }}</span><h3 class="mt-8 text-2xl font-extrabold">{{ step[1] }}</h3><p class="mt-4 leading-7 text-[#69716b]">{{ step[2] }}</p></article></div>
                </div>
            </section>

            <section id="inspiracion" class="px-5 pb-24"><div class="mx-auto max-w-7xl overflow-hidden rounded-[38px] bg-gradient-to-br from-[#eeeaff] via-[#f7f3ff] to-[#ffeaf3] px-6 py-16 text-center md:px-16 md:py-20"><p class="text-xs font-black uppercase tracking-[.2em] text-[#6d4aff]">Tu historia empieza acá</p><h2 class="mx-auto mt-4 max-w-3xl text-4xl font-black tracking-[-.05em] md:text-6xl">Creá algo que todos van a recordar.</h2><p class="mx-auto mt-5 max-w-2xl text-lg text-[#665f70]">Todo listo para diseñar, compartir y disfrutar tu evento desde hoy.</p><Link :href="$page.props.auth.user ? route('invitations.create') : route('register')" class="mt-8 inline-flex rounded-full bg-[#211b35] px-8 py-4 font-extrabold text-white transition hover:-translate-y-1">Empezar ahora →</Link></div></section>
        </main>
        <footer class="border-t border-black/[.06] px-5 py-8"><div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 text-sm text-[#69716b] sm:flex-row"><strong class="text-lg text-[#172019]">Celebration.</strong><p>Invitaciones digitales para momentos inolvidables.</p><p>© {{ new Date().getFullYear() }}</p></div></footer>
    </div>
</template>

<style scoped>
.landing { font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
html { scroll-behavior: smooth; }
</style>
