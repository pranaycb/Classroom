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
    classes: Object,
});

const classes = computed(() => {
    return props.classes.data.length > 0 ? props.classes : null;
});

// delete a class
const deleteClass = async (cls) => {

    const { confirm } = useConfirmModal();

    const confirmed = await confirm('Are you sure you wanted to delete this class?');

    if (confirmed) {
        router.delete(route('dashboard.classroom.online-classes.destroy', [props.room.code, cls]), {
            replace: true,
            preserveState: true,
            preserveScroll: true,
            onSuccess: (e) => showToast(e.props.success, 'success')
        });
    }
}
</script>

<template>
    <ClassroomLayout active="class">
        <div class="container">

            <div class="p-3" style="border: 1px dashed var(--border-color);">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead class="bg-light">
                            <tr>
                                <th>Name</th>
                                <th>Duration</th>
                                <th>Scheduled</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="classes" v-for="cls in classes.data">
                                <td>{{ cls.name }}</td>
                                <td>{{ cls.duration }}</td>
                                <td>{{ cls.scheduled }}</td>
                                <td>
                                    <span v-if="cls.status === 'ongoing'" class="badge bg-success">
                                        Ongoing
                                    </span>
                                    <span v-else-if="cls.status === 'upcoming'" class="badge bg-warning">
                                        Upcoming
                                    </span>
                                    <span v-else class="badge bg-danger">
                                        Ended
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">

                                        <Link
                                            :href="route('dashboard.classroom.online-classes.show', [room.code, cls.id])"
                                            class="btn btn-primary">
                                        <i class="fas fa-circle-info me-1"></i>Details
                                        </Link>

                                        <Link v-if="hasPermissions('class.update')"
                                            :href="route('dashboard.classroom.online-classes.edit', [room.code, cls.id])"
                                            class="btn btn-dark">
                                        <i class="fa-solid fa-pen-to-square me-1"></i>Update
                                        </Link>

                                        <button v-if="hasPermissions('class.delete')" @click=" deleteClass(cls.id)"
                                            class="btn btn-danger">
                                            <i class="fa-solid fa-trash-can me-1"></i>Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="5">
                                    No class record found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="classes" class="mt-5">
                    <Pagination :meta="classes.meta" attr="classes" class="flex-column-reverse" />
                </div>
            </div>
        </div>
    </ClassroomLayout>
</template>

<style scoped>
</style>
