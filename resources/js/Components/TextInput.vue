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
})

const model = defineModel({
    required: true,
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
    <input ref="input" v-model="model" class="form-control" :class="error && 'is-invalid'" v-bind="$attrs"
        autocomplete />
    <InputError class="invalid-feedback" :class="errorClass" :message="error" />
</template>
