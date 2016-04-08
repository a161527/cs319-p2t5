<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Mail;

class SendUpdateEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $subject, $view, $params, $recipients;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subject, $view, $params, $recipients)
    {
        $this->subject = $subject;
        $this->view = $view;
        $this->params = $params;
        $this->recipients = $recipients;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::send($this->view, $this->params, function($msg) {
            $msg->subject($this->subject);
            foreach($this->recipients as $r) {
                $msg->bcc($r);
            }
        });
    }
}
