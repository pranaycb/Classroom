<script setup>
import { onMounted, ref } from "vue";
import Header from '@/Components/Dashboard/Header.vue';

defineProps({
    title: String,
    showStarted: {
        type: Boolean,
        default: false,
    },
    breadcrumb: {
        type: String,
        default: null,
    }
});

const showSidebar = ref(false);

const toggler = () => showSidebar.value = !showSidebar.value;

/**
 * Initialize bootstrap tooltip
 */
onMounted(() => {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl));
});
</script>

<template>

    <Head :title />

    <div class="content-wrapper">

        <!-- Main Content -->
        <div class="main-content">

            <!-- Header -->
            <Header :title="breadcrumb" :show-started="showStarted" :toggler />

            <!-- Main Content Area -->
            <slot />
        </div>
    </div>
</template>

<style scoped>
@import url("@/css/dashboard/layout.css");
</style>
