<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;
use App\UserConference;
use App\Flight;

class RegistrationFlightAggregator extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $registrationId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userConferenceId)
    {
        $this->registrationId = $userConferenceId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function() {
            $registration = UserConference::find($this->registrationId);
            $currentFlight = $registration->flight;
            $others =
                Flight::where('flightNumber', $currentFlight->flightNumber)
                ->where('airline', $currentFlight->airline)
                ->where('arrivalDate', $currentFlight->arrivalDate)
                ->where('arrivalTime', $currentFlight->arrivalTime)
                ->where('airport', $currentFlight->airport)
                ->where('id', '<>', $currentFlight->id)
                ->get();
            foreach ($others as $otherFlight) {
                UserConference::where('flightID', $otherFlight->id)
                    ->update(['flightID' => $currentFlight->id]);
                $otherFlight->delete();
            }
        });
    }
}
