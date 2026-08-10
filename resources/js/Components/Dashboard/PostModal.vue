<script setup>
import Alert from '../Alert.vue';
import { computed, ref } from 'vue';
import PrimaryButton from '../PrimaryButton.vue';
import { useForm, usePage } from '@inertiajs/vue3';

const form = useForm({
    content: '',
    attachments: [],
});

const modal = ref();
const editor = ref();
const fileInput = ref(null);
const fileSeceted = ref([]);

/**
 * Trigger file upload
 */
const triggerFileInput = () => fileInput.value.click();

/**
 * Show and process the selected files
 */
const handleFilesSelect = (event) => {

    const files = event.target.files;

    if (files.length > 0) {

        for (let i = 0; i < files.length; i++) {

            const file = files[i];

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

            form.attachments.push(file);

            fileSeceted.value.push({
                file: file,
                name: file.name,
                icon,
                size: fileSize,
            });
        }
    }

    // Reset file input
    event.target.value = '';
};

/**
 * Remove an attachment from the list
 */
const removeFile = (index) => {
    form.attachments.splice(index, 1);
    fileSeceted.value.splice(index, 1);
    form.clearErrors(`attachments.${index}`);
};

/**
 * Disable the submit button if nothing entered
 */
const submitBtnDisabled = computed(() => {
    return (form.content === '<p><br></p>' || form.content === '') && (form.attachments.length == 0);
});

/**
 * Submit form
 */
const submit = () => {

    const code = usePage().props.room.code;

    form.post(route('dashboard.classroom.streams.store', code), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        }
    })
};

/**
 * Reset form and close modal
 */
const closeModal = () => {
    form.reset();
    form.clearErrors();
    editor.value.setHTML('');
    const bs5modal = bootstrap.Modal.getInstance(modal.value);
    bs5modal.hide();
}
</script>

<template>
    <div class="card shadow-none mb-4">
        <div class="card-body p-3">
            <div class="d-flex gap-3 align-items-center">
                <img class="avatar" :src="$page.props.auth.user.profile" />
                <div class="flex-grow-1 announcement-form" data-bs-toggle="modal" data-bs-target="#postModal">
                    <p class="text-muted mb-0">Announce something to your class</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Post Modal -->
    <div class="modal" ref="modal" id="postModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-hidden="false">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" @submit.prevent="submit">
                <div class="modal-header">
                    <h5 class="modal-title w-100 fw-bold">Announcement</h5>
                    <button type="button" class="btn-close fs-1" @click="closeModal"></button>
                </div>
                <div class="modal-body p-0">

                    <Alert class="mx-3" type="danger" v-if="form.errors.error" :text="form.errors.error" />

                    <!-- Quill Editor -->
                    <QuillEditor ref="editor" v-model:content="form.content" contentType="html"
                        placeholder="Announce something to your class" />

                    <!-- Files Preview Container -->
                    <div v-if="fileSeceted.length > 0" class="mx-3">

                        <h6 class="mb-2">Attachments</h6>

                        <div class="attachment-list">

                            <div v-for="(file, index) in fileSeceted" class="attachment-item"
                                :class="form.errors[`attachments.${index}`] && 'error'">
                                <div class="attachment-info">

                                    <i class="fas file-icon" :class="file.icon"></i>

                                    <div class="file-details">
                                        <span class="file-name">{{ file.name }}</span>
                                        <span class="file-size">{{ file.size }}</span>

                                        <span class="text-danger d-block mt-1" style="font-size: 0.8rem;">
                                            {{ form.errors[`attachments.${index}`] }}
                                        </span>
                                    </div>
                                </div>
                                <button type="button" class="btn-close fs-4" @click="removeFile(index)"></button>
                            </div>
                        </div>
                    </div>

                    <!-- Attach Upload -->
                    <div class="d-flex align-items-center justify-content-between mt-3 p-2 mx-3 mb-3 rounded border">

                        <div class="text-body">Add attachments to your post</div>
                        <!-- Files Upload -->
                        <div class="attachment-option mx-1" @click="triggerFileInput">
                            <i class="fas fa-arrow-up-from-bracket"></i>
                        </div>
                        <!-- Hidden file input -->
                        <input type="file" ref="fileInput" style="display: none;" @change="handleFilesSelect" multiple>
                    </div>
                </div>
                <div class="modal-footer">
                    <PrimaryButton text="Post" :block="true" :showLoader="form.processing"
                        :disabled="form.processing || submitBtnDisabled" />
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
@import url("@/css/post-modal.css");
@import url("@/css/attachment.css");
</style>
