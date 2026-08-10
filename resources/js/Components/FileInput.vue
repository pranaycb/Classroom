<script setup>
import { onMounted, ref } from "vue";
import InputError from "./InputError.vue";

defineProps({
    error: {
        type: String,
        default: null,
    },
    errorClass: {
        type: String,
        default: null,
    },
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute("autofocus")) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input type="file" ref="input" class="form-control" :class="error && 'is-invalid'" v-bind="$attrs" autocomplete />
    <InputError class="invalid-feedback" :class="errorClass" :message="error" />
</template>

<style scoped>
    .form-control[type="file"] {
        padding: 7px 10px 7px 20px;
    }
    input[type="file"]::file-selector-button {
        padding: 3px 10px;
        border-radius: 5px;
    }
</style>
