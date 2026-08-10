<script setup>

defineProps({
    type: {
        type: String,
        required: true,
    },
    text: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div :class="`alert alert-${type} text-center`" role="alert">
        {{ text }}
        <slot />
    </div>
</template>
