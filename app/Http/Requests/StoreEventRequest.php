<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;
use Config;

class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:150',
            'description' => 'required|string|max:1000',
            'started_at' => 'required|date|after:today',
            'ended_at' => 'required|date|after:started_at'
        ]
        ;
    }

    public function getData()
    {
        $data = $this->validated();
        
        $timeZone        = $this->header('timeZone');
        $server_timeZone = Config::get('app.timezone');

        $data['started_at'] = $this->converTimeZone($data['started_at'],$timeZone,$server_timeZone);
        $data['ended_at']   = $this->converTimeZone($data['ended_at'],$timeZone,$server_timeZone);
        $data['event_date'] = date('Y-m-d', strtotime($data['started_at']));
        return $data;
    }

    public function converTimeZone($date,$from,$to)
    {
        $date = new \DateTime($date, new \DateTimeZone($from));
        $date->setTimezone(new \DateTimeZone($to));
        return $date->format('Y-m-d H:i:s');
    }



}
