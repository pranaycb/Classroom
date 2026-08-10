<script setup>
import { onMounted, ref } from 'vue';
import { useToast } from '@/Compose/useToast';
import { router, usePage } from '@inertiajs/vue3';
import { useConfirmModal } from '@/Compose/useConfirmModal';
import usePermissions from '@/Compose/usePermissions';

const { showToast } = useToast();
const { hasPermissions } = usePermissions();

defineProps({
    data: Object,
});

const room = usePage().props.room;

const details = ref();
const modal = ref();
let modalInstance = null;

onMounted(() => modalInstance = new bootstrap.Modal(modal.value));

const showModal = (data) => {
    details.value = data;
    modalInstance.show();
}

// remove a participant
const removeParticipant = async (user) => {

    const { confirm } = useConfirmModal();

    const confirmed = await confirm('Are you sure you wanted to remove this request?');

    if (confirmed) {
        router.delete(route('dashboard.classroom.people.destroy', [room.code, user]), {
            replace: true,
            preserveState: true,
            preserveScroll: true,
            onSuccess: (e) => showToast(e.props.success, 'success')
        });
    }
}
</script>
<template>
    <div class="list-group-item rounded-0 p-3" :class="data.blocked && 'bg-danger-light'">
        <div class="d-flex align-items-center gap-3">
            <img class="avatar" :src="data.profile">
            <div class="flex-grow-1">
                <h4 class="h6 mb-1 fw-semibold">
                    {{ data.name }}
                    <i v-show="data.moderator" class="fas fa-check-circle text-primary small" data-bs-toggle="tooltip"
                        data-bs-title="Moderator"></i>
                </h4>
                <p class="text-muted small mb-0">Requested on {{ data.created }}</p>
            </div>

            <!-- Participant Management -->
            <div class="dropdown">
                <span role="button" class="btn btn-sm p-0 border-0 me-2 fs-6" data-bs-toggle="dropdown"
                    data-bs-auto-close="outside">
                    <i class="fas fa-ellipsis-vertical"></i>
                </span>
                <ul class="dropdown-menu">
                    <li>
                        <a role="button" class="dropdown-item" @click="showModal(data)">
                            <i class="fas fa-address-card me-2"></i> Profile Details
                        </a>
                    </li>
                    <li v-if="hasPermissions('people.approve_request')">
                        <Link :href="route('dashboard.classroom.people.action', room.code)"
                            class="dropdown-item" method="post" as="button"
                            :data="{ user: data.id, status: 'approved' }" preserve-scroll>
                        <i class="fas fa-user-plus me-2"></i> Accept Request
                        </Link>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li v-if="hasPermissions('people.remove')">
                        <button @click="removeParticipant(data.id)" class="dropdown-item text-danger" method="post"
                            as="button">
                            <i class="fas fa-trash-can me-2"></i> Remove Request
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
                    <h1 class="modal-title fs-5">Profile Details</h1>
                    <button type="button" class="btn-close fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row gy-4">

                        <div class="col-12">

                            <figure class="figure text-center">
                                <img :src="details?.profile" class="figure-img img-fluid avatar-lg" alt="...">
                                <figcaption class="figure-caption">
                                    Profile Picture
                                </figcaption>
                            </figure>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-2">
                                <small class="text-muted">Name</small>
                                <span class="fw-medium">{{ details?.name }}</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-2">
                                <small class="text-muted">Email</small>
                                <span class="fw-medium">{{ details?.email }}</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-2">
                                <small class="text-muted">Phone</small>
                                <span class="fw-medium">{{ details?.phone ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-2">
                                <small class="text-muted">University</small>
                                <span class="fw-medium">{{ details?.university ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-2">
                                <small class="text-muted">Department</small>
                                <span class="fw-medium">{{ details?.department ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-2">
                                <small class="text-muted">Metric Id</small>
                                <span class="fw-medium">{{ details?.metric_id ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-2">
                                <small class="text-muted">Designation</small>
                                <span class="fw-medium">{{ details?.designation ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-2">
                                <small class="text-muted">Joined</small>
                                <span class="fw-medium">{{ details?.created }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
