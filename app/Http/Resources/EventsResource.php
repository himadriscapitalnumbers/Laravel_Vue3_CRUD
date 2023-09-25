<?php

namespace App\Http\Resources;

use Config;
use Illuminate\Http\Resources\Json\JsonResource;

class EventsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //return parent::toArray($request);

        $timeZone = $request->header('timeZone');
        $server_timeZone = Config::get('app.timezone');


        $started_at = $this->converTimeZone($this->started_at,$server_timeZone,$timeZone,'Y-m-d H:i:s');
        $ended_at   = $this->converTimeZone($this->ended_at,$server_timeZone,$timeZone,'Y-m-d H:i:s');
        $event_date = $this->converTimeZone($this->started_at,$server_timeZone,$timeZone,'Y-m-d');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'event_date' => $event_date,
            'started_at' => $started_at,
            'ended_at' => $ended_at,
            'started_at_human' => date('F j Y H:i A', strtotime($started_at)),
            'ended_at_human' => date('F j Y H:i A', strtotime($ended_at))
        ];
    }

    public function converTimeZone($date,$from,$to,$format)
    {
        $date = new \DateTime($date, new \DateTimeZone($from));
        $date->setTimezone(new \DateTimeZone($to));
        return $date->format($format);
    }



}
