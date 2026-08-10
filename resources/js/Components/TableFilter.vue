<script setup>
defineProps({
    submit: {
        type: Function,
        required: true,
    },
    reset: {
        type: Function,
        required: true,
    },
});
</script>

<template>
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle border" type="button" data-bs-toggle="dropdown"
            data-bs-auto-close="outside" aria-expanded="false">
            <i class="ri-filter-line"></i> Filter
        </button>
        <form class="dropdown-menu" @submit.prevent="submit" @reset="reset">

            <h6 class="dropdown-header mb-0 pb-1">Filter Records</h6>

            <div class="dropdown-divider"></div>

            <div class="dropdown-item">
                <slot />
            </div>

            <div class="dropdown-divider"></div>

            <div class="dropdown-item d-flex gap-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <button type="reset" class="btn btn-dark">Reset</button>
            </div>
        </form>
    </div>
</template>

<style scoped>
@import url("@/css/filter.css");
</style>
