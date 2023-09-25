<template>
    <FullCalendar :options='calendarOptions'>
            <template v-slot:eventContent='arg'>
                <i>{{ arg.event.title }}</i>
                <b v-bind:title="arg.event.start">{{ arg.timeText }}</b>
            </template>
    </FullCalendar>
    
</template>


<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import {
  myEvents
} from "../../http/event-api";


export default {
  components: {
    FullCalendar 
  },
  data: function() { 
    return {
      calendarOptions: {
        plugins: [dayGridPlugin,timeGridPlugin,interactionPlugin],
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        initialView: 'dayGridMonth',
        weekends: true,
        forceEventDuration : true,
        displayEventEnd : true,
        nextDayThreshold: '09:00:00',
        events: []
      }
    }
  },
  async mounted () {
    
    const { data } = await myEvents()
    let myevents = data.data
    
    for(var i=0;i<myevents.length;i++)
    {
        var obj = {id:myevents[i].id, title: myevents[i].name, start: myevents[i].started_at, end: myevents[i].endeded_at}
        this.calendarOptions.events.push(obj);
    } 
  }
}

</script>


<style lang='css'>
b { /* used for event dates/times */
  margin-right: 3px;
}

.fc { /* the calendar root */
  max-width: 1100px;
  margin: 0 auto;
}

</style>