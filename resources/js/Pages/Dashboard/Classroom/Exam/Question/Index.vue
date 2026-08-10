<script setup>
import { computed } from 'vue';
import { useToast } from '@/Compose/useToast';
import Spinner from '@/Components/Spinner.vue';
import { Deferred, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { useConfirmModal } from '@/Compose/useConfirmModal';
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';
import usePermissions from '@/Compose/usePermissions';

const { showToast } = useToast();
const { hasPermissions } = usePermissions();

const props = defineProps({
    room: Object,
    exam: Object,
    questions: Object,
});

const questions = computed(() => {
    return props.questions?.data.length > 0 ? props.questions : null;
});

// delete a question
const deleteQuestion = async (question) => {

    const { confirm } = useConfirmModal();

    const confirmed = await confirm('Are you sure you wanted to delete this question?');

    if (confirmed) {
        router.delete(route('dashboard.classroom.exams.questions.destroy', [props.room.code, props.exam.id, question]), {
            replace: true,
            preserveState: true,
            preserveScroll: true,
            onSuccess: (e) => showToast(e.props.success, 'success')
        });
    }
}
</script>

<template>
    <ClassroomLayout active="exam">
        <div class="container">

            <div class="p-3" style="border: 1px dashed var(--border-color);">
                <div class="table-responsive mb-5">
                    <table class="table table-borderless">
                        <thead class="bg-light">
                            <tr>
                                <th>Exam Name</th>
                                <th>Start</th>
                                <th>Duration</th>
                                <th>Questions Added</th>
                                <th>Marks</th>
                                <th v-if="exam.addMore"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ exam.name }}</td>
                                <td>{{ exam.start }}</td>
                                <td>{{ exam.duration }}</td>
                                <td>{{ questions?.meta.total ?? 0 }}</td>
                                <td>{{ exam.marks }}</td>
                                <td v-if="exam.addMore && hasPermissions('question.create')">
                                    <Link
                                        :href="route('dashboard.classroom.exams.questions.create', [room.code, exam.id])"
                                        class="btn btn-primary">
                                        <i class="fa-regular fa-plus me-1"></i> Add Question
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Deferred data="questions">

                    <template #fallback>
                        <Spinner />
                    </template>

                    <template v-if="questions">
                        <div class="row gy-5 gx-5">
                            <div v-for="question in questions.data" class="col-lg-6">

                                <div class="p-3" style="border: 1px dashed var(--border-color);">

                                    <div class="d-flex align-items-start gap-3">
                                        <span class="fw-bold d-flex align-items-center gap-1">
                                            {{ question.s }} <i class="fa-solid fa-caret-right"></i>
                                        </span>
                                        <span class="question" v-html="question.question"></span>
                                    </div>

                                    <div class="d-flex gap-3 justify-content-end text-nowrap small mb-3">
                                        <span class="text-success text-end">
                                            Right: + {{ question.right }}
                                        </span>
                                        <span v-if="question.wrong" class="text-danger">
                                            Wrong: - {{ question.wrong }}
                                        </span>
                                    </div>

                                    <div v-for="(option, index) in question.options"
                                        class="d-flex align-items-start gap-2 mb-2 px-3 py-2"
                                        :class="option.correct ? 'bg-success-light' : 'bg-light'">
                                        <span class="">
                                            ({{ index + 1 }})
                                        </span>
                                        <span class="option" v-html="option.option"></span>
                                        <span v-if="option.correct" class="ms-auto">
                                            <i class="fa-solid fa-circle-check text-success"></i>
                                        </span>
                                    </div>

                                    <div class="border-top pt-2 d-flex justify-content-between gap-2 small">
                                        <Link v-if="hasPermissions('question.update')"
                                            :href="route('dashboard.classroom.exams.questions.edit', [room.code, exam.id, question.id])"
                                            class="">
                                        <i class="fa-solid fa-edit"></i> Update
                                        </Link>
                                        <a v-if="hasPermissions('question.delete')" role="button" @click="deleteQuestion(question.id)" class="text-danger">
                                            <i class="fa-solid fa-trash-can"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <Pagination :meta="questions.meta" attr="questions" class="flex-column-reverse" />
                        </div>
                    </template>
                    <div v-else class="text-muted text-center">
                        No questions found for the exam
                    </div>
                </Deferred>
            </div>
        </div>
    </ClassroomLayout>
</template>

<style>
.question img {
    width: 100% !important;
}

.option p:last-child {
    margin: 0;
    padding: 0;
}
</style>
