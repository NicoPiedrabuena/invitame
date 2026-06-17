<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full border-l-4 border-[#40540f] bg-[#e9ddcc] py-2 pe-4 ps-3 text-start text-base font-semibold text-[#40540f] transition duration-150 ease-in-out focus:outline-none'
        : 'block w-full border-l-4 border-transparent py-2 pe-4 ps-3 text-start text-base font-semibold text-[#7c7168] transition duration-150 ease-in-out hover:border-[#d8c8b6] hover:bg-[#fffaf5] hover:text-[#15120f] focus:outline-none',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
