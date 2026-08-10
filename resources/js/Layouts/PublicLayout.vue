<script setup>
import Header from '@/Components/Public/Header.vue';
import Footer from '@/Components/Public/Footer.vue';
defineProps({
    title: {
        type: String,
        default: null
    }
});

</script>

<template>

    <Head :title="title" />

    <Header />
    <main>
        <slot />
    </main>
    <Footer />
</template>

<style scoped>
@import url('@/css/public/layout.css');
</style>
