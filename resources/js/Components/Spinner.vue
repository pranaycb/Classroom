<script setup>
defineProps({
    type: {
        type: String,
        default: 'primary',
    },
    align: {
        type: String,
        default: 'center',
    }
})
</script>

<template>
    <div :class="`text-${align}`">
        <div class="spinner-border" :class="`text-${type}`" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</template>
