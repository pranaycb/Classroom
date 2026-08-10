<script setup>
import { usePage } from '@inertiajs/vue3';

const hero = usePage().props.hero;
</script>

<template>
    <section class="hero-section">
        <div class="row gy-5">
            <div class="col-lg-7">
                <h1>{{ hero.title }}</h1>
                <p>{{ hero.subtitle }}</p>
                <Link :href="route('login')" class="btn-contact">Sign in to Classroom</Link>

                <div class="hero-stats">
                    <div>
                        <h3>{{ hero.counter.classrooms }}</h3>
                        <p>Classrooms Created</p>
                    </div>
                    <div>
                        <h3>{{ hero.counter.students }}</h3>
                        <p>Students Learning</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <img src="/storage/icons/hero.png" alt="Person using phone" class="hero-image" />
            </div>
        </div>
    </section>
</template>

<style scoped>
@import url('@/css/public/hero.css');
</style>
