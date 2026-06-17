<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-2 text-start text-sm font-semibold leading-5 text-[#15120f] transition duration-150 ease-in-out hover:bg-[#faead7] focus:bg-[#faead7] focus:outline-none"
    >
        <slot />
    </Link>
</template>
