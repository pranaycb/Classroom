<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Alert from '@/Components/Alert.vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <AuthLayout title="Email Verification">

        <div class="pb-4 mb-1">
            <h6 class="card-title justify-content-center pb-0 mb-2">
                Email Verification
            </h6>
            <p class="text-center">
                Verify your registered email address to continue
            </p>
        </div>

        <p class="text-body text-center mb-4 pb-2">
            Thanks for signing up! Before getting started, could you verify your
            email address by clicking on the link we just emailed to you? If you
            didn't receive the email, we will gladly send you another.
        </p>

        <Alert v-if="verificationLinkSent" type="success" text="A new verification link has been sent to the email address you
            provided during registration." />

        <form @submit.prevent="submit">
            <div class="mt-4 d-flex flex-column gap-3">

                <PrimaryButton text="Resend Verification Email" :block="true" :showLoader="form.processing"
                    :disabled="form.processing" />

                <Link :href="route('logout')" method="post" as="button" class="btn btn-danger w-100" replace>Cancel &
                Logout
                </Link>
            </div>
        </form>
    </AuthLayout>
</template>
