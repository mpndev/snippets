<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Mail\UserGetRegisteredMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UserGetRegisteredJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $to;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email)
    {
        $this->to = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->to)->send(new UserGetRegisteredMail());
    }
}
