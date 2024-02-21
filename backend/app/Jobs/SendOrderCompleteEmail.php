<?php

namespace App\Jobs;

use App\Mail\OrderComplete;
use App\Models\Orders\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderCompleteEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected User $user, protected Order $order)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Mail::to($this->user->email)->send(new OrderComplete($this->order));
    }
}
