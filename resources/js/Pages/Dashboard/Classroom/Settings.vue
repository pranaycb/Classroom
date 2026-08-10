<script setup>
import { useForm } from '@inertiajs/vue3';
import { useToast } from '@/Compose/useToast';
import Checkbox from '@/Components/Checkbox.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';

const { showToast } = useToast();

const props = defineProps({
    room: Object,
    themes: Object,
});

// class create form
const form = useForm({
    title: props.room.title,
    section: props.room.section,
    subject: props.room.subject,
    room: props.room.room,
    theme: props.room.theme,
    moderation: props.room.moderation,
});

const submit = () => form.put(route('dashboard.classroom.settings.update', props.room.code), {
    preserveScroll: true,
    onSuccess: (e) => showToast(e.props.success, 'success'),
});
</script>

<template>
    <ClassroomLayout active="settings">
        <div class="container">

            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-10 col-xl-8">

                    <div class="p-4" style="border: 2px dashed var(--border-color);">

                        <h5>Settings</h5>
                        <p class="small">Update your classroom settings</p>
                        <hr class="my-3 pb-1" style="border-color: var(--border-color);" />

                        <form class="row gy-4" @submit.prevent="submit">

                            <div class="col-12">
                                <InputLabel value="Class Name" />
                                <TextInput type="text" v-model="form.title" :error="form.errors.title"
                                    placeholder="example room" />
                            </div>

                            <div class="col-md-6">
                                <InputLabel value="Section" />
                                <TextInput v-model="form.section" :error="form.errors.section"
                                    placeholder="section a" />
                            </div>

                            <div class="col-md-6">
                                <InputLabel value="Subject" />
                                <TextInput v-model="form.subject" :error="form.errors.subject"
                                    placeholder="mathematics" />
                            </div>

                            <div class="col-md-6">
                                <InputLabel value="Room" />
                                <TextInput v-model="form.room" :error="form.errors.room" placeholder="cx15" />
                            </div>

                            <div class="col-md-6 mb-2">
                                <InputLabel value="Theme" />
                                <div class="dropdown">
                                    <button class="btn btn-secondary text-capitalize rounded-3 w-100" type="button"
                                        data-bs-toggle="dropdown">
                                        <span
                                            class="dropdown-item d-flex align-items-center justify-content-between gap-5">
                                            <span class="text-capitalize">
                                                {{ form.theme }}
                                            </span>
                                            <span style="width: 10px; height: 10px; border-radius: 50%;"
                                                :style="{ backgroundColor: themes[form.theme] }"></span>
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu w-100" style="max-height: 350px; overflow-y: auto;">
                                        <li v-for="(color, name) in themes">
                                            <span
                                                class="dropdown-item d-flex align-items-center justify-content-between"
                                                @click="form.theme = name">
                                                <span class="text-capitalize">
                                                    {{ name }}
                                                </span>
                                                <span style="width: 10px; height: 10px; border-radius: 50%;"
                                                    :style="{backgroundColor: color}"></span>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch ps-0 d-flex gap-5 justify-content-between">
                                    <label class="form-check-label" for="moderation">
                                        Enable moderation for this
                                        room?
                                        <i role="button" class="fas fa-circle-question" data-bs-toggle="tooltip"
                                            data-bs-title="When moderation is enabled, the teacher or the moderator(s) has to manually approve students joining requests to the classroom. Enable it if you want to create a private classroom."></i>
                                    </label>

                                    <Checkbox name="moderation" v-model:checked="form.moderation" id="moderation" />
                                </div>
                            </div>

                            <div class="col-12">
                                <PrimaryButton text="Update" :block="true" :showLoader="form.processing"
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
</style>
