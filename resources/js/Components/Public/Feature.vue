<script setup>
import { usePage } from '@inertiajs/vue3';

const features = usePage().props.features;
</script>

<template>
    <section id="features" class="features" v-if="features">
        <div class="row gy-4">
            <div class="col-lg-4" v-for="feature in features">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i :class="feature.icon"></i>
                    </div>
                    <h4>{{ feature.title }}</h4>
                    <p>{{ feature.subtitle }}</p>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
@import url('@/css/public/feature.css');
</style>
