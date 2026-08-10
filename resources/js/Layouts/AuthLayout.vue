<script setup>
defineProps({
    title: String,
    className: {
        type: String,
        default: 'col-xl-4 col-lg-6 col-md-8'
    }
});

</script>
<template>

    <Head :title />
    <main>
        <div class="container">
            <section
                class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div :class="className">
                            <div class="d-flex justify-content-center text-center mb-3">
                                <i class="fas fa-graduation-cap text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <slot />
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</template>
