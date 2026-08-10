<script setup>
import { computed } from 'vue';
import { Deferred } from '@inertiajs/vue3';
import { useToast } from '@/Compose/useToast';
import Spinner from '@/Components/Spinner.vue';
import Pagination from '@/Components/Pagination.vue';
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';

const props = defineProps({
    room: Object,
    exam: Object,
    results: Object,
});

const results = computed(() => {
    return props.results?.data.length > 0 ? props.results : null;
});
</script>

<template>
    <ClassroomLayout active="exam">
        <div class="container">

            <div class="p-3" style="border: 1px dashed var(--border-color);">

                <h5 class="mb-4 pb-1">
                    {{ exam.name }}
                </h5>

                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead class="bg-light">
                            <tr>
                                <th>Student</th>
                                <th>Marks Obtained</th>
                                <th>Pass Mark</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>

                            <Deferred data="results">

                                <template #fallback>
                                    <tr>
                                        <td colspan="5">
                                            <Spinner />
                                        </td>
                                    </tr>
                                </template>

                                <tr v-if="results" v-for="result in results.data">
                                    <td>{{ result.student }}</td>
                                    <td>{{ result.marks }}</td>
                                    <td>{{ exam.pass_mark }}</td>
                                    <td>
                                        <span v-if="result.status === 'passed'" class="badge bg-success">
                                            Passed
                                        </span>
                                        <span v-else class="badge bg-danger">
                                            Failed
                                        </span>
                                    </td>
                                    <td>
                                        <Link
                                            :href="route('dashboard.classroom.exams.results.show', [room.code, exam.id, result.id])"
                                            class="btn btn-primary">
                                        <i class="fas fa-circle-info me-1"></i>Details
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-else>
                                    <td colspan="5">
                                        No result record found
                                    </td>
                                </tr>
                            </Deferred>
                        </tbody>
                    </table>
                </div>

                <div v-if="results" class="mt-5">
                    <Pagination :meta="results.meta" attr="results" class="flex-column-reverse" />
                </div>
            </div>
        </div>
    </ClassroomLayout>
</template>
