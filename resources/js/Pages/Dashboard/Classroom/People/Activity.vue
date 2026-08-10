<script setup>
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';

const props = defineProps({
    room: Object,
    student: Object,
    exams: Object,
    assignments: Object,
    marks: Object,
    counter: Object,
});
</script>

<template>
    <ClassroomLayout active="people">
        <div class="container">
            <div class="mb-5">

                <h5 class="mb-4 pb-1 fw-semibold">{{ student.name }} - Activity History</h5>

                <div class="p-4" style="border: 2px dashed var(--border-color);">

                    <h5 class="mb-4">Exams Given</h5>

                    <div class="table-responsive mb-5">
                        <table class="table table-borderless">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Exam Name</th>
                                    <th>Pass Marks</th>
                                    <th>Obtained Marks</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="exams.length > 0" v-for="exam in exams">
                                    <td>{{ exam.date }}</td>
                                    <td>{{ exam.name }}</td>
                                    <td>{{ exam.pass_marks }}</td>
                                    <td>{{ exam.result.marks }}</td>
                                    <td>
                                        <span v-if="exam.result.status === 'passed'"
                                            class="badge bg-success-light text-success">
                                            Passed
                                        </span>
                                        <span v-else-if="exam.result.status === 'not-given'"
                                            class="badge bg-danger-light text-danger">
                                            Not given
                                        </span>
                                        <span v-else class="badge bg-danger-light text-danger">
                                            Failed
                                        </span>
                                    </td>
                                </tr>
                                <tr v-else>
                                    <td colspan="5">
                                        No exam record found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mb-4">Assignment Submitted</h5>

                    <div class="table-responsive mb-5">
                        <table class="table table-borderless">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Assignment</th>
                                    <th>Obtained Marks</th>
                                    <th>Feedback</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="assignments.length > 0" v-for="assignment in assignments">
                                    <td>{{ assignment.due }}</td>
                                    <td>{{ assignment.title }}</td>
                                    <td>{{ assignment.submission.marks }}</td>
                                    <td>{{ assignment.submission.feedback }}</td>
                                </tr>
                                <tr v-else>
                                    <td colspan="5">
                                        No assignment record found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mb-4">Marks Summary</h5>

                    <div class="bg-light p-4">

                        <div class="row gy-3 justify-content-center">

                            <div class="col-6 col-md-4 col-lg-3 text-center">
                                <h4 class="h6 text-muted">Assignment Submitted</h4>
                                <p class="h3 mb-0 text-primary fw-bold">
                                    {{ counter.assignment.eompleted }} / {{ counter.assignment.count }}
                                </p>
                            </div>

                            <div class="col-6 col-md-4 col-lg-3 text-center">
                                <h4 class="h6 text-muted">Exam Given</h4>
                                <p class="h3 mb-0 text-primary fw-bold">
                                    {{ counter.exam.eompleted }} / {{ counter.exam.count }}
                                </p>
                            </div>

                            <div class="col-6 col-md-4 col-lg-3 text-center">
                                <h4 class="h6 text-muted">Marks Obtained</h4>
                                <p class="h3 mb-0 text-primary fw-bold">
                                    {{ marks.obtained }} / {{ marks.total }}
                                </p>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3 text-center">
                                <h4 class="h6 text-muted">Overall Marks</h4>
                                <p class="h3 mb-0 text-primary fw-bold">{{ marks.percentage }}%</p>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </ClassroomLayout>
</template>
