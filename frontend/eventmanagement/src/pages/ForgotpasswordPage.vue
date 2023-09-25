<template>
    <main class="auth-wrapper">
        <form class="auth-form" @submit.prevent="handleSubmit" v-if="!apiresponse.success">
            <h1>
                <span>LOGO</span>
            </h1>
            <h2 class="h3 mb-4 fw-normal">Forgot Password</h2>
            <div class="form-floating mb-2">
                <input type="email" class="form-control" :class="{ 'is-invalid': errors.email && errors.email[0] }" id="email" v-model="form.email" />
                <label for="email">Email</label>
                <div class="invalid-feedback" v-if="errors.email && errors.email[0]">
                    {{ errors.email && errors.email[0] }}
                </div>
            </div>

            <button class="w-100 btn btn-lg btn-primary" type="submit">Submit</button>
        </form>
        <div class="alert alert-success" role="alert" v-if="apiresponse.success">
                Please Check your email
        </div>
    </main>
</template>

<script setup>
import { reactive,ref } from "vue";
import { useRouter } from "vue-router";
import {forgotpassword} from "../http/auth-api";

const errors   = ref({});
const apiresponse   = ref({});
const router = useRouter()

const form = reactive({
    email: '' 
})

const handleSubmit = async () => {
    try {
        
        errors.value = {};
        const { data } = await forgotpassword(form);
        apiresponse.value = data;
        console.log(data);


    } catch (error) {
      if (error.response && error.response.status === 422) {
        errors.value = error.response.data.errors;
      }
    }
  };

</script>

<style scoped>
.auth-wrapper {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    min-height: 60vh;
    margin-top: 2rem;
}

.auth-form {
    width: 400px;
}
</style>