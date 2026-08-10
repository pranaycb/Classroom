<script setup>
import { ref } from 'vue';
import Editor from '@tinymce/tinymce-vue';
import InputError from "./InputError.vue";

defineProps({
    error: {
        type: String,
        default: null,
    },
    errorClass: {
        type: String,
        default: null,
    },
});

const model = defineModel({
    required: true,
});

const input = ref(null);

defineExpose({ focus: () => input.value.focus() });

const editorInit = {
    height: 250,
    external_plugins: {
        tiny_mce_wiris: `https://cdn.jsdelivr.net/npm/@wiris/mathtype-tinymce7@8.13.3/plugin.min.js`
    },
    plugins: [
        'lists', 'link', 'image', 'preview', 'searchreplace', 'visualblocks', 'fullscreen', 'table'
    ],
    toolbar: 'undo redo | bullist numlist checklist outdent indent | tiny_mce_wiris_formulaEditor tiny_mce_wiris_formulaEditorChemistry',
    font_family_formats: "Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Cormorant Upright=cormorant upright;Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Anek Bangla=Anek Bangla; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats",
    content_style:
        '@import url("https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@400;500;600&amp;display=swap"); body { font-family: "Anek Bangla", sans-serif; }',
    draggable_modal: true,
    automatic_uploads: true,
    statusbar: false,
    extended_valid_elements: '*[.*]',
    images_upload_handler: function (blobInfo) {
        return new Promise((resolve, reject) => {
            resolve("data:" + blobInfo.blob().type + ";base64," + blobInfo.base64());
        });
    }
};
</script>

<template>
    <Editor api-key="no-api-key" licenseKey="gpl"
        tinymceScriptSrc="https://cdnjs.cloudflare.com/ajax/libs/tinymce/8.0.2/tinymce.min.js" :init="editorInit"
        v-model="model" :class="error && 'is-invalid'" v-bind="$attrs" />
    <InputError class="invalid-feedback" :class="errorClass" :message="error" />
</template>

<style>
.tox-promotion,
.tox-statusbar__branding {
    display: none;
}
</style>
