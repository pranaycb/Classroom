<script setup>
import Alert from '@/Components/Alert.vue';
import Checkbox from '@/Components/Checkbox.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

defineProps({
    status : String
});
</script>

<template>
    <DashboardLayout title="Get Started" breadcrumb="Get Started" :back="true">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8 col-xl-6">
                    <div class="alert alert-info text-center">
                        {{ status }}
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
