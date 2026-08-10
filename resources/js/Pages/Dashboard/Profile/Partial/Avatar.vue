<script setup>
import { onMounted, ref } from "vue";
import { useToast } from '@/Compose/useToast';
import { usePage, useForm } from "@inertiajs/vue3";
import FileInput from "@/Components/FileInput.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { showToast } = useToast();

const details = usePage().props.details;

const photo = ref();
const modal = ref();
let modalInstance = null;

onMounted(() => modalInstance = new bootstrap.Modal(modal.value));

const form = useForm({
    _method: 'put',
    photo: '',
});

// upload photo
const pickImage = (e) => {
    form.photo = e.target.files[0]
    photo.value.src = URL.createObjectURL(form.photo);
};

const submit = () => form.post(route('dashboard.profile.avatar.update'), {
    onSuccess: (e) => {
        modalInstance.hide();
        showToast(e.props.success, 'success');
    }
});
</script>

<template>
    <div class="p-3 mb-4" style="border: 2px dashed var(--border-color);">

        <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">

            <div class="d-flex flex-column flex-sm-row align-items-center gap-3">
                <img :src="details.avatar" class="avatar-lg" />
                <div>
                    <h5>{{ details.name }}</h5>
                    <span class="text-muted">{{ details.email }}</span>
                </div>
            </div>

            <button class="btn btn-secondary text-capitalize" data-bs-toggle="modal" data-bs-target="#avatarUpdate">
                <i class="fa-solid fa-pen-to-square"></i> Edit
            </button>
        </div>
    </div>

    <!-- Avatar Update Modal -->
    <div ref="modal" class="modal" id="avatarUpdate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Update Photo</h1>
                    <button type="button" class="btn-close fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row gy-4 justify-content-center" @submit.prevent="submit">

                        <div class="col-sm-12 col-md-7 col-lg-7">
                            <img ref="photo" :src="details.avatar" class="avatar-lg d-block mx-auto" />
                        </div>

                        <div class="col-sm-12 col-md-7 col-lg-7">
                            <InputLabel value="Photo" />
                            <FileInput type="file" :error="form.errors.photo" @input="pickImage" />
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
