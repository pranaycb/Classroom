<script setup>
defineProps({
    meta: {
        type: Object,
        required: true
    },
    attr: {
        type: String,
        default: 'records'
    },
    only: {
        type: Array,
        default: [],
    }
});
</script>

<template>
    <div class="d-flex flex-wrap gap-3 align-items-center justify-content-center justify-content-sm-between">

        <span>Showing {{ meta.from }} - {{ meta.to }} from {{ meta.total }}
            {{ attr }}</span>

        <ul class="pagination my-0" v-if="meta.total > meta.per_page">

            <li class="page-item" v-for="link in meta.links">

                <Link v-if="link.url && !link.active" :href="link.url" class="page-link"
                    :class="link.active && 'active'" preserve-scroll preserve-state :only="only">
                    <span aria-hidden="true" v-html="link.label"></span>
                </Link>

                <a v-else class="page-link" :class="link.active && 'active'" role="button"
                    v-html="link.label"></a>
            </li>
        </ul>
    </div>
</template>

<style scoped>
@import url("@/css/pagination.css");
</style>
