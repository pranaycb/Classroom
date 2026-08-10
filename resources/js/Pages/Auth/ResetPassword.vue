<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => form.post(route('password.store'));
</script>

<template>
    <AuthLayout title="Reset Password">

        <div class="pb-4 mb-1">
            <h6 class="card-title justify-content-center pb-0 mb-2">Reset Password</h6>
            <p class="text-center">
                Enter a new password for your account
            </p>
        </div>

        <form @submit.prevent="submit">

            <div class="mb-4">
                <InputLabel value="Email Address" />
                <TextInput :error="form.errors.email" v-model="form.email" placeholder="email@example.com"
                    autocomplete />
            </div>

            <div class="mb-4">
                <InputLabel value="New Password" />
                <TextInput type="password" :error="form.errors.password" v-model="form.password" placeholder="Password"
                    autocomplete />
            </div>

            <div class="mb-4">
                <InputLabel value="Confirm Password" />
                <TextInput type="password" :error="form.errors.password_confirmation"
                    v-model="form.password_confirmation" placeholder="Password" autocomplete />
            </div>

            <div>
                <PrimaryButton text="Reset Password" :block="true" :showLoader="form.processing"
                    :disabled="form.processing" />
            </div>
        </form>
    </AuthLayout>
</template>
