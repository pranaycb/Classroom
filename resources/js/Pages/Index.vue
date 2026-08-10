<script setup>
import Cta from '@/Components/Public/Cta.vue';
import Hero from '@/Components/Public/Hero.vue';
import Feature from '@/Components/Public/Feature.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>

<template>
    <PublicLayout title="Home">
        <div class="container">
            <Hero />
            <Feature />
            <Cta />
        </div>
    </PublicLayout>
</template>
