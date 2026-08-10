<script setup>
import { onMounted, ref } from 'vue';
import { useToast } from '@/Compose/useToast';
import TextArea from '@/Components/TextArea.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from "@/Components/InputLabel.vue";
import usePermissions from '@/Compose/usePermissions';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { useConfirmModal } from '@/Compose/useConfirmModal';

const { showToast } = useToast();
const { hasPermissions } = usePermissions();

const props = defineProps({
    data: Object,
});

const room = usePage().props.room;
const assignment = usePage().props.assignment;

const details = ref();
const modal = ref();
let modalInstance = null;

onMounted(() => modalInstance = new bootstrap.Modal(modal.value));

const showModal = (data) => {
    details.value = data;
    modalInstance.show();
}

const form = useForm({
    marks: props.data.marks,
    feedback: props.data.feedback,
});

const evaluate = () => {
    form.put(route('dashboard.classroom.assignments.submissions.update', [room.code, assignment.id, props.data.id]), {
        replace: true,
        preserveState: true,
        preserveScroll: true,
        onSuccess: (e) => {
            showToast(e.props.success, 'success');
            modalInstance.hide();
        }
    });
}

// remove submission
const removeSubmission = async (submission) => {

    const { confirm } = useConfirmModal();

    const confirmed = await confirm('Are you sure you wanted to remove this submission?');

    if (confirmed) {
        router.delete(route('dashboard.classroom.assignments.submissions.destroy', [room.code, assignment.id, submission]), {
            replace: true,
            preserveState: true,
            preserveScroll: true,
            onSuccess: (e) => showToast(e.props.success, 'success')
        });
    }
}
</script>
<template>
    <div class="list-group-item rounded-0 p-3">
        <div class="d-flex align-items-center gap-3">
            <div class="flex-grow-1">
                <h4 class="h6 mb-2 fw-semibold">
                    {{ data.student }}
                </h4>
                <div class="mb-2 d-flex flex-wrap gap-2">
                    <span class="text-body">
                        Marks :
                        <template v-if="data.marks">
                            {{ data.marks }} / {{ assignment.marks }}
                            <span class="ms-1">({{ data.percentage }}%)</span>
                        </template>
                        <span v-else class="small fw-medium text-danger">Not evaluated</span>
                    </span>
                    <span>•</span>
                    <span>
                        Teacher's Feedback :
                        {{ data.feedback ?? 'N/A' }}
                    </span>
                </div>
                <p class="text-muted small mb-0">Submitted {{ data.created }}</p>
            </div>

            <!-- Submission Management -->
            <div class="dropdown">
                <span role="button" class="btn btn-sm p-0 border-0 me-2 fs-6" data-bs-toggle="dropdown"
                    data-bs-auto-close="outside">
                    <i class="fas fa-ellipsis-vertical"></i>
                </span>
                <ul class="dropdown-menu">
                    <li v-if="hasPermissions('assignment.update_marks')">
                        <a role="button" class="dropdown-item" @click="showModal(data)">
                            <i class="fas fa-chart-line me-2"></i> Evaluate
                        </a>
                    </li>
                    <li>
                        <a :href="data.attachment" class="dropdown-item">
                            <i class="fas fa-download me-2"></i> Download
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li v-if="hasPermissions('assignment.delete_submission')">
                        <button @click="removeSubmission(data.id)" class="dropdown-item text-danger" method="post"
                            as="button">
                            <i class="fas fa-trash-can me-2"></i> Remove
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Info Modal -->
    <div ref="modal" class="modal" id="infoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Evaluate Submission</h1>
                    <button type="button" class="btn-close fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="modal-body" @submit.prevent="evaluate">
                    <div class="row gy-4">

                        <div class="col-12">
                            <InputLabel value="Marks" />
                            <TextInput :error="form.errors.marks" v-model="form.marks" placeholder="Enter marks" />
                        </div>

                        <div class="col-12">
                            <InputLabel value="Feedback" />
                            <TextArea :error="form.errors.feedback" v-model="form.feedback"
                                placeholder="Enter some feedback" />
                        </div>

                        <div class="col-12">
                            <PrimaryButton text="Update" :block="true" :showLoader="form.processing"
                                :disabled="form.processing" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
