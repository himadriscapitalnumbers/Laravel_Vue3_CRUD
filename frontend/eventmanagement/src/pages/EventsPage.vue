<template>
    <main class="px-4 py-5 my-5 text-center" style="min-height: 50vh;">
        <h1 class="display-5 fw-bold mt-4">My Events</h1>
        <div class="col-lg-10 mx-auto">

            <div class="alert alert-danger" role="alert" v-if="errors == 500">
                Internal Server Error!
            </div>
            <div class="alert alert-danger" role="alert" v-if="errors == 401">
                Action Unauthorized!
            </div>
            <div class="alert alert-success" role="alert" v-if="isEventDeleted == 1">
                Event Deleted Successfully
            </div>

        <div class="tableview">
        <div class="mb-2 d-flex justify-content-between align-items-center">
            
            <div class="col-md-3">
                <div class="position-relative">
                <span class="position-absolute search"><i class="fa fa-search"></i></span>
                <input v-model="searchQuery" @input="debouncedSearch" class="form-control w-100" placeholder="Search by name">
                </div>
            </div>
            <div class="col-md-3">
            </div>
            <div class="col-md-2">
                
            </div>

            <div class="col-md-4">
                
                <div class="row">
                <div class="col-md-2"> 
                
                </div>
                <div class="col-md-2"> 
                <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" :class="toggleClass" @click.prevent="toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                               Export
                            </a>
                            <ul class="dropdown-menu" :class="toggleClass">
                                <li><a href="#" class="dropdown-item" @click.prevent="exportToCsv">CSV</a></li>
                                <li><a href="#" class="dropdown-item" @click.prevent="exportToIcs">ICS</a></li>
                            </ul>
                </li>
                </ul>
                </div>
                <div class="col-md-4">
                    <router-link :to="{ name: 'importevent' }" class="btn btn-primary">Import Events</router-link> 
                </div>

                <div class="col-md-4"> 
                    <router-link :to="{ name: 'addnewevent' }" class="btn btn-primary">Add New Event</router-link> 
                </div>


                </div>

            </div>
        
          </div>  

          <div class="table-responsive"> 
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                    <th scope="col"></th>
                    <th scope="col">Name</th>
                    <th scope="col">Start Date Time</th>
                    <th scope="col">End Date Time</th>
                    <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="event in myevents" :key="event.id" class="event-row">
                    <td></td>
                    <td class="text-start">{{ event.name }}</td>
                    <td>{{ event.started_at_human }}</td>
                    <td>{{ event.ended_at_human }}</td>
                    <td>
                        <button class="btn btn-sm btn-info" @click="showModal(event)">Edit</button>
                        <button class="btn btn-sm btn-danger ms-2" @click="removeEventCall(event)">Delete</button>
                    </td>
                    </tr>
                    
                </tbody>
            </table>
          </div>
        </div>  



<!-- The Modal -->
<div class="modal" tabindex="-1" id="editEventModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Event Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="event-form" @submit.prevent="handleSubmit">
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
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
        </div>
    </main>
    
</template>


<script setup>
import { onMounted, ref, reactive, computed } from "vue";
import {
  myEvents,
  searchEvents,
  showEvent,
  removeEvent,
  updateEvent,
  exportCsvEvents,
  exportIcsEvents
} from "../http/event-api";


const myevents = ref([])
const errors   = ref({});
const isEventDeleted = ref({});

const form = reactive({
    name: '',
    description: '',
    started_at:'',
    ended_at:'',
    id:'',
})

let myModal

onMounted(async () => {
    
    showMyEvents()

    myModal = new bootstrap.Modal('#editEventModal',{backdrop:'static',keyboard:false})

})

const showMyEvents = async () =>{

    const { data } = await myEvents()
    myevents.value = data.data
}

let searchQuery   = null;
let debounceTimer = null;
const debouncedSearch = async () =>{

    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }

    debounceTimer = setTimeout( async() => {
       
        if(searchQuery.length == 0 || searchQuery.length > 2)
        {
            const { data } = await searchEvents(searchQuery)
            myevents.value = data.data
        }

    }, 500);

}

const showModal = async (event) => {
    
    try{

        const { data } = await showEvent(event.id);
        
        form.name = data.data.name
        form.started_at = data.data.started_at
        form.ended_at = data.data.ended_at
        form.description = data.data.description
        form.id = data.data.id
        myModal.show();

    } catch( error )
    {
        errors.value = error.response.status
        setTimeout(function () {
            errors.value = {}    
        }, 5000);
        
    }

}


const removeEventCall = async (event) => {
    if (confirm("Are you sure?")) {
     
        try{    
            const { data } = await removeEvent(event.id);
            isEventDeleted.value = data.deleted;
            
            if(data.deleted == 1)
            {
                const index = myevents.value.findIndex((item) => item.id === event.id);
                myevents.value.splice(index, 1);
            
                setTimeout(function () {
                    isEventDeleted.value = {}    
                }, 2000);

            }

        } catch (error) 
        {
            errors.value = error.response.status
            
            setTimeout(function () {
               errors.value = {}    
            }, 5000);
        }
        
    }
}

const handleSubmit = async () => {
      
        try{   

           const { data } = await updateEvent(form.id,form);

           myModal.hide();
           showMyEvents()

        } catch (error) 
        {
            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
            }
            if (error.response && error.response.status === 500) {
                errors.value = error.response.status;
            }
        }

}

const isOpen = ref(false)
const toggle = () => isOpen.value = !isOpen.value
const toggleClass = computed(() => isOpen.value === true ? 'show' : '')

const exportToCsv = async () =>{

    const { data } = await exportCsvEvents();

    var fileURL = window.URL.createObjectURL(new Blob([data]));
    var fileLink = document.createElement('a');
    fileLink.href = fileURL;
    fileLink.setAttribute('download', 'Events-exported.csv');
    document.body.appendChild(fileLink);
    fileLink.click(); 
}

const exportToIcs = async () =>{

    const { data } = await exportIcsEvents();

    var fileURL = window.URL.createObjectURL(new Blob([data]));
    var fileLink = document.createElement('a');
    fileLink.href = fileURL;
    fileLink.setAttribute('download', 'Events-exported.ics');
    document.body.appendChild(fileLink);
    fileLink.click(); 
}
</script>

