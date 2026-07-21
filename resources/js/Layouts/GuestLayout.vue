<template>
    <div class="relative min-h-screen overflow-hidden bg-[#f8f7fc] font-sans text-[#211b35]">
        <div class="pointer-events-none absolute -left-40 -top-40 h-[34rem] w-[34rem] rounded-full bg-[#ded6ff]/75 blur-3xl" />
        <div class="pointer-events-none absolute -bottom-48 right-[-8rem] h-[38rem] w-[38rem] rounded-full bg-[#ffe0ed]/75 blur-3xl" />

        <header class="relative z-20 mx-auto flex max-w-7xl items-center justify-between px-5 py-5 md:px-8">
            <a href="/" class="flex items-center gap-3 font-black tracking-[-0.04em]">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-[#7657ff] text-white shadow-[0_8px_22px_rgba(118,87,255,.28)]">C</span>
                <span class="text-xl">Celebration.</span>
            </a>
            <a href="/" class="rounded-full border border-black/[.07] bg-white/75 px-4 py-2 text-sm font-bold text-[#5f596c] shadow-sm backdrop-blur transition hover:border-[#b7a8ff] hover:text-[#6d4aff]">
                Volver al inicio
            </a>
        </header>

        <main class="relative z-10 mx-auto grid min-h-[calc(100vh-80px)] max-w-7xl items-center gap-12 px-5 pb-10 md:px-8 lg:grid-cols-[1fr_520px] lg:gap-20">
            <section class="hidden max-w-xl lg:block">
                <div class="inline-flex items-center gap-2 rounded-full border border-[#d7ceff] bg-white/70 px-4 py-2 text-xs font-extrabold uppercase tracking-[.16em] text-[#6d4aff] backdrop-blur">
                    <span>✦</span> Tu celebración empieza acá
                </div>
                <h2 class="mt-7 text-6xl font-black leading-[.94] tracking-[-.06em]">
                    Una invitación<br />tan única como<br /><span class="bg-gradient-to-r from-[#7657ff] to-[#e45b98] bg-clip-text text-transparent">tu historia.</span>
                </h2>
                <p class="mt-6 max-w-lg text-lg leading-8 text-[#6d6777]">
                    Diseñá, compartí y administrá cada detalle del evento desde una experiencia simple y hermosa.
                </p>

                <div class="mt-10 grid grid-cols-3 gap-3">
                    <div class="rounded-2xl border border-white/80 bg-white/65 p-4 shadow-[0_12px_40px_rgba(52,35,105,.06)] backdrop-blur">
                        <span class="grid h-9 w-9 place-items-center rounded-xl bg-[#eee9ff] text-[#7657ff]">✦</span>
                        <p class="mt-4 text-sm font-extrabold">Diseños únicos</p>
                    </div>
                    <div class="rounded-2xl border border-white/80 bg-white/65 p-4 shadow-[0_12px_40px_rgba(52,35,105,.06)] backdrop-blur">
                        <span class="grid h-9 w-9 place-items-center rounded-xl bg-[#ffe8f1] text-[#d84f8d]">✓</span>
                        <p class="mt-4 text-sm font-extrabold">RSVP online</p>
                    </div>
                    <div class="rounded-2xl border border-white/80 bg-white/65 p-4 shadow-[0_12px_40px_rgba(52,35,105,.06)] backdrop-blur">
                        <span class="grid h-9 w-9 place-items-center rounded-xl bg-[#eee9ff] text-[#7657ff]">∞</span>
                        <p class="mt-4 text-sm font-extrabold">Todo en uno</p>
                    </div>
                </div>
            </section>

            <section class="flex items-center justify-center py-8 lg:py-12">
                <div class="w-full max-w-[520px] rounded-[30px] border border-white/90 bg-white/85 p-6 shadow-[0_25px_80px_rgba(49,32,101,.12)] backdrop-blur-xl sm:p-9">
                    <div class="mb-7 flex items-center gap-3 lg:hidden">
                        <span class="h-1.5 w-10 rounded-full bg-[#7657ff]" />
                        <p class="text-xs font-black uppercase tracking-[.16em] text-[#6d4aff]">Celebration</p>
                    </div>
                    <slot />
                </div>
            </section>
        </main>
    </div>
</template>
