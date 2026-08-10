<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label v-if="value" class="form-label d-block text-nowrap">{{ value }}</label>
    <label v-else class="form-label d-block text-nowrap">
        <slot />
    </label>
</template>
