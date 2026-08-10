<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from '@/Compose/useToast';
import Pagination from '@/Components/Pagination.vue';
import { useConfirmModal } from '@/Compose/useConfirmModal';
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';
import usePermissions from '@/Compose/usePermissions';

const { showToast } = useToast();
const { hasPermissions } = usePermissions()

const props = defineProps({
    room: Object,
    assignments: Object,
});

const assignments = computed(() => {
    return props.assignments.data.length > 0 ? props.assignments : null;
});

// delete a assignment
const deleteAssignment = async (assignment) => {

    const { confirm } = useConfirmModal();

    const confirmed = await confirm('Are you sure you wanted to delete this assignment?');

    if (confirmed) {
        router.delete(route('dashboard.classroom.assignments.destroy', [props.room.code, assignment]), {
            replace: true,
            preserveState: true,
            preserveScroll: true,
            onSuccess: (e) => showToast(e.props.success, 'success')
        });
    }
}
</script>

<template>
    <ClassroomLayout active="assignment">
        <div class="container">

            <div class="p-3" style="border: 1px dashed var(--border-color);">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead class="bg-light">
                            <tr>
                                <th>Title</th>
                                <th>Due</th>
                                <th v-if="room.role === 'student'">Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="assignments" v-for="assignment in assignments.data">
                                <td>{{ assignment.title }}</td>
                                <td>{{ assignment.due }}</td>
                                <td v-if="room.role === 'student'">
                                    <span v-if="assignment.status === 'missing'" class="badge bg-danger">
                                        Missing
                                    </span>
                                    <span v-else class="badge bg-success">
                                        Submitted
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-2">

                                        <div class="d-flex gap-2">

                                            <Link
                                                :href="route('dashboard.classroom.assignments.show', [room.code, assignment.id])"
                                                class="btn btn-primary">
                                            <i class="fas fa-circle-info me-1"></i>Details
                                            </Link>

                                            <Link v-if="hasPermissions('assignment.view_submissions')"
                                                :href="route('dashboard.classroom.assignments.submissions.index', [room.code, assignment.id])"
                                                class="btn btn-warning">
                                            <i class="fas fa-chart-bar me-1"></i>Submissions
                                            </Link>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <Link v-if="hasPermissions('assignment.update')"
                                                :href="route('dashboard.classroom.assignments.edit', [room.code, assignment.id])"
                                                class="btn btn-dark">
                                            <i class="fa-solid fa-pen-to-square me-1"></i>Update
                                            </Link>

                                            <button v-if="hasPermissions('assignment.delete')"
                                                @click=" deleteAssignment(assignment.id)" class="btn btn-danger">
                                                <i class="fa-solid fa-trash-can me-1"></i>Delete
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="5">
                                    No assignment record found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="assignments" class="mt-5">
                    <Pagination :meta="assignments.meta" attr="assignments" class="flex-column-reverse" />
                </div>
            </div>
        </div>
    </ClassroomLayout>
</template>

<style scoped>
</style>
