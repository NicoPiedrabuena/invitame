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
        class="rounded-xl border-[#ded9e8] bg-[#fbfaff] px-4 py-3 text-sm text-[#211b35] shadow-sm placeholder:text-[#aaa4b2] focus:border-[#7657ff] focus:ring-[#7657ff]"
        v-model="model"
        ref="input"
    />
</template>
