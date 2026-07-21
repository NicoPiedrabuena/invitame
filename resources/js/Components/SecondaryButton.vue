<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center rounded-lg border border-[#d8c8b6] bg-[#fffaf5] px-5 py-3 text-xs font-semibold uppercase tracking-[0.18em] text-[#15120f] shadow-sm transition duration-150 ease-in-out hover:bg-white focus:outline-none focus:ring-2 focus:ring-[#6d4aff] focus:ring-offset-2 focus:ring-offset-[#fff7ef] disabled:opacity-25"
    >
        <slot />
    </button>
</template>
