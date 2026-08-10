<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from '@/Compose/useToast';
import Pagination from '@/Components/Pagination.vue';
import { useConfirmModal } from '@/Compose/useConfirmModal';
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';
import usePermissions from '@/Compose/usePermissions';

const { showToast } = useToast();
const { hasPermissions } = usePermissions();

const props = defineProps({
    room: Object,
    exams: Object,
});

const exams = computed(() => {
    return props.exams.data.length > 0 ? props.exams : null;
});

// delete a exam
const deleteExam = async (exam) => {

    const { confirm } = useConfirmModal();

    const confirmed = await confirm('Are you sure you wanted to delete this exam?');

    if (confirmed) {
        router.delete(route('dashboard.classroom.exams.destroy', [props.room.code, exam]), {
            replace: true,
            preserveState: true,
            preserveScroll: true,
            onSuccess: (e) => showToast(e.props.success, 'success')
        });
    }
}
</script>

<template>
    <ClassroomLayout active="exam">
        <div class="container">

            <div class="p-3" style="border: 1px dashed var(--border-color);">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead class="bg-light">
                            <tr>
                                <th>Name</th>
                                <th>Duration</th>
                                <th>Start</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="exams" v-for="exam in exams.data">
                                <td>{{ exam.name }}</td>
                                <td>{{ exam.duration }}</td>
                                <td>{{ exam.start }}</td>
                                <td>
                                    <span v-if="exam.status === 'ongoing'" class="badge bg-success">
                                        Ongoing
                                    </span>
                                    <span v-else-if="exam.status === 'upcoming'" class="badge bg-warning">
                                        Upcoming
                                    </span>
                                    <span v-else class="badge bg-danger">
                                        Ended
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2 mb-2">

                                        <Link :href="route('dashboard.classroom.exams.show', [room.code, exam.id])"
                                            class="btn btn-primary">
                                        <i class="fas fa-circle-info me-1"></i>Details
                                        </Link>

                                        <Link v-if="exam.status === 'ended'" :href="route('dashboard.classroom.exams.results.index', [room.code, exam.id])"
                                            class="btn btn-warning">
                                        <i class="fas fa-chart-column me-1"></i>Result
                                        </Link>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2">

                                        <Link v-if="hasPermissions('question.view')"
                                            :href="route('dashboard.classroom.exams.questions.index', [room.code, exam.id])"
                                            class="btn btn-info">
                                        <i class="fa-regular fa-circle-question me-1"></i>Questions
                                        </Link>

                                        <Link v-if="hasPermissions('exam.update')"
                                            :href="route('dashboard.classroom.exams.edit', [room.code, exam.id])"
                                            class="btn btn-dark">
                                        <i class="fa-solid fa-pen-to-square me-1"></i>Update
                                        </Link>

                                        <button v-if="hasPermissions('exam.delete')" @click="deleteExam(exam.id)"
                                            class="btn btn-danger">
                                            <i class="fa-solid fa-trash-can me-1"></i>Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="5">
                                    No exam record found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="exams" class="mt-5">
                    <Pagination :meta="exams.meta" attr="exams" class="flex-column-reverse" />
                </div>
            </div>
        </div>
    </ClassroomLayout>
</template>
