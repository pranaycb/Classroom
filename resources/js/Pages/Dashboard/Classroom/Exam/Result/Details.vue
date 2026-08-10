<script setup>
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';

const props = defineProps({
    room: Object,
    exam: Object,
    result: Object,
    answers: [Object, null],
});
</script>

<template>
    <ClassroomLayout active="exam">
        <div class="container">
            <div class="p-3" style="border: 1px dashed var(--border-color);">

                <div class="table-responsive">
                    <table class="table table-bordered text-nowrap">
                        <tbody>
                            <tr>
                                <th>Exam</th>
                                <td colspan="5">{{ exam.name }}</td>
                            </tr>
                            <tr>
                                <th>Your Mark</th>
                                <th>Pass Mark</th>
                                <th>Highest Mark</th>
                                <th>Right Answered</th>
                                <th>Wrong Answered</th>
                                <th>Result Status</th>
                            </tr>
                            <tr>
                                <td>{{ result.mark }}</td>
                                <td>{{ result.pass_mark }}</td>
                                <td>{{ result.highest }}</td>
                                <td>{{ result.right }}</td>
                                <td>{{ result.wrong }}</td>
                                <td>
                                    <span v-if="result.status === 'passed'" class="badge bg-success-light text-success">
                                        <i class="ri-check-line"></i> passed
                                    </span>
                                    <span v-else class="badge bg-danger-light text-danger">
                                        <i class="ri-forbid-line"></i> failed
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <template v-if="answers">

                    <hr class="my-4" style="border-color: var(--border-color);" />

                    <h5 class="card-title">Answers Analysis</h5>

                    <div class="row gy-5 gx-5 mb-5">

                        <div v-for="(answer, index) in answers" class="col-lg-6">

                            <div class="p-3" style="border: 1px dashed var(--border-color);">

                                <div class="d-flex align-items-start gap-3">

                                    <span class="fw-bold d-flex align-items-center gap-1">
                                        {{ index + 1 }} <i class="fa-solid fa-caret-right"></i>
                                    </span>

                                    <span class="question" v-html="answer.question.ques"></span>

                                    <div class="ms-auto text-nowrap fw-bold">

                                        <span v-if="!answer.attempted">0</span>

                                        <span v-else-if="answer.correct" class="text-success">
                                            + {{ answer.mark }}
                                        </span>

                                        <span v-else-if="!answer.correct" class="text-danger">
                                            - {{ answer.mark }}
                                        </span>
                                    </div>
                                </div>

                                <div v-for="(option, index) in answer.question.options"
                                    class="d-flex align-items-start gap-2 mb-2 px-3 pt-2" :class="{
                                    'bg-danger-light': answer.attempted && option.answered && !answer.correct,
                                    'bg-success-light': option.correct,
                                }">

                                    <span class="">
                                        ({{ index + 1 }})
                                    </span>

                                    <span class="option" v-html="option.option"></span>

                                    <span class="ms-auto">
                                        <i :class="{
                                        'fa-solid fa-circle-xmark text-danger': answer.attempted && option.answered && !answer.correct,
                                        'fa-solid fa-circle-check text-success': option.correct,
                                    }"></i>
                                    </span>
                                </div>

                                <span v-if="!answer.attempted" class="text-muted small fw-medium">Not attempted</span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </ClassroomLayout>
</template>

<style scoped>
</style>
