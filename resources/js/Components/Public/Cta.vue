<script setup>
import { usePage } from '@inertiajs/vue3';

const cta = usePage().props.cta;
</script>

<template>
    <section id="get-started" class="cta-section">
        <div class="row align-items-center gy-4">
            <div class="col-lg-8">
                <h2>{{ cta.title }}</h2>
                <p>{{ cta.subtitle }}</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <Link :href="route('register')" class="btn-demo">
                    <i class="ri-telegram-2-line"></i> Register Now
                </Link>
            </div>
        </div>
    </section>
</template>

<style scoped>
@import url('@/css/public/cta.css');
</style>
