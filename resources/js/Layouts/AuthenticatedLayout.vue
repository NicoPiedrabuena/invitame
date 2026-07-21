<script setup>
import { computed, ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const page = usePage();
const user = computed(() => page.props.auth.user);

const initials = computed(() => {
    const name = user.value?.name || 'Usuario';

    return name
        .split(' ')
        .map((part) => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase();
});
</script>

<template>
    <div class="cm-page p-0 font-sans md:p-2">
        <div class="cm-app-frame mx-auto min-h-screen max-w-7xl overflow-hidden md:min-h-[calc(100vh-1rem)] md:rounded-[22px]">
            <nav class="border-b border-[#ead8c2] bg-[#faead7]">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-20 items-center justify-between">
                        <div class="flex items-center gap-8">
                            <Link :href="route('dashboard')" class="font-serif text-xl leading-tight text-[#15120f]">
                                Celebration<br class="hidden sm:block" />
                                Memories
                            </Link>

                            <div class="hidden items-center gap-2 md:flex">
                                <Link
                                    :href="route('dashboard')"
                                    class="rounded-lg px-4 py-2 text-sm font-semibold transition hover:bg-white/45"
                                    :class="{ 'bg-[#eee9ff] text-[#6d4aff]': route().current('dashboard') }"
                                >
                                    Panel
                                </Link>
                                <Link
                                    :href="route('invitations.create')"
                                    class="rounded-lg px-4 py-2 text-sm font-semibold transition hover:bg-white/45"
                                    :class="{ 'bg-[#eee9ff] text-[#6d4aff]': route().current('invitations.create') }"
                                >
                                    Crear invitación
                                </Link>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-3 rounded-full border border-[#ead8c2] bg-white/60 px-3 py-2 text-sm font-semibold text-[#15120f] transition hover:bg-white focus:outline-none focus:ring-2 focus:ring-[#6d4aff] focus:ring-offset-2 focus:ring-offset-[#faead7]"
                                    >
                                        <span class="grid h-8 w-8 place-items-center rounded-full bg-[#15120f] text-xs font-bold text-white">
                                            {{ initials }}
                                        </span>
                                        <span>{{ user.name }}</span>
                                        <span class="text-[#7c7168]">⌄</span>
                                    </button>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">
                                        Perfil
                                    </DropdownLink>
                                    <DropdownLink
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                    >
                                        Cerrar sesión
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>

                        <div class="flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-[#15120f] transition hover:bg-white/45 focus:outline-none focus:ring-2 focus:ring-[#6d4aff]"
                                aria-label="Abrir navegación"
                            >
                                <span class="text-2xl leading-none">{{ showingNavigationDropdown ? '×' : '☰' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="border-t border-[#ead8c2] bg-[#fff7ef] sm:hidden"
                >
                    <div class="space-y-1 px-4 py-3">
                        <Link :href="route('dashboard')" class="block rounded-lg px-3 py-2 text-sm font-semibold">
                            Panel
                        </Link>
                        <Link :href="route('invitations.create')" class="block rounded-lg px-3 py-2 text-sm font-semibold">
                            Crear invitación
                        </Link>
                    </div>

                    <div class="border-t border-[#ead8c2] px-4 py-4">
                        <div class="text-sm font-bold text-[#15120f]">{{ user.name }}</div>
                        <div class="text-xs text-[#7c7168]">{{ user.email }}</div>

                        <div class="mt-3 space-y-1">
                            <Link :href="route('profile.edit')" class="block rounded-lg px-3 py-2 text-sm font-semibold">
                                Perfil
                            </Link>
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="block rounded-lg px-3 py-2 text-left text-sm font-semibold"
                            >
                                Cerrar sesión
                            </Link>
                        </div>
                    </div>
                </div>
            </nav>

            <header v-if="$slots.header" class="border-b border-[#ead8c2] bg-[#fffaf5]">
                <div class="mx-auto max-w-7xl px-4 py-7 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
