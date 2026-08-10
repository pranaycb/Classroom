<script setup>
import { useForm } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import AuthLayout from "@/Layouts/AuthLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
});
</script>

<template>
    <AuthLayout title="Register">

        <div class="pb-4 mb-2">
            <h6 class="card-title justify-content-center pb-0 mb-2">
                Classroom Registration
            </h6>
            <p class="text-center">
                Enter your details below to create your account
            </p>
        </div>

        <form @submit.prevent="submit">

            <div class="mb-4">
                <InputLabel value="Name" />
                <TextInput :error="form.errors.name" v-model="form.name" placeholder="Full name" />
            </div>

            <div class="mb-4">
                <InputLabel value="Email Address" />
                <TextInput :error="form.errors.email" v-model="form.email" placeholder="email@example.com" />
            </div>

            <div class="mb-4">
                <InputLabel value="Password" />
                <TextInput type="password" :error="form.errors.password" v-model="form.password"
                    placeholder="Password" />
            </div>

            <div class="mb-4">
                <InputLabel value="Confirm Password" />
                <TextInput type="password" :error="form.errors.password_confirmation"
                    v-model="form.password_confirmation" placeholder="Password" />
            </div>

            <div class="mb-4">
                <PrimaryButton text="Create Account" :block="true" :showLoader="form.processing"
                    :disabled="form.processing" />
            </div>

            <div class="small text-center">
                Already have an account?
                <Link :href="route('login')">Login</Link>
            </div>
        </form>
    </AuthLayout>
</template>
