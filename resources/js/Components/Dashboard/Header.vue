<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AddClassCard from './AddClassCard.vue';

const props = defineProps({
    title: [String, null],
    showStarted: Boolean,
});

const user = computed(() => usePage().props.auth.user);
</script>

<template>
    <header class="main-header">
        <div class="container">
            <div class="d-flex gap-4 justify-content-between align-items-center h-100">
                <div class="d-flex gap-4 align-items-center">

                    <Link v-if="!showStarted" class="header-btn" :href="route('dashboard.index')">
                    <i class="fas fa-arrow-left"></i>
                    </Link>

                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Classroom
                    </h5>
                </div>

                <div class="d-flex align-items-center">

                    <div class="dropdown" v-if="showStarted">

                        <i class="fas fa-plus btn btn-primary py-0 px-2 fs-5 me-4" data-bs-toggle="dropdown"></i>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="dropdown-item bg-white px-4 py-3">
                                <AddClassCard />
                            </li>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <img class="avatar-sm" :src="user.profile" data-bs-toggle="dropdown">
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <div class="dropdown-header d-flex align-items-center">
                                    <img class="avatar me-2" :src="user.profile" alt="User avatar">
                                    <div>
                                        <h6 class="mb-0">
                                            {{ user.name }}
                                        </h6>
                                        <p class="text-muted small mb-0">
                                            {{ user.email }}
                                        </p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <Link class="dropdown-item" :href="route('dashboard.profile.index')">
                                <i class="fas fa-user me-2 text-primary"></i> Profile
                                </Link>
                            </li>
                            <li>
                                <Link class="dropdown-item text-danger" :href="route('logout')" as="button"
                                    method="post">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped>
@import url("@/css/dashboard/header.css");
</style>
