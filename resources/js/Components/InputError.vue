<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <span v-if="message">
        {{ message }}
    </span>
</template>
