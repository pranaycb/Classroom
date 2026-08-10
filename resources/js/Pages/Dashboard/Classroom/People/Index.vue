<script setup>
import { computed } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';
import Participant from '@/Components/Dashboard/Participant.vue';

const props = defineProps({
    room: Object,
    teacher: Object,
    participants: Object
});

const participants = computed(() => {
    return props.participants.data.length > 0 ? props.participants : null;
});
</script>

<template>
    <ClassroomLayout active="people">
        <div class="container">
            <div class="mb-5">

                <h3 class="h6 mb-3 fw-semibold">Teacher</h3>

                <div class="list-group shadow-none">
                    <div class="list-group-item rounded-0 p-3">
                        <div class="d-flex gap-3 align-items-center">
                            <img class="avatar" :src="teacher.profile" />
                            <div class="flex-grow-1">
                                <h4 class="h6 mb-1 fw-semibold">{{ teacher.name }}</h4>
                                <p class="text-muted small mb-0">{{ teacher.email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="participants" class="mb-4">

                <h3 class="h6 mb-3 fw-semibold">Participants ({{ participants.meta.total }})</h3>

                <div class="list-group shadow-none">
                    <template v-for="participant in participants.data">
                        <Participant :data="participant" />
                    </template>
                </div>

                <div class="mt-5">
                    <Pagination :meta="participants.meta" attr="participants" class="flex-column-reverse" />
                </div>
            </div>

            <span v-else class="d-block text-center text-muted">No participant found in this classroom.</span>
        </div>
    </ClassroomLayout>
</template>
