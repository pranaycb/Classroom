<script setup>
import { usePoll } from '@inertiajs/vue3';
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';

const props = defineProps({
    room: Object,
    data: Object,
    link: [String, null]
});

usePoll(2000, { only: ['link'] });
</script>

<template>
    <ClassroomLayout active="class">
        <div class="container">

            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8 col-xl-6">
                    <div class="card card-body">

                        <div class="d-flex flex-column gap-2">
                            <span class="text-muted fw-medium">Class Name</span>
                            <h5 class="card-title">{{ data.name }}</h5>
                        </div>

                        <div class="d-flex flex-column gap-2">
                            <span class="text-muted fw-medium">Class Start</span>
                            <h6 class="card-title fs-6">{{ data.start }}</h6>
                        </div>

                        <div class="d-flex flex-column gap-2">
                            <span class="text-muted fw-medium">Class End</span>
                            <h6 class="card-title fs-6">{{ data.end }}</h6>
                        </div>

                        <div class="d-flex flex-column gap-2">
                            <span class="text-muted fw-medium">Duration</span>
                            <h6 class="card-title fs-6">{{ data.duration }}</h6>
                        </div>

                        <div v-if="link"
                            class="alert alert-info rounded-1 d-inline-flex align-items-center justify-content-between gap-3">
                            <span>
                                Class has been started. Click the join button to join in the
                                class.
                            </span>
                            <Link
                                :href="link"
                                class="btn badge bg-white text-info">Join</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClassroomLayout>
</template>

<style scoped>
</style>
