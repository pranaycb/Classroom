<script setup>
import { ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from '@/Compose/useToast';
import useCountDown from '@/Compose/useCountdown';
import RadioButton from "@/Components/RadioButton.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';

const { showToast } = useToast();

const props = defineProps({
    room: Object,
    exam: Object,
    questions: Object,
});

const cacheKey = `exam-${props.exam.id}-answers`;

// set answers locally in cookie
const setAnswersInLocally = (value) => {
    const expiryDate = new Date(props.exam.end);
    const expires = "expires=" + expiryDate.toUTCString();
    document.cookie = `${cacheKey}=${encodeURIComponent(value)}; ${expires}; path=/`;
}

// get answers from cookie
const getAnswersLocally = () => {
    const decodedCookie = decodeURIComponent(document.cookie);
    const cookies = decodedCookie.split('; ');
    for (let cookie of cookies) {
        const [key, val] = cookie.split('=');
        if (key === cacheKey) return val;
    }
    return null;
}

const timeOver = ref(false);

// Load cached answers from localStorage (object keyed by questionId)
let cachedAnswers = {}

try {
    cachedAnswers = JSON.parse(getAnswersLocally()) || {}
} catch (e) {
    cachedAnswers = {}
}

// Build form.answers as an array, but pull `answer` from cache by ID
const form = useForm({
    answers: props.questions.questions.map(q => ({
        question: q.id,
        answer: cachedAnswers[q.id] || '',
    })),
})

// Always save as object: { [questionId]: answer }
watch(() => form.answers, (newAnswers) => {
    const obj = {}
    newAnswers.forEach(a => {
        obj[a.question] = a.answer
    })
    setAnswersInLocally(JSON.stringify(obj))
}, { deep: true })

const submit = () => {
    form.post(route('dashboard.classroom.exams.results.store', [props.room.code, props.exam.id]), {
        onSuccess: (e) => {
            document.cookie = cacheKey + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            showToast(e.props.success, 'success');
        },
        onError: (e) => e.error && showToast(e.error, 'danger'),
    })
};

// count down timer
const { timeRemaining: tm } = useCountDown(props.exam.end, () => {
    timeOver.value = true;
    submit();
});
</script>

<template>
    <ClassroomLayout active="exam" :showMenu="false">

        <!-- Heading Exam Name | Time | Marks -->
        <div class="card-header text-center bg-primary">
            <h3 class="text-white">{{ exam.name }}</h3>
            <div class="small text-white fw-medium d-flex justify-content-center gap-2">
                <span>{{ exam.mark }} Marks</span>
                <div>|</div>
                <span>{{ questions.total }} Questions</span>
                <div>|</div>
                <span>{{ exam.duration }}</span>
            </div>
        </div>

        <!-- Count Down Timer -->
        <div class="sticky-top bg-primary text-center p-3" style="top: 70px; z-index: 1;">

            <div v-if="!timeOver" class="d-flex justify-content-center gap-3 h6 mb-0 text-white">

                <template v-if="tm.hours > 0">
                    <div class="d-flex gap-1 flex-column flex-sm-row">
                        <span>{{ tm.hours }}</span>
                        <span>Hour</span>
                    </div>
                    <span>:</span>
                </template>

                <div class="d-flex gap-1 flex-column flex-sm-row">
                    <span>{{ tm.minutes }}</span>
                    <span>Minute</span>
                </div>
                <span>:</span>
                <div class="d-flex gap-1 flex-column flex-sm-row">
                    <span>{{ tm.seconds }}</span>
                    <span>Second</span>
                </div>
            </div>
            <div v-else class="h6 mb-0 text-white">Time over</div>
        </div>

        <div class="container mt-5">

            <p class="text-center mb-4">Read the questions carefully and choose the correct answers.</p>

            <form v-if="!timeOver" class="row gy-5" @submit.prevent="submit">

                <div v-for="(question, index) in questions.questions" class="col-12">

                    <div class="p-3" style="border: 2px dashed var(--border-color);">

                        <div class="d-flex align-items-start gap-3">
                            <span class="fw-bold d-flex align-items-center gap-1">
                                {{ index + 1 }} <i class="fa-solid fa-caret-right"></i>
                            </span>
                            <span class="question" v-html="question.question"></span>
                        </div>

                        <div class="d-flex justify-content-end gap-3 text-nowrap small fw-medium mb-3">
                            <span class="text-success text-end">
                                Right: + {{ question.right }}
                            </span>
                            <span v-if="question.wrong" class="text-danger">
                                Wrong: - {{ question.wrong }}
                            </span>
                        </div>

                        <label v-for="(option, serial) in question.options"
                            class="d-block mb-2 px-3 py-2 bg-light d-flex align-items-start gap-3"
                            :for="'option_' + option.id">
                            <RadioButton :name="'option_' + question.id" v-model:checked="form.answers[index].answer"
                                :value="option.id" :id="'option_' + option.id" />

                            <span class="">
                                ({{ serial + 1 }})
                            </span>
                            <span class="option" v-html="option.option"></span>
                        </label>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-center">
                    <PrimaryButton text="Submit Answers" :showLoader="form.processing" :disabled="form.processing" />
                </div>
            </form>

            <div v-else class="alert alert-warning text-center">
                Exam time is over. We are submitting your answers. Please dont reload this page
            </div>
        </div>
    </ClassroomLayout>
</template>
