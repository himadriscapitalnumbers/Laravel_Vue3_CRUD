import { ref, computed } from "vue";
import { defineStore } from "pinia";
import {
  myEvents,
  createEvent
} from "../http/event-api";

export const useEventStore = defineStore("eventStore", () => {
  
  const events = ref([]);
  const errors = ref({});
  const newEventData = ref({});

  const isEventCreated = computed(() => !!newEventData.value);

  const fetchAllEvents = async () => {
    const { data } = await myEvents();
    events.value = data.data;
  };
  
  const handleCreateevent = async (newEvent) => {
    try {
      errors.value = {};
      const { data } = await createEvent(newEvent);
      newEventData.value = data.data;
    } catch (error) {
      if (error.response && error.response.status === 422) {
        errors.value = error.response.data.errors;
      }
      if (error.response && error.response.status === 500) {
        errors.value = error.response.status;
      }
    }
  };

  const handleEeventCreated = () => {
    newEventData.value = null;
  };

  return {
    events,
    errors,
    newEventData,
    isEventCreated,
    fetchAllEvents,
    handleCreateevent,
    handleEeventCreated
  };
});
