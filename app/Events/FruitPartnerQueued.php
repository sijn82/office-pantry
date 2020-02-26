<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FruitPartnerQueued implements ShouldBroadcast
{

    public $fruitpartner;

    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($fruitpartner)
    {
        //
        $this->fruitpartner = $fruitpartner;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        //dd($this->fruitpartner);
         return new Channel('fruitpartner-staging-queue');
        // return new Channel('office-pantry-development');
    }
    
    public function broadcastAs()
    {
        return 'add';
    }
}
