<?php
namespace App\Jobs;
use App\Mail\notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;


class sendnotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $user;
    protected $subject;
    protected $description;
    /**
     * Create a new job instance.
     */
    
        public function __construct($user, $subject, $description)
        {
            $this->user = $user;
            $this->subject = $subject;
            $this->description = $description;
        }
    

    /**
     * Execute the job.
     */
    public function handle(): void
    {
      
        Mail::to($this->user)->send(new notification($this->subject, $this->description));
    }
}


