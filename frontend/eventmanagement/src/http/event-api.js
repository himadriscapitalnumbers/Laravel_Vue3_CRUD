import api from "./api";

export const myEvents = () => api.get("/events");
export const searchEvents = (s) => api.get(`/events?s=${s}`);
export const createEvent = (newdata) => api.post('/events', newdata);
export const removeEvent = (id) => api.delete(`/events/${id}`);
export const showEvent = (id) => api.get(`/events/${id}`);
export const updateEvent = (id,updateddata) => api.put(`/events/${id}`, updateddata);
export const exportCsvEvents = () => api.get("/events-exporttocsv");
export const exportIcsEvents = () => api.get("/events-exporttoics");
export const importEvents = (data) => api.post("/events-import", data);