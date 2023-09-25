import axios from "axios";
axios.defaults.withCredentials = true;

const api = axios.create({
  baseURL: import.meta.env.VITE_BASE_URL,
  /* headers: {
    'Content-Type': 'application/json', 
  }, */
});

api.interceptors.request.use(
  (config) => {

    const token    = localStorage.getItem('authtoken');
    const timeZone = localStorage.getItem('timeZone');
    
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`;
    }
    if (timeZone) {
      config.headers['timeZone'] = timeZone;
    }
    else{
      config.headers['timeZone'] = Intl.DateTimeFormat().resolvedOptions().timeZone;
    }


    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

export default api;
