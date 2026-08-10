<script setup>
import Pagination from '../Pagination.vue';
import { ref, onMounted, computed } from 'vue';
import TextArea from '@/Components/TextArea.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    slug: String,
    target: Number,
    comments: Object,
    only: Array,
});

const modalRef = ref(null)
let modalInstance = null;
const room = usePage().props.room;

const comments = computed(() => {
    return props.comments.data.length > 0 ? props.comments : null;
});

onMounted(() => {
    if (modalRef.value) {
        modalInstance = new bootstrap.Modal(modalRef.value)
    }
});

const commentForm = useForm({
    slug: props.slug,
    id: props.target,
    comment: '',
});

/**
 * Post comment
 */
const postComment = () => {
    commentForm.post(route('dashboard.classroom.comments.store', room.code), {
        preserveScroll: true,
        preserveState: true,
        only: props.only,
        onSuccess: (e) => commentForm.reset(),
    });
};

const replyForm = useForm({
    parent: null,
    reply: '',
})

/**
 * Send reply
 */
const sendReply = (commentId, parentId = null) => {

    if (parentId) replyForm.parent = parentId

    replyForm.post(route('dashboard.classroom.comments.reply.store', [room.code, commentId]), {
        preserveScroll: true,
        preserveState: true,
        only: props.only,
        onSuccess: () => replyForm.reset(),
    });
}
</script>

