<script setup>
import { onMounted, ref } from "vue";
import { useToast } from '@/Compose/useToast';
import TextInput from "@/Components/TextInput.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { showToast } = useToast();

const modal = ref();
let modalInstance = null;

const details = usePage().props.details;

onMounted(() => modalInstance = new bootstrap.Modal(modal.value));

const form = useForm({
    name: details.name,
    email: details.email,
    phone: details.phone,
    university: details.university,
    department: details.department,
    metric_id: details.metric_id,
    designation: details.designation,
});

const submit = () => form.put(route('dashboard.profile.info.update'), {
    onSuccess: (e) => {
        modalInstance.hide();
        showToast(e.props.success, 'success');
    }
});
</script>

<template>
    <div class="p-3 mb-4" style="border: 2px dashed var(--border-color);">

        <div class="card-title">
            <h6>Personal Information</h6>

            <button class="btn btn-secondary text-capitalize" data-bs-toggle="modal" data-bs-target="#infoUpdate">
                <i class="fa-solid fa-pen-to-square"></i> Edit
            </button>
        </div>

        <div class="row gy-4">

            <div class="col-md-6">
                <div class="d-flex flex-column gap-2">
                    <small class="text-muted">Name</small>
                    <span class="fw-medium">{{ details.name }}</span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="d-flex flex-column gap-2">
                    <small class="text-muted">Email</small>
                    <span class="fw-medium">{{ details.email }}</span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="d-flex flex-column gap-2">
                    <small class="text-muted">Phone</small>
                    <span class="fw-medium">{{ details.phone ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="d-flex flex-column gap-2">
                    <small class="text-muted">University</small>
                    <span class="fw-medium">{{ details.university ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="d-flex flex-column gap-2">
                    <small class="text-muted">Department</small>
                    <span class="fw-medium">{{ details.department ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="d-flex flex-column gap-2">
                    <small class="text-muted">Metric Id</small>
                    <span class="fw-medium">{{ details.metric_id ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="d-flex flex-column gap-2">
                    <small class="text-muted">Designation</small>
                    <span class="fw-medium">{{ details.designation ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Update Modal -->
    <div ref="modal" class="modal" id="infoUpdate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Update Personal Info</h1>
                    <button type="button" class="btn-close fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row gy-4" @submit.prevent="submit">

                        <div class="col-12">
                            <InputLabel value="Name" />
                            <TextInput :error="form.errors.name" v-model="form.name"
                                placeholder="Enter your name" />
                        </div>

                        <div class="col-md-6">
                            <InputLabel value="Email Id" />
                            <TextInput :error="form.errors.email" v-model="form.email"
                                placeholder="Enter email id" />
                        </div>

                        <div class="col-md-6">
                            <InputLabel value="Phone" />
                            <TextInput :error="form.errors.phone" v-model="form.phone"
                                placeholder="Enter phone number" />
                        </div>

                        <div class="col-12">
                            <InputLabel value="University Name" />
                            <TextInput :error="form.errors.university" v-model="form.university"
                                placeholder="Enter your university name" />
                        </div>

                        <div class="col-md-6">
                            <InputLabel value="Department" />
                            <TextInput :error="form.errors.department" v-model="form.department"
                                placeholder="Enter your department" />
                        </div>

                        <div class="col-md-6">
                            <InputLabel value="Metric Id" />
                            <TextInput :error="form.errors.metric_id" v-model="form.metric_id"
                                placeholder="Enter your metric id" />
                        </div>

                        <div class="col-12">
                            <InputLabel value="Designation" />
                            <TextInput :error="form.errors.designation" v-model="form.designation"
                                placeholder="Enter your designation" />
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
</template>
