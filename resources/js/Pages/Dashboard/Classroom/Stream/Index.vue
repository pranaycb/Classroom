<script setup>
import { computed } from 'vue';
import { useToast } from '@/Compose/useToast';
import Spinner from '@/Components/Spinner.vue';
import { Deferred, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import usePermissions from '@/Compose/usePermissions';
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';
import { useConfirmModal } from '@/Compose/useConfirmModal';
import PostModal from '@/Components/Dashboard/PostModal.vue';
import CommentsModal from '@/Components/Dashboard/CommentsModal.vue';

const { showToast } = useToast();
const { hasPermissions } = usePermissions();

const props = defineProps({
    room: Object,
    announcements: Object
});

const announcements = computed(() => {
    return props.announcements.data.length > 0 ? props.announcements : null;
});

// delete an announcement
const deleteAnnouncement = async (announcement) => {

    const { confirm } = useConfirmModal();

    const result = await confirm('Are you sure you wanted to delete this announcement?');

    if (result) {
        router.delete(route('dashboard.classroom.streams.destroy', [props.room.code, announcement]), {
            replace: true,
            preserveState: true,
            preserveScroll: true,
            onSuccess: (e) => showToast(e.props.success, 'success')
        });
    }
}
</script>

<template>
    <ClassroomLayout active="stream">

        <div class="container">

            <!-- Announcement Form -->
            <PostModal v-if="hasPermissions('announcement.can_announce')" />

            <Deferred data="announcements">

                <template #fallback>
                    <Spinner />
                </template>

                <!-- Announcements -->
                <template v-if="announcements">
                    <div class="card shadow-none mb-4" v-for="announcement in announcements.data">
                        <div class="card-body">
                            <div class="d-flex">

                                <img class="avatar me-3" :src="announcement.avatar" alt="Professor avatar">

                                <div class="flex-grow-1">

                                    <div class="d-flex justify-content-between align-items-center">

                                        <h3 class="h6 mb-0 fw-semibold">{{ announcement.by }}</h3>

                                        <div v-if="announcement.action.update || announcement.action.delete" class="dropdown">
                                            <button type="button" class="btn btn-sm p-0 border-0 fs-6" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-lg"
                                                style="border-radius: 8px;">
                                                <li v-if="announcement.action.update">
                                                    <Link :href="route('dashboard.classroom.streams.edit', [room.code, announcement.id])" class="dropdown-item" href="#">
                                                        <i class="fas fa-edit me-2 text-primary"></i>
                                                        Edit
                                                    </Link>
                                                </li>
                                                <li v-if="announcement.action.delete">
                                                    <button @click="deleteAnnouncement(announcement.id)"
                                                        class="dropdown-item text-danger">
                                                        <i class="fas fa-trash-alt me-2"></i>
                                                        Delete
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <p class="text-muted small mb-2">{{ announcement.date }}</p>
                                    <div class="mt-2 position-relative" v-html="announcement.content"></div>

                                    <!-- Attachments -->
                                    <div v-if="announcement.attachments.count > 0" class="mt-4 pt-3">

                                        <h6>Attachments ({{ announcement.attachments.count }})</h6>

                                        <div class="attachment-list">
                                            <div v-for="attachment in announcement.attachments.data"
                                                class="attachment-item">
                                                <div class="attachment-info">
                                                    <i class="fas file-icon" :class="attachment.icon"></i>
                                                    <div class="file-details">
                                                        <span class="file-name">{{ attachment.name }}</span>
                                                        <span class="file-size">{{ attachment.size }}</span>
                                                    </div>
                                                </div>
                                                <a :href="attachment.url" class="action-btn fs-6">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <CommentsModal :comments="announcement.comments" :target="announcement.id" slug="stream" :only="['announcements']" />
                        </div>
                    </div>

                    <Pagination :meta="announcements.meta" attr="announcements" class="flex-column-reverse" />
                </template>

                <div v-else class="text-center text-muted mt-5">
                    <span>There is no announcement in this classroom</span>
                </div>
            </Deferred>
        </div>
    </ClassroomLayout>
</template>

<style scoped>
.attachment-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
}

.attachment-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
    background-color: #fff;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    transition: background-color 0.2s ease;
    width: 100%;
}

.attachment-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
    min-width: 0;
}

.file-icon {
    font-size: 1.5rem;
    flex-shrink: 0;
    color: var(--primary-dark);
}

.file-details {
    display: flex;
    flex-direction: column;
    min-width: 0;
    flex: 1;
}

.file-name {
    font-size: 0.9rem;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.file-size {
    font-size: 0.75rem;
    color: #6b7280;
}

.action-btn {
    border-radius: 8px;
    height: 30px;
    width: 30px;
    display: grid;
    place-items: center;
    cursor: pointer;
    background-color: #f0f2f5;
    color: #6b7280;
    transition: background-color 0.2s;
}

.action-btn:hover {
    background-color: #d9e0ebe0;
}
</style>