<template>
    <a class="toggle text-body" href="javascript:void(0)" @click="modalInstance.show()">
        <i class="far fa-comment-dots"></i>
        Comment ({{ comments?.meta.total ?? 0 }})
    </a>

    <div class="modal" :id="`announce-${target}`" tabindex="-1" ref="modalRef">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 fw-bold">Comments ({{ comments?.meta.total ?? 0 }})</h5>
                    <button type="button" class="btn-close fs-1" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="postComment">
                        <div class="mb-4">
                            <TextArea v-model="commentForm.comment" :error="commentForm.errors.comment"
                                placeholder="Write your comment here..." />
                        </div>
                        <div class="d-flex justify-content-end">
                            <PrimaryButton text="Comment" :showLoader="commentForm.processing"
                                :disabled="commentForm.processing" />
                        </div>
                    </form>

                    <hr style="border-color: var(--border-color);" />

                    <!-- Show all comments -->
                    <template v-if="comments">

                        <div v-for="comment in comments.data" :key="comment.id" class="comment-box mb-5 ps-2">
                            <div class="d-flex">
                                <img class="avatar" :src="comment.avatar" />
                                <div class="ms-3">
                                    <p class="fw-bold text-body fs-6 mb-2">{{ comment.by }}</p>
                                    <p class="mb-3">{{ comment.comment }}</p>

                                    <div class="comment-actions d-flex flex-wrap align-items-center gap-3">
                                        <a class="text-muted">
                                            <i class="fas fa-clock-rotate-left"></i>
                                            {{ comment.date }}
                                        </a>

                                        <!-- Reply dropdown -->
                                        <div>
                                            <a class="text-nowrap" data-bs-toggle="dropdown"
                                                data-bs-auto-close="outside">
                                                <i class="fas fa-reply"></i> Reply
                                            </a>

                                            <form class="dropdown-menu" @submit.prevent="sendReply(comment.id)"
                                                style="max-width: 380px; width: 100%;">
                                                <h6 class="dropdown-header">
                                                    Replying to <span class="fw-medium text-body">@{{ comment.by
                                                        }}</span>
                                                </h6>
                                                <div class="dropdown-item bg-white text-body mb-2">
                                                    <TextArea v-model="replyForm.reply" :error="replyForm.errors.reply"
                                                        placeholder="Write your reply here..." />
                                                </div>
                                                <div class="dropdown-item bg-white text-body">
                                                    <PrimaryButton text="Send Reply" :showLoader="replyForm.processing"
                                                        :disabled="replyForm.processing" />
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Delete -->
                                        <div v-if="comment.delete">
                                            <a class="text-nowrap text-danger" data-bs-toggle="dropdown"
                                                data-bs-auto-close="inside">
                                                <i class="fas fa-trash-can"></i> Delete
                                            </a>

                                            <div class="dropdown-menu">
                                                <h6 class="dropdown-header">Delete Comment</h6>
                                                <div class="dropdown-item bg-white text-body mb-2">
                                                    Are you sure you want to delete this comment?
                                                </div>
                                                <div class="dropdown-item bg-white text-body d-flex gap-3">
                                                    <Link
                                                        :href="route('dashboard.classroom.comments.delete', [room.code, comment.id])"
                                                        class="btn btn-danger text-white" as="button" method="delete"
                                                        preserve-scroll preserve-state :only="only">
                                                    Delete
                                                    </Link>
                                                    <button class="btn btn-outline--light">Cancel</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Show/Hide Replies toggle -->
                                        <a v-if="comment.replies.length > 0" class="toggle" href="javascript:void(0)"
                                            data-bs-toggle="collapse" :data-bs-target="`#comment-${comment.id}`"
                                            aria-expanded="false">
                                            <i class="ri-arrow-down-s-line arrow"></i>
                                            Replies ({{ comment.replies.length }})
                                        </a>
                                    </div>

                                    <!-- Replies Section with Bootstrap Collapse -->
                                    <div v-if="comment.replies.length > 0" :id="`comment-${comment.id}`"
                                        class="collapse">
                                        <div class="reply-section mt-4">
                                            <div v-for="reply in comment.replies" :key="reply.id"
                                                class="comment-box mb-3">
                                                <div class="d-flex">
                                                    <img class="avatar" :src="reply.avatar" />
                                                    <div class="ms-3">
                                                        <p class="fw-bold text-body mb-2">{{ reply.by }}</p>
                                                        <p class="mb-2">
                                                            <span class="text-primary fw-medium">@{{ reply.mentioned
                                                                }}</span>
                                                            {{ reply.comment }}
                                                        </p>
                                                        <div
                                                            class="comment-actions d-flex flex-wrap align-items-center gap-3">
                                                            <a class="text-muted">
                                                                <i class="fas fa-clock-rotate-left"></i>
                                                                {{ reply.date }}
                                                            </a>

                                                            <div v-if="reply.delete">
                                                                <a class="text-nowrap text-danger"
                                                                    data-bs-toggle="dropdown"
                                                                    data-bs-auto-close="inside">
                                                                    <i class="fas fa-trash-can"></i> Delete
                                                                </a>

                                                                <div class="dropdown-menu">
                                                                    <h6 class="dropdown-header">Delete Reply</h6>
                                                                    <div class="dropdown-item bg-white text-body mb-2">
                                                                        Are you sure you want to delete this reply?
                                                                    </div>
                                                                    <div
                                                                        class="dropdown-item bg-white text-body d-flex gap-3">
                                                                        <Link
                                                                            :href="route('dashboard.classroom.comments.reply.delete', [room.code, comment.id, reply.id])"
                                                                            class="btn btn-danger text-white"
                                                                            as="button" method="delete" preserve-scroll
                                                                            preserve-state :only="only">
                                                                        Delete
                                                                        </Link>
                                                                        <button
                                                                            class="btn btn-outline--light">Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <a class="text-nowrap" data-bs-toggle="dropdown"
                                                                data-bs-auto-close="outside" aria-expanded="false">
                                                                <i class="fas fa-reply"></i> Reply
                                                            </a>

                                                            <form class="dropdown-menu"
                                                                @submit.prevent="sendReply(comment.id, reply.id)"
                                                                style="max-width: 380px; width: 100%;">
                                                                <h6 class="dropdown-header">
                                                                    Replying to
                                                                    <span class="fw-medium text-body">
                                                                        @{{ reply.by }}
                                                                    </span>
                                                                </h6>
                                                                <div class="dropdown-item bg-white text-body mb-2">
                                                                    <TextArea :error="replyForm.errors.reply"
                                                                        v-model="replyForm.reply"
                                                                        placeholder="Write your reply here..." />
                                                                </div>
                                                                <div class="dropdown-item bg-white text-body">
                                                                    <PrimaryButton text="Send Reply"
                                                                        :showLoader="replyForm.processing"
                                                                        :disabled="replyForm.processing" />
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <Pagination :meta="comments.meta" attr="comments" class="flex-column-reverse small"
                            :only="only" />
                    </template>
                    <div v-else class="mt-4 mb-2 text-center">
                        <span class="text-muted">No comments found</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.comment-actions a {
    color: var(--text-muted);
    font-size: 0.8rem;
    cursor: pointer;
    text-wrap: nowrap;
    transition: 0.3s all;
}

.comment-actions a:hover {
    color: var(--theme-color);
}

.toggle i.arrow {
    font-size: 0.55rem !important;
    margin-left: 5px;
    vertical-align: middle;
    transition: transform 0.3s ease;
}

.toggle[aria-expanded="true"] i.arrow {
    transform: rotate(180deg);
}
</style>
