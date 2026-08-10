<script setup>
import { router } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';
import { useToast } from '@/Compose/useToast';
import ClassroomLayout from '@/Layouts/ClassroomLayout.vue';

const { showToast } = useToast();

const props = defineProps({
    room: Object,
    data: Object,
});

let api;

const loadJitsiScript = () => {
    return new Promise((resolve, reject) => {
        if (window.JitsiMeetExternalAPI) return resolve();
        const script = document.createElement('script');
        script.src = 'https://8x8.vc/vpaas-magic-cookie-fb428604c9aa4245ab6cafc2506b2f34/external_api.js';
        script.async = true;
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('Failed to load Jitsi API'));
        document.body.appendChild(script);
    });
}

onMounted(async () => {
    await loadJitsiScript();
    api = new JitsiMeetExternalAPI('8x8.vc', {
        roomName: `${props.data.appId}/${props.data.room}`,
        parentNode: document.querySelector('.conf-wrapper'),
        jwt: props.data.token,
        invitees: false,
        width: '100%',
        height: '100%',
        configOverwrite: {
            toolbarButtons: [
               'camera',
               'chat',
               'desktop',
               'download',
               'embedmeeting',
               'etherpad',
               'feedback',
               'filmstrip',
               'fullscreen',
               'hangup',
               'help',
               'highlight',
               'linktosalesforce',
               'microphone',
               'noisesuppression',
               'participants-pane',
               'profile',
               'raisehand',
               'security',
               'select-background',
               'settings',
               'shortcuts',
               'tileview',
               'toggle-camera',
               'videoquality',
               'whiteboard',
            ],
        },
    });

    // redirect to details page
    api.addEventListener('videoConferenceLeft', () => {
        api.dispose();
        router.visit(route('dashboard.classroom.online-classes.show', [props.room.code, props.data.id]));
    });
});


onUnmounted(() => api && api.dispose());
</script>

<template>
    <ClassroomLayout active="class" :show-menu="false">
        <div class="conf-wrapper"></div>
    </ClassroomLayout>
</template>

<style scoped>
.conf-wrapper {
    position: relative;
    width: 100%;
    height: calc(100vh - 60px);
}
</style>
