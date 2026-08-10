<script setup>
import { usePage } from '@inertiajs/vue3';

const settings = usePage().props.settings;
</script>

<template>
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center border-top pt-5">
                <div class="col-lg-6">
                    <p class="mb-3 mb-lg-0">Copyright &copy; {{ new Date().getFullYear() }} by Classroom. All
                        rights reserved</p>
                </div>
                <div class="col-lg-6 text-lg-end">
                    <a href="#contact">
                        <i class="ri-arrow-up-line"></i> Scroll To Top
                    </a>
                </div>
            </div>
        </div>
    </footer>
</template>

<style scoped>
@import url('@/css/public/footer.css');
</style>
