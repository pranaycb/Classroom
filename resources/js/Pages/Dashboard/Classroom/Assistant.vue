<script setup>
import { ref } from 'vue';
import Markdown from 'vue3-markdown-it';
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import MarkdownItHLJS from "markdown-it-highlightjs";
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';

const props = defineProps({
    room: Object
});

const chat = ref([]);
const chatBox = ref();
const fileInput = ref(null);
const fileSeceted = ref([]);
const isFullscreen = ref(false);

const plugins = [{
    plugin: MarkdownItHLJS
}];

const scrollToBottom = () => {
    chatBox.value.scrollTop = chatBox.value.scrollHeight;
};

const form = useForm({
    message: '',
    files: [],
});

/**
 * Trigger file upload
 */
const triggerFileInput = () => {
    fileInput.value.click();
};

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

            form.files.push(file);

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
    form.files.splice(index, 1);
    fileSeceted.value.splice(index, 1);
    form.clearErrors(`attachments.${index}`);
};

const send = () => {
    form.post(route('dashboard.classroom.assistant.ask', props.room.code), {
        preserveState: true,
        preserveScroll: true,
        onStart: () => {
            if(form.message || form.files.length > 0){
                chat.value.push({
                    role: 'user',
                    text: form.message,
                    files: fileSeceted.value,
                });
                scrollToBottom();
            }
        },
        onSuccess : (e) => {
            form.reset();
            form.files = [];
            fileSeceted.value = [];
            chat.value.push({
                role: 'assistant',
                html: e.props.success
            });
        }
    })
};

// toggle fullscreen
const toggleFullscreen = () => {
    isFullscreen.value = !isFullscreen.value
}
</script>

<template>
    <ClassroomLayout active="assistant" :showMenu="!isFullscreen">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-10 col-xl-8" :class="{'col-xl-12 pt-4' : isFullscreen}">
                    <div ref="fullscreenContainer" class="card d-flex flex-column"
                        :style="{ maxHeight: isFullscreen ? 'calc(100vh - 100px)' : 'calc(100vh - 330px)', minHeight: isFullscreen ? 'calc(100vh - 100px)' : 'calc(100vh - 330px)' }">

                        <div class="card-header d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="/storage/icons/drsmith.png" class="avatar" />
                                <div>
                                    <h5 class="text-body fw-bolder mb-0 text-primary">
                                        Dr. Smith
                                    </h5>
                                    <span>Your ai learning assistant</span>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-secondary" @click="toggleFullscreen">
                                <i v-if="isFullscreen" class="fa-solid fa-compress"></i>
                                <i v-else class="fa-solid fa-expand"></i>
                            </button>
                        </div>

                        <div ref="chatBox" class="card-body chat-messages flex-1">

                            <!-- Receiver Message -->
                            <div class="message receiver">
                                <img src="/storage/icons/drsmith.png" class="avatar" />
                                <div class="message-content">
                                    Hey I'm Dr. Smith. Your AI Learning Assistant. How can I help
                                    you today?
                                </div>
                            </div>

                            <template v-for="(msg, i) in chat" :key="i">

                                <!-- Receiver Message -->
                                <div v-if="msg.role === 'assistant'" class="message receiver">
                                    <img src="/storage/icons/drsmith.png" class="avatar" />
                                    <!-- <div class="message-content" v-html="md.render(msg.html)"></div> -->

                                    <div class="message-content">
                                        <Markdown :source="msg.html" :breaks="true" :html="true" :linkify="true"
                                            :typographer="true" :plugins="plugins" />
                                    </div>
                                </div>

                                <!-- Sender Message -->
                                <div v-else class="message sender">
                                    <div class="message-content">
                                        <span>{{ msg.text }}</span>

                                        <!-- Files Preview Container -->
                                        <div v-if="msg.files.length > 0" class="attachment-list" :class="msg.text ?? 'mt-5'">
                                            <div v-for="file in msg.files" class="attachment-item">
                                                <div class="attachment-info">

                                                    <i class="fas file-icon" :class="file.icon"></i>

                                                    <div class="file-details">
                                                        <span class="file-name text-body">{{ file.name }}</span>
                                                        <span class="file-size">{{ file.size }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <img :src="$page.props.auth.user.profile" class="avatar" />
                                </div>
                            </template>

                            <!-- Loading Message -->
                            <div v-if="(form.message || form.files.length > 0) && form.processing"
                                class="message receiver">
                                <img src="/storage/icons/drsmith.png" class="avatar" />
                                <div class="message-content">
                                    <i class="fas fa-ellipsis fa-fade fs-5"></i>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <form @submit.prevent="send">

                                <!-- Files Preview Container -->
                                <div v-if="fileSeceted.length > 0" class="attachment-list mb-3">

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
                                        <button v-if="!form.processing" type="button" class="btn-close fs-4"
                                            @click="removeFile(index)"></button>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start gap-2">

                                    <!-- Hidden file input -->
                                    <input type="file" ref="fileInput" style="display: none;"
                                        @change="handleFilesSelect" multiple>

                                    <button type="button" class="btn btn-sm btn-secondary"
                                        @click.prevent="triggerFileInput">
                                        <i class="fas fa-arrow-up-from-bracket"></i>
                                    </button>

                                    <div class="w-100">
                                        <TextInput v-model="form.message" :error="form.errors.message"
                                            placeholder="Ask anything" :disabled="form.processing" />
                                    </div>

                                    <button type="submit" class="btn btn-sm btn-primary" :disabled="form.processing">
                                        <i v-if="form.processing" class="fas fa-spinner fa-spin me-1"></i>
                                        <i v-else class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClassroomLayout>
</template>

<style scoped>
@import url('@/css/dashboard/assistant.css');
@import url("@/css/attachment.css");
</style>
