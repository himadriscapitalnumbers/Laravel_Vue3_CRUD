<template>
    <main class="event-wrapper">
        <form class="event-form" @submit.prevent="handleSubmit">
            <h1>
                <strong></strong>
            </h1>
            <h2 class="h3 mb-4 fw-normal">Add New Event</h2>
            <div class="alert alert-danger" role="alert" v-if="errors == 500">
                Internal Server Error!
            </div>
            <div class="alert alert-success" role="alert" v-if="isEventCreated.id > 0">
                Event Created Successfully
            </div>

            <div class="form-floating mb-2">
                <input type="text" class="form-control" :class="{ 'is-invalid': errors.name && errors.name[0] }" id="name" v-model="form.name"/>
                <label for="name">Name</label>
                <div class="invalid-feedback text-start" v-if="errors.name && errors.name[0]">
                    {{ errors.name && errors.name[0] }}
                </div>
            </div>

            <div class="form-floating mb-2">
                <input type="datetime-local" class="form-control" :class="{ 'is-invalid': errors.started_at && errors.started_at[0] }" id="started_at" v-model="form.started_at"/>
                <label for="name">Start Date Time</label>
                <div class="invalid-feedback text-start" v-if="errors.started_at && errors.started_at[0]">
                    {{ errors.started_at && errors.started_at[0] }}
                </div>
            </div>
            <div class="form-floating mb-2">
                <input type="datetime-local" class="form-control" :class="{ 'is-invalid': errors.ended_at && errors.ended_at[0] }" id="ended_at" v-model="form.ended_at"/>
                <label for="name">End Date Time</label>
                <div class="invalid-feedback text-start" v-if="errors.ended_at && errors.ended_at[0]">
                    {{ errors.ended_at && errors.ended_at[0] }}
                </div>
            </div>
            
            <div class="form-floating mb-2">
                <textarea class="form-control" :class="{ 'is-invalid': errors.description && errors.description[0] }" id="description" v-model="form.description" ></textarea>
                <label for="name">Event Description</label>
                <div class="invalid-feedback text-start" v-if="errors.description && errors.description[0]">
                    {{ errors.description && errors.description[0] }}
                </div>
            </div>
            
            <button class="w-100 btn btn-lg btn-primary" type="submit">Save Event</button>



        </form>
    </main>
</template>

<script setup>
import { onMounted, reactive } from "vue";
import { storeToRefs } from "pinia";
import { useRouter } from "vue-router";
import { useEventStore } from "../stores/event";


const router = useRouter()
const store = useEventStore()
const { errors, isEventCreated } = storeToRefs(store)

const { handleCreateevent, handleEeventCreated } = store

const form = reactive({
    name: '',
    description: '',
    started_at:'',
    ended_at:'',
})

onMounted(async () => {
    handleEeventCreated() 
})

const handleSubmit = async () => {
   await handleCreateevent(form)
   
   if(isEventCreated.value)
   {
    setTimeout(function () {
        router.push({ name: 'events' })        
    }, 2000);
   }
   


}
</script>


<style scoped>
.event-wrapper {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    min-height: 60vh;
    margin-top: 2rem;
}

.event-form {
    width: 600px;
}
</style>