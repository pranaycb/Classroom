<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast } from '@/Compose/useToast';
import usePermissions from '@/Compose/usePermissions';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const { showToast } = useToast();
const { hasPermissions } = usePermissions();

defineProps({
    showMenu: {
        type: Boolean,
        default: true,
    },
    active: {
        type: String,
        default: 'stream'
    }
});

const room = computed(() => usePage().props.room);

const theme = computed(() => {
    const colors = usePage().props.theme;
    return colors[room.value.theme];
});

const copy = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
        showToast('Copied successfully', 'success');
    } catch (err) {
        showToast('Failed to copy', 'danger');
    }
};
</script>

<template>
    <DashboardLayout :title="room.title" :breadcrumb="room.title">

        <div :style="{ '--theme-color': theme }" class="pb-5">

            <template v-if="showMenu">

                <div class="class-banner">
                    <div class="container">
                        <h1 class="h3 fw-bold mb-2 pb-1">{{ room.title }}</h1>
                        <p class="mb-0">
                            <span class="me-1">By {{ room.teacher }}</span> •
                            <span class="ms-1">Sec: {{ room.section }}</span>
                        </p>
                    </div>
                </div>

                <!-- Navigation Tab -->
                <div class="border-bottom mb-5">
                    <div class="container">
                        <ul class="nav nav-tabs flex-nowrap overflow-x-auto overflow-y-hidden">

                            <!-- Class Stream -->
                            <li class="nav-item text-nowrap">
                                <Link class="nav-link" :class="{'active': active === 'stream'}"
                                    :href="route('dashboard.classroom.streams.index', room.code)">
                                <i class="fas fa-stream me-2"></i>Stream
                                </Link>
                            </li>

                            <!-- Class Work -->
                            <li class="nav-item text-nowrap">
                                <div class="dropdown">
                                    <a role="button" class="nav-link dropdown-toggle"
                                        :class="{ 'active': active === 'assignment' }" data-bs-toggle="dropdown"
                                        data-bs-popper-config='{"strategy":"fixed"}'>
                                        <i class="fas fa-book-open-reader me-2"></i>Assignment
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li v-if="hasPermissions('assignment.create')">
                                            <Link class="dropdown-item"
                                                :href="route('dashboard.classroom.assignments.create', room.code)"
                                                :class="{ active: route().current('dashboard.classroom.assignments.create') }">
                                            New Assignment
                                            </Link>
                                        </li>
                                        <li>
                                            <Link class="dropdown-item"
                                                :href="route('dashboard.classroom.assignments.index', room.code)"
                                                :class="{ active: route().current('dashboard.classroom.assignments.index') }">
                                            All Assignments
                                            </Link>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Participants -->
                            <li class="nav-item text-nowrap">
                                <div class="dropdown">
                                    <a role="button" class="nav-link dropdown-toggle"
                                        :class="{ 'active': active === 'people' }" data-bs-toggle="dropdown"
                                        data-bs-popper-config='{"strategy":"fixed"}'>
                                        <i class="fas fa-users me-2"></i>People
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <Link class="dropdown-item"
                                                :href="route('dashboard.classroom.people.index', room.code)"
                                                :class="{ active: route().current('dashboard.classroom.people.index') }">
                                            All Participants
                                            </Link>
                                        </li>
                                        <li v-if="hasPermissions('people.view_request')">
                                            <Link class="dropdown-item"
                                                :href="route('dashboard.classroom.people.requests', room.code)"
                                                :class="{ active: route().current('dashboard.classroom.people.requests') }">
                                            Joining Request
                                            </Link>
                                        </li>
                                        <li v-if="room.role === 'teacher'">
                                            <Link class="dropdown-item"
                                                :href="route('dashboard.classroom.people.permissions.index', room.code)"
                                                :class="{ active: route().current('dashboard.classroom.people.permissions.index') }">
                                            Access Permissions
                                            </Link>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Online Class -->
                            <li class="nav-item text-nowrap">
                                <div class="dropdown" data-bs-container="body">
                                    <a role="button" class="nav-link dropdown-toggle"
                                        :class="{ 'active': active === 'class' }" data-bs-toggle="dropdown"
                                        data-bs-popper-config='{"strategy":"fixed"}'>
                                        <i class="fas fa-chalkboard-user me-2"></i>Class
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li v-if="hasPermissions('class.create')">
                                            <Link class="dropdown-item"
                                                :href="route('dashboard.classroom.online-classes.create', room.code)"
                                                :class="{ active: route().current('dashboard.classroom.online-classes.create') }">
                                            Create Class
                                            </Link>
                                        </li>
                                        <li>
                                            <Link class="dropdown-item"
                                                :href="route('dashboard.classroom.online-classes.index', room.code)"
                                                :class="{ active: route().current('dashboard.classroom.online-classes.index')}">
                                            All Classes
                                            </Link>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Online Exam -->
                            <li class="nav-item text-nowrap">
                                <div class="dropdown">
                                    <a role="button" class="nav-link dropdown-toggle"
                                        :class="{ 'active': active === 'exam' }" data-bs-toggle="dropdown"
                                        data-bs-popper-config='{"strategy":"fixed"}'>
                                        <i class="fas fa-newspaper me-2"></i>Exam
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li v-if="hasPermissions('exam.create')">
                                            <Link class="dropdown-item"
                                                :href="route('dashboard.classroom.exams.create', room.code)"
                                                :class="{ active: route().current('dashboard.classroom.exams.create') }">
                                            Create Exam
                                            </Link>
                                        </li>
                                        <li>
                                            <Link class="dropdown-item"
                                                :href="route('dashboard.classroom.exams.index', room.code)"
                                                :class="{ active: route().current('dashboard.classroom.exams.index') }">
                                            All Exams
                                            </Link>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Assistant -->
                            <li class="nav-item text-nowrap">
                                <Link class="nav-link" :class="{ 'active': active === 'assistant' }"
                                    :href="route('dashboard.classroom.assistant.index', room.code)">
                                <i class="fas fa-user-graduate me-2"></i>Ask Dr. Smith
                                </Link>
                            </li>

                            <!-- Classroom Settings -->
                            <li v-if="hasPermissions('settings.update')" class="nav-item text-nowrap">
                                <Link class="nav-link" :class="{ 'active': active === 'settings' }"
                                    :href="route('dashboard.classroom.settings.index', room.code)">
                                <i class="fas fa-gear me-2"></i>Settings
                                </Link>
                            </li>

                            <!-- Classroom Settings -->
                            <li class="nav-item text-nowrap">
                                <a role="button" class="nav-link" data-bs-toggle="modal" data-bs-target="#shareModal">
                                    <i class="fas fa-share-nodes me-2"></i>Share
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </template>
            <slot />
        </div>

        <!-- Modal -->
        <div class="modal" id="shareModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Share Classroom</h5>
                        <button type="button" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Classroom Code</p>

                        <div class="d-flex align-items-center justify-content-between bg-light p-2 mb-4">
                            <span class="fs-4">{{ room.code }}</span>
                            <button class="btn btn-sm btn-light fs-6 py-1 px-2" @click="copy(room.code)">
                                <i class="far fa-copy"></i>
                            </button>
                        </div>

                        <p>Or copy link</p>
                        <div class="d-flex align-items-center justify-content-between bg-light p-2">
                            <span>{{ route('dashboard.getstart.join.link', room.code) }}</span>
                            <button class="btn btn-sm btn-light fs-6 py-1 px-2" @click="copy(route('dashboard.getstart.join.link', room.code))">
                                <i class="far fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<style scoped>
@import url("@/css/dashboard/classroom-layout.css");
</style>
