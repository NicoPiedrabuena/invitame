<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-lg border-[#d8c8b6] bg-[#fffaf5] px-4 py-3 text-sm text-[#15120f] shadow-sm placeholder:text-[#a69a8f] focus:border-[#40540f] focus:ring-[#40540f]"
        v-model="model"
        ref="input"
    />
</template>
