<script setup>
import { useForm } from '@inertiajs/vue3';
import { useToast } from '@/Compose/useToast';
import Checkbox from '@/Components/Checkbox.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';

const { showToast } = useToast();

const props = defineProps({
    room: Object,
    permissions: Object
});

const form = useForm({
    permissions: props.permissions
});

const submit = () => {
    form.put(route('dashboard.classroom.people.permissions.update', props.room.code), {
        onSuccess: (e) => showToast(e.props.success, 'success'),
    })
};
</script>

<template>
    <ClassroomLayout active="people">
        <div class="container">

            <div class="p-4" style="border: 2px dashed var(--border-color);">

                <h3 class="h5 mb-4 pb-1 fw-semibold">
                    Moderator's Permissions
                </h3>

                <form @submit.prevent="submit">
                    <div class="table-responsive mb-4">
                        <table class="table table-borderless">
                            <thead class="bg-light">
                                <tr>
                                    <th>Module</th>
                                    <th>Permissions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="permission, role in form.permissions" :key="Math.random()">
                                    <td class="text-capitalize">
                                        {{ role.replaceAll('_', ' ') }}
                                    </td>
                                    <td class="text-wrap">
                                        <div v-for="value, key in permission"
                                            class="form-check form-check-inline text-capitalize">

                                            <Checkbox :name="role + '.' + key" :checked="form.permissions[role][key]"
                                                v-model="form.permissions[role][key]" :id="role + '-' + key" />

                                            <InputLabel :value="key.replaceAll('_', ' ')" :for="role + '-' + key"
                                                class="fw-normal" :required="false" />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <PrimaryButton text="Update Permission" :showLoader="form.processing" :disabled="form.processing" />
                </form>
            </div>

        </div>
    </ClassroomLayout>
</template>
