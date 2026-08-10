<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from '@/Compose/useToast';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import MathEditor from '@/Components/MathEditor.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';

const { showToast } = useToast();

const props = defineProps({
    room: Object,
    data: Object,
});

// assignment create form
const form = useForm({
    _method: 'put',
    title: props.data.title,
    due: props.data.due,
    marks: props.data.marks,
    description: props.data.description,
    attachments: props.data.attachments,
});

const fileInput = ref(null);
const fileSeceted = ref(props.data.attachments);

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

            form.attachments.push({
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
    form.clearErrors(`attachments.${index}`);
};

const submit = () => form.post(route('dashboard.classroom.assignments.update', [props.room.code, props.data.id]), {
    preserveScroll: true,
    onSuccess: (e) => showToast(e.props.success, 'success'),
});
</script>

<template>
    <ClassroomLayout active="assignment">
        <div class="container">

            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-10 col-xl-8">

                    <div class="p-4" style="border: 2px dashed var(--border-color);">

                        <h5>Update Assignment</h5>
                        <p class="small">Fillup the below form to update the assignment</p>
                        <hr class="my-3 pb-1" style="border-color: var(--border-color);" />

                        <form class="row gy-4" @submit.prevent="submit">

                            <div class="col-12">
                                <InputLabel value="Assignment Title" />
                                <TextInput type="text" v-model="form.title" :error="form.errors.title"
                                    placeholder="Enter an assignment title" />
                            </div>

                            <div class="col-md-6">
                                <InputLabel value="Due Date" />
                                <TextInput type="datetime-local" v-model="form.due" :error="form.errors.due" />
                            </div>

                            <div class="col-md-6">
                                <InputLabel value="Marks" />
                                <TextInput v-model="form.marks" :error="form.errors.marks" />
                            </div>

                            <div class="col-md-12">
                                <InputLabel value="Description" />
                                <MathEditor v-model="form.description" :error="form.errors.description" />
                            </div>

                            <!-- Files Preview Container -->
                            <div v-if="form.attachments.length > 0" class="mx-3">

                                <h6 class="mb-2">Attachments</h6>

                                <div class="attachment-list">

                                    <div v-for="(file, index) in form.attachments" class="attachment-item"
                                        :class="form.errors[`attachments.${index}`] && 'error'">

                                        <div class="attachment-info">

                                            <i class="fas file-icon" :class="file.icon"></i>

                                            <div class="file-details">
                                                <span class="file-name">{{ file.name }}</span>
                                                <span class="file-size">{{ file.size }}</span>

                                                <span class="text-danger d-block mt-1" style="font-size: 0.8rem;">
                                                    {{ form.errors[`attachments.${index}.file`] }}
                                                </span>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close fs-4"
                                            @click="removeFile(index)"></button>
                                    </div>
                                </div>
                            </div>

                            <!-- Attach Upload -->
                            <div class="col-12">

                                <div
                                    class="d-flex align-items-center justify-content-between mt-3 p-2 rounded-3 border">


                                    <div class="text-body">Add attachments to your assignment</div>
                                    <!-- Files Upload -->
                                    <div class="attachment-option mx-1" @click="triggerFileInput">
                                        <i class="fas fa-arrow-up-from-bracket"></i>
                                    </div>
                                    <!-- Hidden file input -->
                                    <input type="file" ref="fileInput" style="display: none;"
                                        @change="handleFilesSelect" multiple>
                                </div>
                            </div>

                            <div class="col-12">
                                <PrimaryButton text="Update Assignment" :block="true" :showLoader="form.processing"
                                    :disabled="form.processing" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </ClassroomLayout>
</template>

<style scoped>
@import url("@/css/attachment.css");
</style>
