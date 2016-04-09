<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use JWTAuth;
use Mail;
use Log;

class ResetPassword extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $account;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($account)
    {
        $this->account = $account;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $token = JWTAuth::fromUser($this->account);
        Log::info("Generated token: " . $token);
        $link = config('app.url') . "?token={$token}";

        Mail::send('email-reset', ['link' => $link], function($msg) {
            $msg->to($this->account->email);
            $msg->subject("Password Reset");
        });

        JWTAuth::manager()->decode(new \Tymon\JWTAuth\Token($token));

        Log::info("Sent password reset to {$this->account->email}");
    }
}
