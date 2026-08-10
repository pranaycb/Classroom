<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from '@/Compose/useToast';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';
import CommentsModal from '@/Components/Dashboard/CommentsModal.vue';

const { showToast } = useToast();

const props = defineProps({
    room: Object,
    data: Object,
    submission: [Object, null],
    comments: Object,
});

// assignment create form
const form = useForm({
    attachment: '',
});

const fileInput = ref(null);
const selectedFile = ref(null);

/**
 * Show and process the selected files
 */
const handleFilesSelect = (event) => {

    const file = event.target.files[0];

    const size = file.size;
    const fileSize = size < 1048576 ?
        (size / 1024).toFixed(2) + ' KB' :
        (size / 1048576).toFixed(2) + ' MB';

    // Determine file type
    let icon = 'fa-file';

    if (file.type.startsWith('image/')) {
        icon = 'fa-file-image';
    }
    else if (file.type.startsWith('video/')) {
        icon = 'fa-file-video';
    }
    else if (file.name.endsWith('.pdf')) {
        icon = 'fa-file-pdf';
    }
    else if (file.name.endsWith('.json')) {
        icon = 'fa-file-lines';
    }
    else if (file.name.endsWith('.doc') || file.name.endsWith('.docx')) {
        icon = 'fa-file-word';
    }
    else if (file.name.endsWith('.xls') || file.name.endsWith('.xlsx')) {
        icon = 'fa-file-excel';
    }
    else if (file.name.endsWith('.ppt') || file.name.endsWith('.pptx')) {
        icon = 'fa-file-powerpoint';
    }
    else if (file.name.endsWith('.zip')) {
        icon = 'fa-file-zipper';
    }

    form.attachment = file;

    selectedFile.value = {
        file: file,
        name: file.name,
        icon,
        size: fileSize,
    };
};

/**
 * Remove file
 */
const removeFile = () => {
    form.attachment = '';
    form.clearErrors(`attachment.file`);
};

const submit = () => {
    form.post(route('dashboard.classroom.assignments.submissions.store', [props.room.code, props.data.id]), {
        preserveScroll: true,
        onSuccess: (e) => showToast(e.props.success, 'success'),
    })
};
</script>

<template>
    <ClassroomLayout active="assignment">
        <div class="container">
            <div class="row gy-4 justify-content-center">
                <div class="col-xl-8 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                {{ data.title }}
                                <div class="px-2 badge small bg-warning">
                                    {{ data.marks }} Marks
                                </div>
                            </h5>

                            <div class="text-muted small mb-4">
                                <span class="fw-medium">{{ room.teacher }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ data.created }}</span>
                                <p class="text-danger fw-bold mt-2">Due {{ data.due }}</p>
                            </div>

                            <div v-html="data.description"></div>

                            <!-- Attachments -->
                            <div v-if="data.attachments.data.length > 0" class="mt-4 pt-3">

                                <h6>Attachments ({{ data.attachments.data.length }})</h6>

                                <div class="attachment-list">
                                    <div v-for="attachment in data.attachments.data" class="attachment-item">
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
                        <div class="card-footer">
                            <CommentsModal :comments="comments" :target="data.id" slug="assignment"
                                :only="['comments']" />
                        </div>
                    </div>
                </div>

                <div v-if="room.role === 'student' && submission" class="col-xl-4 col-lg-4">
                    <div class="card">
                        <div class="card-body">

                            <h5 class="card-title">Your Work</h5>

                            <div class="d-block mb-2 text-body fw-medium">
                                Status :
                                <span v-if="submission.status === 'missing'" class="badge bg-danger-light text-danger">
                                    Missing
                                </span>
                                <span v-else class="badge bg-success-light text-success">
                                    Submitted
                                </span>
                            </div>

                            <span class="d-block mb-2 text-body fw-medium">
                                Due {{ data.due }}
                            </span>

                            <form v-if="submission.canSubmit" class="mt-4" @submit.prevent="submit">

                                <!-- Files Preview Container -->
                                <div v-if="selectedFile" class="mb-4">
                                    <div class="attachment-list">

                                        <div class="attachment-item" :class="form.errors.attachment && 'error'">
                                            <div class="attachment-info">

                                                <i class="fas file-icon" :class="selectedFile.icon"></i>

                                                <div class="file-details">
                                                    <span class="file-name">{{ selectedFile.name }}</span>
                                                    <span class="file-size">{{ selectedFile.size }}</span>

                                                    <span class="text-danger d-block mt-1" style="font-size: 0.8rem;">
                                                        {{ form.errors.attachment }}
                                                    </span>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-close fs-4" @click="removeFile"></button>
                                        </div>
                                    </div>
                                </div>

                                <!-- File Upload -->
                                <div v-else class="mb-4" @click="fileInput.click()">
                                    <div class="file-upload-area">
                                        <div class="file-upload-icon">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <div class="">
                                            <h4 class="h6 mb-1 fw-semibold">Click here to upload</h4>
                                            <p class="text-muted small pb-0 mb-0">PDF, Word, Excel, or image files</p>
                                        </div>
                                        <!-- Hidden file input -->
                                        <input type="file" ref="fileInput" style="display: none;"
                                            @change="handleFilesSelect">
                                    </div>
                                </div>

                                <PrimaryButton text="Submit Assignment" :block="true" :showLoader="form.processing"
                                    :disabled="form.processing || !form.attachment" />

                                <span class="d-block text-muted mt-3 text-center small">Teacher will be notified when
                                    you
                                    submit your assignment</span>
                            </form>

                            <!-- Files -->
                            <div v-else-if="submission.data?.attachment" class="mt-2 pt-3">

                                <h6>Uploaded File</h6>

                                <div class="attachment-list">
                                    <div class="attachment-item">
                                        <div class="attachment-info">
                                            <i class="fas file-icon" :class="submission.data.attachment.icon"></i>
                                            <div class="file-details">
                                                <span class="file-name">{{ submission.data.attachment.name }}</span>
                                                <span class="file-size">{{ submission.data.attachment.size }}</span>
                                            </div>
                                        </div>
                                        <a :href="submission.data.attachment.url" class="action-btn fs-6">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="submission.data?.marks" class="card card-body mt-3">

                        <h5 class="card-title">Evaluation</h5>

                        <div class="mb-2">
                            <span class="me-1">Marks : </span>
                            <span>
                                {{ submission.data.marks }} / {{ data.marks }}
                                <span class="ms-1">({{ submission.data.percentage }}%)</span>
                            </span>
                        </div>
                        <div class="mb-2">
                            <span class="me-1">Teacher's Feedback : </span>
                            <span>
                                {{ submission.data.feedback ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
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

/* File upload area */
.file-upload-area {
    border: 2px dashed var(--border-color);
    border-radius: 12px;
    padding: 1rem 1.5rem;
    transition: all 0.3s ease;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.9rem;
}

.file-upload-area:hover {
    border-color: var(--primary);
    background-color: rgba(127, 86, 217, 0.05);
}

.file-upload-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background-color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.25rem;
}
</style>
