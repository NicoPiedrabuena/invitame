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
        ? 'inline-flex items-center border-b-2 border-[#6d4aff] px-1 pt-1 text-sm font-semibold leading-5 text-[#15120f] transition duration-150 ease-in-out focus:outline-none focus:border-[#6d4aff]'
        : 'inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-semibold leading-5 text-[#7c7168] transition duration-150 ease-in-out hover:border-[#d8c8b6] hover:text-[#15120f] focus:border-[#d8c8b6] focus:text-[#15120f] focus:outline-none',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
