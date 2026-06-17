<template>
    <div class="cm-page p-0 font-sans md:p-2">
        <div class="cm-app-frame mx-auto grid min-h-screen max-w-6xl overflow-hidden md:min-h-[calc(100vh-1rem)] md:grid-cols-[0.92fr_1fr] md:rounded-[22px]">
            <section class="hidden flex-col justify-between bg-[#faead7] px-8 py-9 md:flex">
                <div>
                    <a href="/" class="block font-serif text-2xl leading-tight text-[#15120f]">
                        Celebration<br />
                        Memories
                    </a>
                    <p class="mt-2 text-xs tracking-wide text-[#7c7168]">Momentos que perduran</p>
                </div>

                <div class="space-y-7">
                    <div class="rounded-2xl border border-[#ead8c2] bg-[#fffaf5] p-5 shadow-sm">
                        <p class="cm-eyebrow">Estudio digital</p>
                        <h2 class="cm-title mt-4 text-4xl">Invitaciones con alma de recuerdo.</h2>
                        <p class="cm-body mt-4">
                            Diseña, guarda y comparte celebraciones con una experiencia cálida y cuidada de principio a fin.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-xl bg-white/60 p-4">
                            <p class="font-serif text-2xl text-[#40540f]">01</p>
                            <p class="mt-2 text-xs text-[#7c7168]">Elige el estilo</p>
                        </div>
                        <div class="rounded-xl bg-white/60 p-4">
                            <p class="font-serif text-2xl text-[#40540f]">02</p>
                            <p class="mt-2 text-xs text-[#7c7168]">Personaliza detalles</p>
                        </div>
                    </div>
                </div>

                <p class="text-xs italic text-[#7c7168]">Papel, textura y memoria en una misma pantalla.</p>
            </section>

            <main class="flex min-h-screen items-center justify-center px-4 py-8 md:min-h-0 md:px-10">
                <div class="w-full max-w-md">
                    <div class="mb-8 md:hidden">
                        <a href="/" class="font-serif text-2xl leading-tight text-[#15120f]">
                            Celebration Memories
                        </a>
                        <p class="mt-1 text-xs tracking-wide text-[#7c7168]">Momentos que perduran</p>
                    </div>

                    <div class="cm-panel rounded-2xl px-6 py-7 sm:px-8">
                        <slot />
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
