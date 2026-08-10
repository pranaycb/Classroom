<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Info from "@/Pages/Dashboard/Profile/Partial/Info.vue";
import Avatar from "@/Pages/Dashboard/Profile/Partial/Avatar.vue";
import Password from "@/Pages/Dashboard/Profile/Partial/Password.vue";
</script>

<template>
    <DashboardLayout title="Profile">
        <div class="container py-4">

            <h4 class="text-body mb-4">Profile</h4>

            <!-- Avatar -->
            <Avatar />

            <!-- Personal Info -->
             <Info />

            <!-- Password -->
            <Password />
        </div>
    </DashboardLayout>
</template>
