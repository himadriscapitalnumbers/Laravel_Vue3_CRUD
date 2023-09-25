<?php

namespace App\Http\Controllers\Api;

use Config;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventsResource;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\ImportEventRequest;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event as CalendarEvent;
use ICal\ICal;


class EventsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(isset($_GET['s']) && $_GET['s'] !='')
        {

            return EventsResource::collection(auth()->user()->events()->where('name','LIKE','%'.$_GET['s'].'%')->orderBy('event_date', 'ASC')->get()); 

        }
        else
        return EventsResource::collection(auth()->user()->events()->orderBy('event_date', 'ASC')->get()); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEventRequest $request)
    {

        $event = $request->user()->events()->create($request->getData());

        return EventsResource::make($event);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        if(auth()->user()->id === $event->user_id)
        {
            return new EventsResource(Event::find($event->id));
        }
        
        return response()->json([], 401); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(StoreEventRequest $request, Event $event)
    {
        if(auth()->user()->id === $event->user_id)
        {
            $event->update($request->getData());

            return EventsResource::collection(Event::where('id',$event->id)->get());
        }

        return response()->json([], 401); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    { 
        if(auth()->user()->id === $event->user_id)
        {
            if($event->delete())
            {
                return response()->json(['deleted' => 1]);
            }
        }
        
        return response()->json([], 401); 
    }

    public function exporttocsv(Request $request)
    {
        ob_start();
        
        /* header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Origin: http://localhost:5173');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=events_export.csv'); */

        $header_args = array( 'Name', 'Description', 'Start Date Time', 'End Date Time' );

        $events = auth()->user()->events()->orderBy('event_date', 'ASC')->get();

        $timeZone = $request->header('timeZone');
        $server_timeZone = Config::get('app.timezone');

        $storeEvent_Obj = new StoreEventRequest();

        foreach($events as $event)
        {
            
            $data[] = [$event->name,
                       $event->description,
                       $storeEvent_Obj->converTimeZone($event->started_at,$server_timeZone,$timeZone),
                       $storeEvent_Obj->converTimeZone($event->ended_at,$server_timeZone,$timeZone),
                      ];
        }

        ob_end_clean();
        $output = fopen( 'php://output', 'w' );
        
        fputcsv( $output, $header_args );

        foreach( $data as $data_item ){
            fputcsv( $output, $data_item );
        }

        fclose( $output );

        return response()->json('')->withHeaders([
                                'Content-Type' => 'text/csv; charset=utf-8',
                                'Content-Disposition' => 'attachment; filename="events_export.csv"',
                            ]);
    }

    public function exporttoics(Request $request)
    {
         
        $calendar = Calendar::create();
        
        $events = auth()->user()->events()->orderBy('event_date', 'ASC')->get();

        $timeZone = $request->header('timeZone');
        $server_timeZone = Config::get('app.timezone');

        foreach($events as $event)
        {
            
            $calendarEvent = CalendarEvent::create()
                            ->name($event->name)
                            ->description($event->description)
                            ->organizer($request->user()->email, $request->user()->name)
                            ->startsAt(new \DateTime($event->started_at, new \DateTimeZone($server_timeZone)))
                            ->endsAt(new \DateTime($event->ended_at, new \DateTimeZone($server_timeZone)));


            $calendar->event($calendarEvent);     
        }
        
        return response($calendar->get(), 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="my-awesome-calendar.ics"',
         ]);

    }

    public function importevents(ImportEventRequest $request)
    {
        $data = $request->getData();
        
        $uid = auth()->id();
        $ext = $request->file->extension();

        $fileName = time().$uid.'.'.$ext;  
       
        $request->file->move(public_path('uploads'), $fileName);

        $timeZone        = $request->header('timeZone');
        $server_timeZone = Config::get('app.timezone');

        if($ext == 'csv')
        $success = $this->importfromCsv($uid,$fileName,$timeZone);
        if($ext == 'ics')
        $success = $this->importfromIcs($uid,$fileName,$timeZone);

        return response()->json(['success' => $success]);
    }

    private function importfromCsv($uid,$filename,$timeZone)
    {
        $server_timeZone = Config::get('app.timezone');

        $filepath = public_path('uploads/'.$filename);

        $csvFile = fopen($filepath, 'r');

        $row = 0;
        $success = 0;

        $storeEvent_Obj = new StoreEventRequest();

        if($csvFile)
        {
        while (($getData = fgetcsv($csvFile, 10000, ",")) !== FALSE)
        {
            if($row >0)
            {
                $eventdata['name']        = $getData[0];
                $eventdata['description'] = $getData[1];
                $eventdata['started_at']  = $storeEvent_Obj->converTimeZone($getData[2],$timeZone,$server_timeZone);
                $eventdata['ended_at']    = $storeEvent_Obj->converTimeZone($getData[3],$timeZone,$server_timeZone);
                $eventdata['event_date']  = date('Y-m-d', strtotime($eventdata['started_at']));

                if($eventdata['ended_at'] > $eventdata['started_at'])
                {
                    auth()->user()->events()->create($eventdata);

                    $success++;
                }
            }
            $row++;
        }
          @unlink($filepath);
        }

        return $success;
        

    }

    private function importfromIcs($uid,$filename,$timeZone)
    {
        $server_timeZone = Config::get('app.timezone');
        $storeEvent_Obj  = new StoreEventRequest();

        $filepath = public_path('uploads/'.$filename);

        $ical   = new ICal($filepath);
        $events = $ical->events();
        $calendarTimeZone = $ical ->calendarTimeZone();

        $success = 0;

        foreach ($events as $event)
        {

            $eventdata['name']        = $event->summary;
            $eventdata['description'] = $event->description;

            $timeZone = 'UTC';
            if(isset($event->dtstart_array[0]['TZID']))
            {
                $timeZone = $event->dtstart_array[0]['TZID'];
            }
            else{

                $timeZone = $calendarTimeZone;
            }

            $eventdata['started_at']  = $storeEvent_Obj->converTimeZone($event->dtstart,$timeZone,$server_timeZone);
            $eventdata['ended_at']    = $storeEvent_Obj->converTimeZone($event->dtend,$timeZone,$server_timeZone);
            $eventdata['event_date']  = date('Y-m-d', strtotime($eventdata['started_at']));

            if($eventdata['ended_at'] > $eventdata['started_at'])
            {
                auth()->user()->events()->create($eventdata);
                $success++;
            }
            
        }
        return $success;

    }

    
}
