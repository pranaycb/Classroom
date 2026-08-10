<script setup>
import { useForm } from "@inertiajs/vue3";
import Alert from "@/Components/Alert.vue";
import Checkbox from "@/Components/Checkbox.vue";
import TextInput from "@/Components/TextInput.vue";
import AuthLayout from "@/Layouts/AuthLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => form.post(route("login"));
</script>

<template>
    <AuthLayout title="Login">

        <div class="pb-4 mb-2">
            <h6 class="card-title justify-content-center pb-0 mb-2">
                Classroom Login
            </h6>
            <p class="text-center">
                Enter your email and password below to log in
            </p>
        </div>

        <Alert v-if="status" type="success" :text="status" />

        <form @submit.prevent="submit">

            <div class="mb-4 pb-1">
                <InputLabel value="Email Address" />
                <TextInput :error="form.errors.email" v-model="form.email" placeholder="email@example.com" />
            </div>

            <div class="mb-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <InputLabel value="Password" />
                    <Link class="small" :href="route('password.request')">Forgot Password?</Link>
                </div>
                <TextInput type="password" :error="form.errors.password" v-model="form.password"
                    placeholder="Password" />
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <Checkbox name="remember" v-model:checked="form.remember" id="rememberMe" />
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>
            </div>

            <div class="mb-4">
                <PrimaryButton text="Login" :block="true" :showLoader="form.processing" :disabled="form.processing" />
            </div>

            <div class="small text-center">
                Dont have an account?
                <Link :href="route('register')">Create an account</Link>
            </div>
        </form>
    </AuthLayout>
</template>
