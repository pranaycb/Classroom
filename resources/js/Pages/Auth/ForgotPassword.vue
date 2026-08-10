<script setup>
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Alert from '@/Components/Alert.vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => form.post(route('password.email'));
</script>

<template>
    <AuthLayout title="Forgot Password">

        <div class="pb-4 mb-1">
            <h6 class="card-title justify-content-center pb-0 mb-2">
                Forgot Password
            </h6>
            <p class="text-center">
                Enter your email to receive a password reset link
            </p>
        </div>

        <Alert v-if="status" type="success" :text="status" />

        <form @submit.prevent="submit">

            <div class="mb-4">
                <InputLabel value="Email Address" />
                <TextInput type="text" :error="form.errors.email" v-model="form.email" placeholder="email@example.com"
                    autocomplete />
            </div>

            <div class="mb-4">
                <PrimaryButton text="Email Password Reset Link" :block="true" :showLoader="form.processing"
                    :disabled="form.processing" />
            </div>

            <div class="small text-center">
                Or, return to
                <Link class="" :href="route('login')">Login</Link>
            </div>
        </form>
    </AuthLayout>
</template>
