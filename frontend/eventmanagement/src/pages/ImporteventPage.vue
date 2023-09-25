<template>
    <main class="event-wrapper">
        <form id="importform" class="event-form" @submit.prevent="submitFile" enctype="multipart/form-data">
            <h1>
                <strong></strong>
            </h1>
            <h2 class="h3 mb-4 fw-normal">Import Events From CSV or ICS File</h2>
            <div class="alert alert-danger" role="alert" v-if="errors == 500">
                Internal Server Error!
            </div>
            <div class="alert alert-success" role="alert" v-if="fileimported.success > 0">
                {{ fileimported.success}} Records Imported Successfully
            </div>

            <div class="form-floating mb-2">
                <input name="file" type="file" @change="uploadFile" class="form-control" :class="{ 'is-invalid': errors.file && errors.file[0] }" id="file"/>
                <div class="invalid-feedback text-start" v-if="errors.file && errors.file[0]">
                    {{ errors.file && errors.file[0] }}
                </div>
            </div>
            
            <button class="w-100 btn btn-lg btn-primary" type="submit">Start Import</button>



        </form>
    </main>
</template>

<script setup>
import { onMounted, computed, ref } from "vue";
import {
    importEvents
} from "../http/event-api";

import { useRouter } from "vue-router";

const errors    = ref({});
const fileimported = ref({});

const router = useRouter()



const file = ref(null);
/* const fileName = computed(() => file.value?.name);
const fileExtension = computed(() => fileName.value?.substr(fileName.value?.lastIndexOf(".") + 1));
const fileMimeType = computed(() => file.value?.type); */

const uploadFile = (event) => {
  file.value = event.target.files[0];
  errors.value = {};
};

const submitFile = async () => {
  const reader = new FileReader();
  reader.readAsDataURL(file.value);
  reader.onload = async () => {
    const formData = new FormData(document.getElementById('importform'));
    
    /* const encodedFile = reader.result.split(",")[1];
    const data = {
      file: encodedFile,
      fileName: fileName.value,
      fileExtension: fileExtension.value,
      fileMimeType: fileMimeType.value,
    }; */

    try {
        const response =  await importEvents(formData)
        fileimported.value = response.data;
        setTimeout(function () {
          router.push({ name: 'events' })        
        }, 2000);

    } catch (error) {
      
        if (error.response && error.response.status === 422) {
          errors.value = error.response.data.errors;
        }
        if (error.response && error.response.status === 500) {
          errors.value = error.response.status;
        }
    }
  };
};

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