<script setup>
defineProps({
    block: {
        type: Boolean,
        default: false,
    },
    text: {
        type: String,
    },
    showLoader: {
        type: Boolean,
        default: false
    }
});
</script>

<template>
    <button :class="`btn btn-danger px-4 ${block && 'w-100'} d-flex align-items-center justify-content-center gap-2`"
        type="submit">
        <i v-if="showLoader" class="fas fa-spinner fa-spin me-1"></i> {{ text }}
        <slot />
    </button>
</template>
