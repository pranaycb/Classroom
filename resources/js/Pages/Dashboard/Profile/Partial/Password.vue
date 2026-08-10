<script setup>
import { onMounted, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from '@/Compose/useToast';
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { showToast } = useToast();

const modal = ref();
let modalInstance = null;

onMounted(() => modalInstance = new bootstrap.Modal(modal.value));

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submit = () => form.put(route('dashboard.profile.password.update'), {
    onSuccess: (e) => {
        modalInstance.hide();
        showToast(e.props.success, 'success');
    }
});
</script>

<template>
    <div class="p-3" style="border: 2px dashed var(--border-color);">
        <div class="card-title mb-0 pb-0">
            <div>
                <h6>Password</h6>
                <span class="text-muted">•••••••••••••</span>
            </div>
            <button class="btn btn-secondary text-capitalize" data-bs-toggle="modal" data-bs-target="#passUpdate">
                <i class="fa-solid fa-pen-to-square"></i> Edit
            </button>
        </div>
    </div>

    <!-- Password Update Modal -->
    <div ref="modal" class="modal" id="passUpdate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Update Password</h1>
                    <button type="button" class="btn-close fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row gy-4" @submit.prevent="submit">

                        <div class="col-12">
                            <InputLabel value="Current Password" />
                            <TextInput :error="form.errors.current_password" v-model="form.current_password"
                                placeholder="Enter your current password" />
                        </div>

                        <div class="col-12">
                            <InputLabel value="New Password" />
                            <TextInput :error="form.errors.password" v-model="form.password"
                                placeholder="Enter a new password" />
                        </div>

                        <div class="col-12">
                            <InputLabel value="Confirm New Password" />
                            <TextInput :error="form.errors.password_confirmation" v-model="form.password_confirmation"
                                placeholder="Confirm your new password" />
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
