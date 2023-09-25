<template>
    <footer class="py-3 my-4">
        <ul class="nav justify-content-center border-bottom pb-3 mb-3">
            <li class="nav-item">
                <router-link :to="{ name: 'about' }" class="nav-link px-2 text-muted">About Us</router-link>
            </li>
            <li class="nav-item">
                <router-link :to="{ name: 'privacy' }" class="nav-link px-2 text-muted">Privacy Policy</router-link>
            </li>
        </ul>
        <p class="text-center text-muted">&copy; Company Name</p>
    </footer>
</template>