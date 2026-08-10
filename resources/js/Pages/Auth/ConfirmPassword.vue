<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const form = useForm({
    password: '',
});

const submit = () => form.post(route('password.confirm'), {
    onFinish: () => form.reset(),
});
</script>

<template>
    <AuthLayout title="Login">
        <div class="pb-4 mb-2">
            <h6 class="card-title justify-content-center pb-0 mb-2">
                Classroom Login
            </h6>
            <p class="text-center">
                Confirm your password before continuing
            </p>
        </div>
        <form @submit.prevent="submit">
            <div class="mb-4">
                <InputLabel value="Password" />
                <TextInput type="password" :error="form.errors.password" v-model="form.password"
                    placeholder="Password" />
            </div>
            <div class="mb-4">
                <PrimaryButton text="Confirm Password" :block="true" :showLoader="form.processing"
                    :disabled="form.processing" />
            </div>
        </form>
    </AuthLayout>
</template>
