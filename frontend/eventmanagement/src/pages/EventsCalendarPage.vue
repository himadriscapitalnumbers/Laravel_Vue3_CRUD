<template>
    <main class="px-4 py-5 my-5 text-center" style="min-height: 50vh;">
        <h1 class="display-5 fw-bold mt-4">My Events</h1>
        <div class="col-lg-10 mx-auto">
            <Calendar></Calendar>
         </div>
    </main>
</template>


<script setup>
import Calendar from '../components/events/Calendar.vue'
</script>