<script setup>
import { computed } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';
import JoinRequest from '@/Components/Dashboard/JoinRequest.vue';

const props = defineProps({
    room: Object,
    requests: Object
});

const requests = computed(() => {
    return props.requests.data.length > 0 ? props.requests : null;
});
</script>

<template>
    <ClassroomLayout active="people">
        <div class="container">
            <div v-if="requests" class="mb-4">

                <h3 class="h6 mb-3 fw-semibold">Joining Request ({{ requests.meta.total }})</h3>

                <div class="list-group shadow-none">
                    <template v-for="participant in requests.data">
                        <JoinRequest :data="participant" />
                    </template>
                </div>

                <div class="mt-5">
                    <Pagination :meta="requests.meta" attr="requests" class="flex-column-reverse" />
                </div>
            </div>

            <span v-else class="d-block text-center text-muted">No joining request found.</span>
        </div>
    </ClassroomLayout>
</template>
