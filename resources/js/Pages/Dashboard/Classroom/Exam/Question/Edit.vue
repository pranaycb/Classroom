<script setup>
import { useForm } from '@inertiajs/vue3';
import { useToast } from '@/Compose/useToast';
import Checkbox from "@/Components/Checkbox.vue";
import TextInput from '@/Components/TextInput.vue';
import MathEditor from '@/Components/MathEditor.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';

const { showToast } = useToast();

const props = defineProps({
    room: Object,
    exam: Object,
    data: Object,
});

const form = useForm({
    question: props.data.question,
    right: props.data.right,
    wrong: props.data.wrong,
    options: props.data.options,
});

const addOption = () => {
    form.options.push({
        option: '',
        correct: false,
    });
};

const removeOption = (index) => {
    form.options.splice(index, 1);
    if (form.options.length === 0) {
        form.options = [{
            option: '',
            correct: false,
        }];
    }
};

const submit = () => {
    form.put(route('dashboard.classroom.exams.questions.update', [
        props.room.code, props.exam.id, props.data.id
    ]), {
        preserveScroll: true,
        onSuccess: (e) => showToast(e.props.success, 'success'),
        onError: (e) => e.error && showToast(e.error, 'danger'),
    });
};
</script>

<template>
    <ClassroomLayout active="exam">
        <div class="container">

            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8 col-xl-6">

                    <div class="p-4" style="border: 2px dashed var(--border-color);">

                        <h5>Update Question</h5>
                        <p class="small">Fillup the below form to update question</p>
                        <hr class="my-3 pb-1" style="border-color: var(--border-color);" />

                        <form class="row gy-4" @submit.prevent="submit">

                            <div class="col-12">
                                <InputLabel value="Exam" />
                                <div class="form-control">{{ exam.name }}</div>
                            </div>

                            <div class="col-md-6">
                                <InputLabel value="Right Answer Mark" />
                                <TextInput :error="form.errors.right" v-model="form.right" />
                            </div>

                            <div class="col-md-6">
                                <InputLabel value="Negetive Mark" />
                                <TextInput :error="form.errors.wrong" v-model="form.wrong" />
                            </div>

                            <div class="col-md-12">
                                <InputLabel value="Question" />
                                <MathEditor v-model="form.question" :error="form.errors.question" />
                            </div>

                            <div class="col-12" v-for="option, index in form.options">

                                <div class="row gy-4">

                                    <div class="col-12">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <InputLabel :value="`Option ${index + 1}`" />

                                            <span role="button" class="text-danger small"
                                                @click="removeOption(index)">Remove</span>
                                        </div>
                                        <MathEditor v-model="option.option"
                                            :error="form.errors[`options.${index}.option`]" />
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check form-check-inline">
                                            <Checkbox name="correct" v-model:checked="option.correct"
                                                :id="`mark_${index}_as_correct`"
                                                :error="form.errors[`options.${index}.option`]" />
                                            <label class="form-check-label" :for="`mark_${index}_as_correct`">
                                                Mark this option as correct answer
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 text-center mb-4">
                                <button type="button" class="btn btn-secondary btn-sm" @click="addOption">
                                    Add a new option
                                </button>
                            </div>

                            <div class="col-12">
                                <PrimaryButton text="Update Question" :block="true" :showLoader="form.processing"
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
