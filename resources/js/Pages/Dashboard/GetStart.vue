<script setup>
import { useForm } from '@inertiajs/vue3';
import Alert from '@/Components/Alert.vue';
import { useToast } from '@/Compose/useToast';
import Checkbox from '@/Components/Checkbox.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const { showToast } = useToast();

// Classroom create form
const createForm = useForm({
    title: '',
    section: '',
    subject: '',
    room: '',
    moderation: false,
});

const submitCreateForm = () => createForm.post(route('dashboard.getstart.create'), {
    onSuccess: (e) => showToast(e.props.success, 'success')
});

// Classroom joining form
const joiningForm = useForm({
    code: '',
});

const submitJoiningForm = () => joiningForm.post(route('dashboard.getstart.join'), {
    onSuccess: () => joiningForm.reset(),
});
</script>

<template>
    <DashboardLayout title="Get Started" breadcrumb="Get Started" :back="true">
        <div class="container py-4">
            <ul class="nav d-flex flex-sm-row gap-2 align-items-center justify-content-center" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#create" role="tab">Create
                        Class</button>
                </li>

                <li class="nav-item mx-2" role="presentation">OR</li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#join" role="tab">Join
                        Class</button>
                </li>
            </ul>

            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8 col-xl-6">
                    <div class="" style="border: 2px dashed var(--border-color);">

                        <div class="tab-content px-3 py-4 rounded-3">

                            <div class="tab-pane fade show active" id="create" role="tabpanel">
                                <h5>Create Class</h5>
                                <p class="small">Fillup the below form to create a classroom to start teaching</p>
                                <hr class="my-3 pb-1" style="border-color: var(--border-color);" />

                                <form class="" @submit.prevent="submitCreateForm">

                                    <div class="mb-4">
                                        <InputLabel value="Class Name" />
                                        <TextInput type="text" v-model="createForm.title"
                                            :error="createForm.errors.title" placeholder="example room" />
                                    </div>

                                    <div class="mb-4">
                                        <InputLabel value="Section" />
                                        <TextInput type="text" v-model="createForm.section"
                                            :error="createForm.errors.section" placeholder="section a" />
                                    </div>

                                    <div class="mb-4">
                                        <InputLabel value="Subject" />
                                        <TextInput type="text" v-model="createForm.subject"
                                            :error="createForm.errors.subject" placeholder="mathematics" />
                                    </div>

                                    <div class="mb-4">
                                        <InputLabel value="Room" />
                                        <TextInput type="text" v-model="createForm.room" :error="createForm.errors.room"
                                            placeholder="cx15" />
                                    </div>

                                    <div class="mb-4">
                                        <div class="form-check">
                                            <Checkbox name="remember" v-model:checked="createForm.moderation"
                                                id="moderation" />
                                            <label class="form-check-label" for="moderation">
                                                Enable room moderation?

                                                <i role="button" class="fas fa-circle-question" data-bs-toggle="tooltip"
                                                    data-bs-title="When moderation is enabled, the teacher or the moderator(s) has to manually approve students joining requests to the classroom. Enable it if you want to create a private classroom."></i>
                                            </label>
                                        </div>
                                    </div>

                                    <PrimaryButton text="Create Room" :block="true" :showLoader="createForm.processing"
                                        :disabled="createForm.processing" />

                                </form>
                            </div>

                            <div class="tab-pane fade" id="join" role="tabpanel">

                                <h5>Join Class</h5>
                                <p class="small">Enter the class code to join a classroom</p>
                                <hr class="my-3 pb-1" style="border-color: var(--border-color);" />

                                <Alert v-if="$page.props.status" type="primary" :text="$page.props.status" />

                                <form class="" @submit.prevent="submitJoiningForm">

                                    <div class="mb-4">
                                        <InputLabel value="Class Code" />
                                        <TextInput type="text" v-model="joiningForm.code"
                                            :error="joiningForm.errors.code" placeholder="3546s416846" />
                                    </div>

                                    <PrimaryButton text="Join Room" :block="true" :showLoader="joiningForm.processing"
                                        :disabled="joiningForm.processing" />
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<style scoped>
@import url("@/css/dashboard/create-room.css");
</style>
