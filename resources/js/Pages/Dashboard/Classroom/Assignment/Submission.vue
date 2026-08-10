<script setup>
import { computed } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';
import Submission from '@/Components/Dashboard/Submission.vue';

const props = defineProps({
    room: Object,
    assignment: Object,
    submissions: Object,
});

const submissions = computed(() => {
    return props.submissions.data.length > 0 ? props.submissions : null;
});
</script>

<template>
    <ClassroomLayout active="assignment">
        <div class="container">

            <div v-if="submissions" class="mb-4">

                <h3 class="h5 mb-4 fw-semibold">
                    {{ assignment.title }}
                </h3>

                <div class="list-group shadow-none">
                    <template v-for="submission in submissions.data">
                        <Submission :data="submission" />
                    </template>
                </div>

                <div class="mt-5">
                    <Pagination :meta="submissions.meta" attr="submissions" class="flex-column-reverse" />
                </div>
            </div>

            <span v-else class="d-block text-center text-muted">No submission found in this assignment.</span>
        </div>
    </ClassroomLayout>
</template>

<style scoped>
</style>
