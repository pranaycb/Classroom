<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    data: Object,
});

const theme = computed(() => {
    const colors = usePage().props.theme;
    return `background: ${colors[props.data.theme]}`;
});
</script>

<template>
    <Link class="col" :href="route('dashboard.classroom.streams.index', data.code)">
    <div class="card h-100 class-card">
        <div class="class-card-header" :style="theme">
            <h2 class="h5 fw-semibold text-truncate">
                {{ data.title }}
            </h2>
            <p class="fw-medium">{{ data.section }}</p>
            <small class="mb-0">
                By {{ data.teacher }}
            </small>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="text-muted small mb-0 fw-medium">Latest Assigned</p>
                <Link :href="route('dashboard.classroom.assignments.index', data.code)"
                    class="text-primary small fw-semibold">View
                all</Link>
            </div>

            <div v-if="data.assigned" class="text-dark">
                <Link class="d-flex py-2" :href="route('dashboard.classroom.assignments.show', [data.code, data.assigned.id])">
                    <div class="me-3 pt-1">
                        <span class="badge avatar-sm text-white d-flex align-items-center justify-content-center"
                            :style="theme">
                            <i class="fas fa-file-alt"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0 small fw-medium">{{ data.assigned.title }}</p>
                        <small class="text-muted small mb-0">Due : {{ data.assigned.date }}</small>
                    </div>
                </Link>
            </div>
            <div v-else class="small text-muted">
                <i class="fas fa-check me-1"></i> No assignment found
            </div>
        </div>
    </div>
    </Link>
</template>

<style scoped>
@import url("@/css/dashboard/class-card.css");
</style>
