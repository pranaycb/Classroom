<script setup>
import { computed } from 'vue';
import { Deferred } from '@inertiajs/vue3'
import Spinner from '@/Components/Spinner.vue';
import Pagination from '@/Components/Pagination.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ClassCard from '@/Components/Dashboard/ClassCard.vue';

const props = defineProps({
    active: String,
    classes: Object,
});

const classes = computed(() => {
    return props.classes.data.length > 0 ? props.classes : null;
});
</script>

<template>
    <DashboardLayout title="Dashboard" :show-started="true">
        <div class="container py-4">

            <ul class="nav nav-underline mb-5 justify-content-center justify-content-md-start">
                <li class="nav-item">
                    <Link :href="route('dashboard.index')" class="nav-link" :class="{ 'active': active === 'all' }">
                    All</Link>
                </li>
                <li class="nav-item">
                    <Link :href="route('dashboard.index', 'created')" class="nav-link" :class="{'active' : active === 'created'}">
                    Created</Link>
                </li>
                <li class="nav-item">
                    <Link :href="route('dashboard.index', 'joined')" class="nav-link"
                        :class="{ 'active': active === 'joined' }">
                    Joined</Link>
                </li>
                <li class="nav-item">
                    <Link :href="route('dashboard.getstart.index')" class="nav-link">Join or Create New</Link>
                </li>
            </ul>

            <Deferred data="classes">

                <template #fallback>
                    <Spinner />
                </template>

                <template v-if="classes">

                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4">
                        <ClassCard v-for="data in classes.data" :data />
                    </div>

                    <div class="mt-5">
                        <Pagination :meta="classes.meta" attr="classes" class="flex-column-reverse" />
                    </div>
                </template>

                <span v-else class="d-block text-center text-muted">No classes found. Create or Join a class to get
                    started</span>

            </Deferred>
        </div>
    </DashboardLayout>
</template>

<style scoped>
.nav-item .nav-link.active,
.nav-item .nav-link:hover{
    color: var(--primary);
}
</style>
