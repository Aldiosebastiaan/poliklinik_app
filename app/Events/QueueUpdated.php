<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Broadcasting\ShouldRescue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueUpdated implements ShouldBroadcastNow, ShouldRescue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $poliId;

    protected $antrianNumber;

    protected $status;

    /**
     * Create a new event instance.
     *
     * @param  int  $poliId
     * @param  string  $antrianNumber
     * @param  string  $status
     */
    public function __construct($poliId, $antrianNumber, $status = 'called')
    {
        $this->poliId = $poliId;
        $this->antrianNumber = $antrianNumber;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('queue.'.$this->poliId),
        ];
    }

    /**
     * The data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'poli_id' => $this->poliId,
            'antrian_number' => $this->antrianNumber,
            'status' => $this->status,
            'timestamp' => now()->toISOString(),
        ];
    }
}
