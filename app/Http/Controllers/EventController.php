<?php

namespace App\Http\Controllers;

use App\Http\Requests\Event\SotreRequest;
use App\Jobs\AfterEventAdd;
use App\Models\Event;
use App\Services\EventService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;

class EventController extends Controller
{
    protected $service;
    public function __construct (EventService $service)
    {
        $this->service = $service;
    }

    public function store(SotreRequest $request)
    {
        $data = $request->validated();
        $date = Carbon::createFromFormat("Y-m-d", $request->date);
        $periodDatas = $this->service->calculateDate($date);
        $event = new Event ([
            "title" => $data['title'],
            "place" => $data['place'],
            "date" => $data['date'],
            "period" => $periodDatas['period'],
            "period_type" => $periodDatas['period_type']
        ]);
        $event->save();
        $eventsInCache = Cache::get("event_list");
        $eventsInCache[] = $event;
        Cache::put('event_list', $eventsInCache, 5);
        Queue::push(new AfterEventAdd($event));
        return redirect()->route('events.index');
    }

    public function index()
    {
        if(Cache::has("event_list"))
            $events = Cache::get('event_list');
        else
            $events = Event::all()->sortByDesc('id');

        $formattedEvents = [];
        foreach ($events as $event) {
            $date = Carbon::createFromFormat("Y-m-d", $event->date);
            $formattedEvent = [
                'name' => $event->title . ' ' . $event->place,
                'date' => $date->format('d.m.Y'),
                'period' => $event->formatPeriod(),
            ];
            $formattedEvents[] = $formattedEvent;
        }
        return $formattedEvents;
    }
}
