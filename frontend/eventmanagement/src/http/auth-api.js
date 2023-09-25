import api from "./api";

export const csrfCookie = () => api.get("/sanctum/csrf-cookie");

export const login = (credentials) => api.post("/auth/login", credentials);

export const register = (user) => api.post("/auth/register", user);

export const logout = () => api.post("/auth/logout");

export const getUser = () => api.get("/user");

export const forgotpassword = (email) => api.post("/auth/forgotpassword", email);

export const ispasswordtokenvalid = (token) => api.post("/auth/validate-password-token", token);

export const resetpassword = (data) => api.post('/auth/reset-password', data);
