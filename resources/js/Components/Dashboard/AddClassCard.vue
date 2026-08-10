<template>
    <Link class="col" :href="route('dashboard.getstart.index')">
    <div class="card h-100 add-class-card">
        <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-4">
            <div class="add-class-icon">
                <i class="fas fa-plus"></i>
            </div>
            <h3 class="h6 fw-semibold">Join or create class</h3>
            <p class="text-muted small mb-0">Get started with a new class</p>
        </div>
    </div>
    </Link>
</template>

<style scoped>
@import url("@/css/dashboard/add-class.css");
</style>
