<template>
    <main class="auth-wrapper">
        <form class="auth-form" @submit.prevent="handleSubmit" v-if="istokenvalid.valid">
            <h1>
                <span>LOGO</span>
            </h1>
            <h2 class="h3 mb-4 fw-normal">Reset Password</h2>
            <div class="form-floating mb-2">
                <input type="email" class="form-control" :class="{ 'is-invalid': errors.email && errors.email[0] }" id="email" v-model="form.email" />
                <label for="email">Confirm Your Email</label>
                <div class="invalid-feedback" v-if="errors.email && errors.email[0]">
                    {{ errors.email && errors.email[0] }}
                </div>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" :class="{ 'is-invalid': errors.password && errors.password[0] }" id="password" v-model="form.password" placeholder="Password" />
                <label for="password">Password</label>
                <div class="invalid-feedback" v-if="errors.password && errors.password[0]">
                    {{ errors.password && errors.password[0] }}
                </div>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password_confirmation" v-model="form.password_confirmation"
                    placeholder="Password Confirmation" />
                <label for="password_confirmation">Password Confirmation</label>
            </div>

            <button class="w-100 btn btn-lg btn-primary" type="submit">Submit</button>
        </form>

        <div class="alert alert-success" role="alert" v-if="apiresponse.success">
                Your Password Resets Successfully. <router-link :to="{ name: 'login' }">Login</router-link>
        </div>

    </main>
</template>

<script setup>
import { onMounted,reactive,ref } from "vue";
import { useRouter } from "vue-router";
import {ispasswordtokenvalid, resetpassword} from "../http/auth-api";

const errors   = ref({});
const apiresponse   = ref({});
const istokenvalid   = ref({});
const router = useRouter()

const form = reactive({
    email: '',
    password: '',
    password_confirmation: '',
    token: ''
})

onMounted(async () => {

    form.token = window.location.pathname.split("/").pop();
    validatePasswordToken()

})

const validatePasswordToken = async () => {
    try {
        
        const { data } = await ispasswordtokenvalid(form);
        istokenvalid.value = data;

    } catch (error) {
      
    }
  };


const handleSubmit = async () => {
    try {
        
        errors.value = {};
        const { data } = await resetpassword(form);
        apiresponse.value = data;
        istokenvalid.value ={};
        
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