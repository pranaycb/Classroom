<template>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <Link class="navbar-brand" :href="route('index')">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="fas fa-graduation-cap me-2"></i>
                Classroom
            </h5>
            </Link>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#toggler"
                aria-controls="toggler">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="toggler">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#get-started">Get Started</a>
                    </li>
                    <li class="nav-item">
                        <Link class="nav-link" :href="route('login')">Login</Link>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<style scoped>
@import url('@/css/public/header.css');
</style>
