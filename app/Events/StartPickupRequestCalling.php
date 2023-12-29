<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StartPickupRequestCalling implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public $pickup,public $driver)
    { }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {

        return [
            new Channel('pickup_request.'.$this->driver['s_id'])
        ];
    }
    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        //get the client data
        $client =getPickupClientData($this->pickup->client_id);

        return [
            'pickup_request'=>
            [
                    's_id'=>$this->pickup->s_id,
                    'location'=>$this->pickup->location,
                    'destination'=>$this->pickup->destination,
                    'estimated_price'=>$this->pickup->estimated_price,
                    'estimated_duration'=>$this->pickup->estimated_duration,
            ],
            'client'=>
            [
                'first_name'=>$client->first_name,
                'last_name'=>$client->last_name,
                'photo'=>$client->photo,
                'phone_number'=>$client->phone_number
            ]
            ];
    }
}
