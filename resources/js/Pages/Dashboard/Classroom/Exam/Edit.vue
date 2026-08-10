<script setup>
import { useForm } from '@inertiajs/vue3';
import { useToast } from '@/Compose/useToast';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';

const { showToast } = useToast();

const props = defineProps({
    room: Object,
    data: Object,
});

// class create form
const form = useForm({
    name: props.data.name,
    start: props.data.start,
    end: props.data.end,
    marks: props.data.marks,
    pass_mark: props.data.pass_mark,
});

const submit = () => form.put(route('dashboard.classroom.exams.update', [props.room.code, props.data.id]), {
    preserveScroll: true,
    onSuccess: (e) => showToast(e.props.success, 'success'),
});
</script>

<template>
    <ClassroomLayout active="exam">
        <div class="container">

            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8 col-xl-6">

                    <div class="p-4" style="border: 2px dashed var(--border-color);">

                        <h5>Update Exam</h5>
                        <p class="small">Fillup the below form to Update exam</p>
                        <hr class="my-3 pb-1" style="border-color: var(--border-color);" />

                        <form class="row gy-4" @submit.prevent="submit">

                            <div class="col-12">
                                <InputLabel value="Exam Name" />
                                <TextInput type="text" v-model="form.name" :error="form.errors.name"
                                    placeholder="Enter a exam name" />
                            </div>

                            <div class="col-md-6">
                                <InputLabel value="Start Time" />
                                <TextInput type="datetime-local" v-model="form.start" :error="form.errors.start" />
                            </div>

                            <div class="col-md-6">
                                <InputLabel value="End Time" />
                                <TextInput type="datetime-local" v-model="form.end" :error="form.errors.end" />
                            </div>

                            <div class="col-md-6">
                                <InputLabel value="Total Mark" />
                                <TextInput v-model="form.marks" :error="form.errors.marks" />
                            </div>

                            <div class="col-md-6">
                                <InputLabel value="Pass Mark" />
                                <TextInput v-model="form.pass_mark" :error="form.errors.pass_mark" />
                            </div>

                            <div class="col-12">
                                <PrimaryButton text="Update Exam" :block="true" :showLoader="form.processing"
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
