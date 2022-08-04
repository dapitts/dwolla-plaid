<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';
    protected $fillable = [
        'eventId', 'topic', 'account', 'resource', 'customer', 'eventCreated'
    ];

    public function add($event) {
        if (!empty($event->id))
            $this->eventId = $event->id;

        if (!empty($event->topic))
            $this->topic = $event->topic;

        if (!empty($event->_links->account->href))
            $this->account = $event->_links->account->href;

        if (!empty($event->_links->resource->href))
            $this->resource = $event->_links->resource->href;

        if (!empty($event->_links->customer->href))
            $this->customer = $event->_links->customer->href;

        if (!empty($event->created))
            $this->eventCreated = \Carbon\Carbon::parse($event->created)->setTimezone('UTC');

        $this->save();

        return $this->id;
    }

    public function doesntExist($eventId) {
        return $this->where('eventId', $eventId)->doesntExist();
    }
}
