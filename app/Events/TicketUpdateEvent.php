<?php

namespace App\Events;

use App\Models\Ticket;
use App\Traits\LookupTrait;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketUpdateEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels, LookupTrait;

    public $ticket;
    public $test;

    /**
     * Create a new event instance.
     */
    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    public function handle()
    {
        // Do Something
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('ticketUpdate'),
        ];
    }

    public function broadcastAs(): string
    {
        // Event
        return 'ticket.update';
    }

    public function broadcastWith()
    {

        $tickets = Ticket::select(['id', 'title', 'description', 'created_at', 'updated_at', 'priority', 'status', 'creator', 'assignee'])->with([
            'user:id,name',
            'assignee:id,name'
        ])->where('id', $this->ticket->id)->get();
        $tickets->each(function ($ticket) {
            $ticket->created_at = Carbon::parse($ticket->created_at)->format('Y-m-d H:i:s');
            $ticket->updated_at = Carbon::parse($ticket->updated_at)->format('Y-m-d H:i:s');
            $ticket->priority = $this->getPriority($ticket->priority);
            $ticket->status = $this->getStatus($ticket->status);
            $ticket->link = "/ticket/" . str_pad($ticket->id, 7, 0, STR_PAD_LEFT);
            $ticket->human_readable_created_at = Carbon::parse($ticket->created_at)->diffForHumans();
        });
        return $tickets->toArray();
    }
}
