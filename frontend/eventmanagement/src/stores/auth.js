import { defineStore } from "pinia";
import { computed, ref } from "vue";
import { login, register, logout, getUser } from "../http/auth-api";

export const useAuthStore = defineStore("authStore", () => {
  
  const user = ref(null);
  const errors = ref({});

  const isLoggedIn = computed(() => !!user.value);

  const fetchUser = async () => {
    try {
      const token = localStorage.getItem('authtoken');
      if (token) {
        const { data } = await getUser();
        user.value = data;
      }else{
        user.value = null;
      }
    } catch (error) {
      user.value = null;
    }
  };

  const handleLogin = async (credentials) => {
    
    try {
      const { data } = await login(credentials); 
      localStorage.setItem('authtoken', data.token);
      localStorage.setItem('timeZone', Intl.DateTimeFormat().resolvedOptions().timeZone);
      await fetchUser();
      errors.value = {};
    } catch (error) {
      if (error.response && error.response.status === 422) {
        errors.value = error.response.data.errors;
      }
    }
  };

  const handleRegister = async (newUser) => {
    try {
      await register(newUser);
      await handleLogin({
        email: newUser.email,
        password: newUser.password,
      });
    } catch (error) {
      if (error.response && error.response.status === 422) {
        errors.value = error.response.data.errors;
      }
    }
  };

  const handleLogout = async () => {
    const { data } = await logout();
    if(data.logout)
    {
      localStorage.setItem('authtoken', data.token);
      user.value = null;
    }
    
  };

  return {
    user,
    errors,
    isLoggedIn,
    fetchUser,
    handleLogin,
    handleRegister,
    handleLogout,
  };
});
